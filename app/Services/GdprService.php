<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderLine;
use App\Services\Commercial\AddressService;

class GdprService
{
    public const EXPIRED_ORDER_YEARS = 10;

    private const ANONYMIZED_CLIENT_DATA = [
        'nom_client' => 'ANONYME',
        'prenom_client' => 'Utilisateur',
        'email_client' => null,
        'civilite' => 'Monsieur',
        'naissance_client' => '1900-01-01',
        'hash_mdp_client' => null,
        'google_id' => null,
        'stripe_id' => null,
    ];

    public function __construct(
        private readonly AddressService $addressService,
    ) {}

    public function deleteOrAnonymizeClient(Client $client): string
    {
        $orderExpirationDate = now()->subYears(GdprService::EXPIRED_ORDER_YEARS);
        $client->orders()
            ->where('date_commande', '<', $orderExpirationDate)
            ->delete();

        if ($client->orders()->exists()) {
            foreach ($client->addresses as $address) {
                $this->deleteOrSoftDelete($address);
            }

            $anonymizedData = self::ANONYMIZED_CLIENT_DATA;
            $anonymizedData['email_client'] = 'anonyme_'.$client->id_client.'_'.time().'@deleted.local';

            $client->update($anonymizedData);
            $client->delete();

            return 'Compte anonymisé. Les commandes de plus de 10 ans ont été purgées. Les récentes sont conservées (conservation légale pour comptabilité).';
        }

        $client->addresses()->forceDelete();
        $client->forceDelete();

        return 'Compte supprimé définitivement (aucune donnée récente à conserver).';
    }

    public function deleteOrSoftDelete(Address $address): string
    {
        if ($this->isAddressLinkedToOrder($address)) {
            $address->delete();

            return 'L\'adresse a été archivée car elle est liée à une commande (conservation légale pour comptabilité).';
        }

        $address->forceDelete();

        return 'L\'adresse a été supprimée avec succès.';
    }

    public function isAddressLinkedToOrder(Address $address): bool
    {
        return Order::where('id_adresse_facturation', $address->id_adresse)
            ->orWhere('id_adresse_livraison', $address->id_adresse)
            ->exists();
    }

    public function updateOrReplaceAddress(Address $address, array $validatedData): Address
    {
        if ($this->isAddressLinkedToOrder($address)) {
            $newAddress = $this->addressService->createAddress($address->id_client, $validatedData);
            $address->delete();

            return $newAddress;
        }

        $address->update($validatedData);

        return $address->fresh();
    }

    /**
     * @return array<string, mixed>
     */
    public function exportClientData(Client $client): array
    {
        return [
            'client' => [
                'id' => $client->id_client,
                'civilite' => $client->civilite,
                'nom' => $client->nom_client,
                'prenom' => $client->prenom_client,
                'email' => $client->email_client,
                'date_naissance' => $client->naissance_client?->format('Y-m-d'),
                'derniere_connexion' => $client->date_der_connexion?->format('Y-m-d H:i:s'),
            ],
            'adresses' => $client->addresses->map(function ($adresse) {
                return [
                    'id' => $adresse->id_adresse,
                    'alias' => $adresse->alias_adresse,
                    'nom' => $adresse->nom_adresse,
                    'prenom' => $adresse->prenom_adresse,
                    'telephone' => $adresse->telephone_adresse,
                    'telephone_mobile' => $adresse->tel_mobile_adresse,
                    'societe' => $adresse->societe_adresse,
                    'tva' => $adresse->tva_adresse,
                    'numero_voie' => $adresse->num_voie_adresse,
                    'rue' => $adresse->rue_adresse,
                    'complement' => $adresse->complement_adresse,
                    'ville' => $adresse->city->nom_ville,
                    'code_postal' => $adresse->city->cp_ville,
                ];
            })->toArray(),
            'commandes' => $client->orders->map(function (Order $order) {
                return [
                    'numero' => $order->num_commande,
                    'date' => $order->date_commande,
                    'montant_livraison' => $order->frais_livraison,
                    'pourcentage_remise' => $order->pourcentage_remise,
                    'moyen_paiement' => $order->paymentType->label_type_paiement,
                    'last4_cb' => $order->cb_last4,
                    'adresse_facturation' => $order->billingAddress->toArray(),
                    'adresse_livraison' => $order->deliveryAddress->toArray(),
                    'mode_livraison' => $order->shippingMode->label_moyen_livraison,
                    'articles' => $order->items->map(function (OrderLine $item) {
                        $article = $item->reference->article;

                        return [
                            'produit' => $article->nom_article,
                            'quantite' => $item->quantite_ligne,
                            'prix_unitaire' => $item->prix_unit_ligne,
                        ];
                    })->toArray(),
                ];
            })->toArray(),
            'export_date' => now()->format('Y-m-d H:i:s'),
        ];
    }

    public function anonymizeClientsBeforeDate($beforeDate): int
    {
        $clients = Client::where('date_der_connexion', '<', $beforeDate)->get();
        $anonymizedCount = 0;

        foreach ($clients as $client) {
            $this->deleteOrAnonymizeClient($client);
            $anonymizedCount++;
        }

        return $anonymizedCount;
    }

    public function deleteExpiredOrders(): int
    {
        $orderExpirationDate = now()->subYears(GdprService::EXPIRED_ORDER_YEARS);

        return Order::where('date_commande', '<', $orderExpirationDate)
            ->delete();
    }
}
