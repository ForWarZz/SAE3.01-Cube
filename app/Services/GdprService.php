<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderLine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GdprService
{
    private const ANONYMIZED_ADDRESS_DATA = [
        'alias_adresse' => 'Adresse supprimée',
        'nom_adresse' => 'ANONYME',
        'prenom_adresse' => 'Utilisateur',
        'num_voie_adresse' => '0',
        'rue_adresse' => 'Données supprimées',
        'complement_adresse' => null,
        'telephone_adresse' => '0000000000',
        'tel_mobile_adresse' => null,
        'societe_adresse' => null,
        'tva_adresse' => null,
    ];

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

    public function isAddressLinkedToOrder(Address $adresse): bool
    {
        return Order::where('id_adresse_facturation', $adresse->id_adresse)
            ->orWhere('id_adresse_livraison', $adresse->id_adresse)
            ->exists();
    }

    public function clientHasOrders(Client $client): bool
    {
        return $client->orders()->exists();
    }

    /**
     * @return array{deleted: bool, anonymized: bool, message: string}
     *
     * @throws \Exception
     */
    public function deleteOrAnonymizeAddress(Address $adresse): array
    {
        if ($this->isAddressLinkedToOrder($adresse)) {
            return $this->anonymizeAddress($adresse);
        }

        return $this->deleteAddress($adresse);
    }

    /**
     * @return array{deleted: bool, anonymized: bool, message: string}
     *
     * @throws \Exception
     */
    public function anonymizeAddress(Address $adresse): array
    {
        try {
            $adresse->update(self::ANONYMIZED_ADDRESS_DATA);
            $adresse->delete(); // Soft delete

            Log::info('RGPD: Adresse anonymisée', [
                'id_adresse' => $adresse->id_adresse,
                'id_client' => $adresse->id_client,
            ]);

            return [
                'deleted' => false,
                'anonymized' => true,
                'message' => 'L\'adresse a été anonymisée car elle est liée à une commande (conservation légale).',
            ];
        } catch (\Exception $e) {
            Log::error('RGPD: Erreur lors de l\'anonymisation de l\'adresse', [
                'id_adresse' => $adresse->id_adresse,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * @return array{deleted: bool, anonymized: bool, message: string}
     *
     * @throws \Exception
     */
    public function deleteAddress(Address $adresse): array
    {
        try {
            $addressId = $adresse->id_adresse;
            $clientId = $adresse->id_client;

            $adresse->forceDelete();

            Log::info('RGPD: Adresse supprimée', [
                'id_adresse' => $addressId,
                'id_client' => $clientId,
            ]);

            return [
                'deleted' => true,
                'anonymized' => false,
                'message' => 'L\'adresse a été supprimée avec succès.',
            ];
        } catch (\Exception $e) {
            Log::error('RGPD: Erreur lors de la suppression de l\'adresse', [
                'id_adresse' => $adresse->id_adresse,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * @return array{deleted: bool, anonymized: bool, message: string}
     *
     * @throws \Throwable
     */
    public function deleteOrAnonymizeClient(Client $client): array
    {
        return DB::transaction(function () use ($client) {
            if ($this->clientHasOrders($client)) {
                return $this->anonymizeClient($client);
            }

            return $this->deleteClient($client);
        });
    }

    /**
     * @return array{deleted: bool, anonymized: bool, message: string}
     *
     * @throws \Exception
     */
    public function anonymizeClient(Client $client): array
    {
        try {
            // Anonymiser toutes les adresses du client
            foreach ($client->addresses as $adresse) {
                $this->anonymizeAddress($adresse);
            }

            // Générer un email anonyme unique
            $anonymizedData = self::ANONYMIZED_CLIENT_DATA;
            $anonymizedData['email_client'] = 'anonyme_'.$client->id_client.'_'.time().'@deleted.local';

            // Anonymiser le client
            $client->update($anonymizedData);

            Log::info('RGPD: Client anonymisé', [
                'id_client' => $client->id_client,
            ]);

            return [
                'deleted' => false,
                'anonymized' => true,
                'message' => 'Votre compte a été anonymisé. Vos données personnelles ont été supprimées mais l\'historique des commandes est conservé pour des raisons légales.',
            ];
        } catch (\Exception $e) {
            Log::error('RGPD: Erreur lors de l\'anonymisation du client', [
                'id_client' => $client->id_client,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * @return array{deleted: bool, anonymized: bool, message: string}
     *
     * @throws \Exception
     */
    public function deleteClient(Client $client): array
    {
        try {
            $clientId = $client->id_client;

            // Supprimer toutes les adresses
            $client->addresses()->delete();

            // Supprimer le client
            $client->delete();

            Log::info('RGPD: Client supprimé', [
                'id_client' => $clientId,
            ]);

            return [
                'deleted' => true,
                'anonymized' => false,
                'message' => 'Votre compte et toutes vos données ont été supprimés conformément au RGPD.',
            ];
        } catch (\Exception $e) {
            Log::error('RGPD: Erreur lors de la suppression du client', [
                'id_client' => $client->id_client,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
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
                    'articles' => $order->items->map(function (OrderLine $item) {
                        $article = $item->reference->bikeReference->article ?? $item->reference->accessory->article;

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
}
