<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderLine;
use App\Services\Commercial\AddressService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

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

    //    public function deleteOrAnonymizeClient(Client $client): string
    //    {
    //        // 1. Purge des commandes de plus de 10 ans
    //        $orderExpirationDate = now()->subYears(self::EXPIRED_ORDER_YEARS);
    //        $ordersToDelete = $client->orders()->where('date_commande', '<', $orderExpirationDate)->get();
    //
    //        foreach ($ordersToDelete as $order) {
    //            \DB::table('evolue')->where('id_commande', $order->id_commande)->delete();
    //            \DB::table('ligne_commande')->where('id_commande', $order->id_commande)->delete();
    //            $order->delete();
    //        }
    //
    //        // 2. On traite les adresses (SoftDelete si liée à une commande, ForceDelete sinon)
    //        foreach ($client->addresses()->withTrashed()->get() as $address) {
    //            $this->deleteOrSoftDelete($address);
    //        }
    //
    //        // 3. ON RECHARGE TOUT
    //        $client->refresh();
    //
    //        // 4. On vérifie s'il reste des commandes (même celles de moins de 10 ans)
    //        if ($client->orders()->exists()) {
    //            // On anonymise car on doit garder les commandes (compta)
    //            $anonymizedData = self::ANONYMIZED_CLIENT_DATA;
    //            $anonymizedData['email_client'] = 'anonyme_'.$client->id_client.'_'.time().'@deleted.local';
    //
    //            $client->update($anonymizedData);
    //            $client->delete(); // Soft Delete du client
    //
    //            return 'Compte anonymisé (commandes conservées).';
    //        }
    //
    //        // 5. CAS CRITIQUE : Suppression totale
    //        // Si on est ici, le client n'a PLUS de commandes du tout.
    //        // On DOIT supprimer physiquement toutes ses adresses (même soft-deleted)
    //        // sinon la FK bloquera le forceDelete du client.
    //        $client->addresses()->withTrashed()->forceDelete();
    //
    //        // Maintenant on peut supprimer le client sans erreur de FK
    //        $client->forceDelete();
    //
    //        return 'Compte supprimé définitivement.';
    //    }

    //    public function deleteOrAnonymizeClient(Client $client): string
    //    {
    //        // 1. Supprimer les commandes de +10 ans (Physiquement pour nettoyer la base)
    //        $orderExpirationDate = now()->subYears(self::EXPIRED_ORDER_YEARS);
    //        $expiredOrderIds = $client->orders()
    //            ->where('date_commande', '<', $orderExpirationDate)
    //            ->pluck('id_commande');
    //
    //        if ($expiredOrderIds->isNotEmpty()) {
    //            \DB::table('evolue')->whereIn('id_commande', $expiredOrderIds)->delete();
    //            \DB::table('ligne_commande')->whereIn('id_commande', $expiredOrderIds)->delete();
    //            \DB::table('commande')->whereIn('id_commande', $expiredOrderIds)->delete();
    //        }
    //
    //        // 2. Traiter les adresses : On nettoie d'abord tout ce qui n'est plus lié
    //        // On utilise withTrashed() pour ne rien oublier
    //        foreach ($client->addresses()->withTrashed()->get() as $address) {
    //            // IMPORTANT : On vérifie si l'adresse est liée à une commande ENCORE EXISTANTE
    //            if ($this->isAddressLinkedToOrder($address)) {
    //                $address->delete(); // On garde en SoftDelete car la commande existe
    //            } else {
    //                // AUCUNE commande ne pointe sur cette adresse : on la tue
    //                $address->forceDelete();
    //            }
    //        }
    //
    //        // 3. ON RAFRAICHIT LE CLIENT ET SES RELATIONS
    //        $client->refresh();
    //
    //        // 4. VERDICT : Est-ce qu'il reste des commandes au client ?
    //        if ($client->orders()->exists()) {
    //            // Il reste des commandes récentes (-10 ans) -> ANONYMISATION
    //            $anonymizedData = self::ANONYMIZED_CLIENT_DATA;
    //            $anonymizedData['email_client'] = 'anonyme_'.$client->id_client.'_'.time().'@deleted.local';
    //
    //            $client->update($anonymizedData);
    //            $client->delete(); // Soft delete
    //
    //            return 'Compte anonymisé (commandes récentes conservées).';
    //        }
    //
    //        // 5. SUPPRESSION TOTALE
    //        // Si on est ici, le client n'a PLUS de commandes.
    //        // On doit quand même s'assurer qu'aucune adresse soft-deleted ne traîne
    //        // car elles bloqueraient la FK du client.
    //        $client->addresses()->withTrashed()->forceDelete();
    //
    //        // Là, le client est totalement libre de toute attache, on peut le supprimer
    //        $client->forceDelete();
    //
    //        return 'Compte supprimé définitivement.';
    //    }

    // jjhtthjthojth

    public function deleteOrAnonymizeClient(Client $client): string
    {
        //        $orderExpirationDate = now()->subYears(GdprService::EXPIRED_ORDER_YEARS);
        //        $client->orders()
        //            ->where('date_commande', '<', $orderExpirationDate)
        //            ->delete();

        $orderExpirationDate = now()->subYears(GdprService::EXPIRED_ORDER_YEARS);
        $orders = $client->orders()
            ->where('date_commande', '<', $orderExpirationDate)
            ->get();

        foreach ($orders as $order) {
            $order->items()->delete();
            $order->states()->delete();
            $order->delete();
        }

        foreach ($client->addresses()->withTrashed()->get() as $address) {
            $this->deleteOrSoftDelete($address);
        }

        $client = $client->refresh();

        if ($client->orders()->exists()) {
            $anonymizedData = self::ANONYMIZED_CLIENT_DATA;
            $anonymizedData['email_client'] = 'anonyme_'.$client->id_client.'_'.time().'@deleted.local';

            $client->update($anonymizedData);
            $client->delete();

            return 'Compte anonymisé. Les commandes de plus de 10 ans ont été purgées. Les récentes sont conservées (conservation légale pour comptabilité).';
        }

        $client->forceDelete();

        return 'Compte supprimé définitivement (aucune donnée récente à conserver).';
    }

    //    public function deleteOrAnonymizeClient(Client $client): string
    //    {
    //        // 1. Purge des commandes expirées (> 10 ans)
    //        $orderExpirationDate = now()->subYears(self::EXPIRED_ORDER_YEARS);
    //        $expiredOrders = $client->orders()->where('date_commande', '<', $orderExpirationDate)->get();
    //
    //        foreach ($expiredOrders as $order) {
    //            \DB::table('evolue')->where('id_commande', $order->id_commande)->delete();
    //            \DB::table('ligne_commande')->where('id_commande', $order->id_commande)->delete();
    //            $order->delete(); // Supprime ou SoftDelete la commande
    //        }
    //
    //        // 2. Traitement des adresses : on boucle proprement
    //        foreach ($client->addresses()->withTrashed()->get() as $address) {
    //            // On vérifie une dernière fois si l'adresse est liée à UNE COMMANDE EXISTANTE en base
    //            if ($this->isAddressLinkedToOrder($address)) {
    //                $address->delete(); // On fait juste un SoftDelete pour garder la cohérence SQL
    //            } else {
    //                $address->forceDelete(); // Personne ne l'utilise, on peut la supprimer
    //            }
    //        }
    //
    //        $client->refresh();
    //
    //        // 3. Vérification des commandes restantes (moins de 10 ans)
    //        if ($client->orders()->exists()) {
    //            // ANONYMISATION (Obligation légale de conservation)
    //            $anonymizedData = self::ANONYMIZED_CLIENT_DATA;
    //            $anonymizedData['email_client'] = 'anonyme_'.$client->id_client.'_'.time().'@deleted.local';
    //
    //            $client->update($anonymizedData);
    //            $client->delete(); // SoftDelete du client
    //
    //            return 'Compte anonymisé (commandes récentes conservées).';
    //        }
    //
    //        // 4. SUPPRESSION TOTALE
    //        // Avant de forceDelete le client, on doit libérer les adresses restantes
    //        // Mais attention : on ne forceDelete QUE celles qui ne sont plus liées
    //        foreach ($client->addresses()->withTrashed()->get() as $address) {
    //            if (! $this->isAddressLinkedToOrder($address)) {
    //                $address->forceDelete();
    //            }
    //        }
    //
    //        // Enfin, on tente de supprimer le client
    //        try {
    //            $client->forceDelete();
    //        } catch (\Exception $e) {
    //            // Si ça crash encore ici, c'est qu'il y a une AUTRE table (pas adresse)
    //            // qui bloque (ex: logs, avis, etc.)
    //            $client->delete();
    //
    //            return 'Suppression complète impossible (FK externe), le compte a été archivé.';
    //        }
    //
    //        return 'Compte supprimé définitivement.';
    //    }

    //    public function deleteOrAnonymizeClient(Client $client): string
    //    {
    //        $orderExpirationDate = now()->subYears(self::EXPIRED_ORDER_YEARS);
    //
    //        // 1. Supprimer les commandes expirées
    //        $orders = $client->orders()
    //            ->where('date_commande', '<', $orderExpirationDate)
    //            ->get();
    //
    //        $orders->each(function (Order $order) {
    //            $order->states()->delete();
    //            $order->items()->delete();
    //            $order->delete();
    //        });
    //
    //        // 2. Recharger l'état réel
    //        $client->refresh();
    //
    //        // 3. Si des commandes existent encore → ANONYMISATION
    //        if ($client->orders()->exists()) {
    //
    //            foreach ($client->addresses as $address) {
    //                //                if ($this->isAddressLinkedToOrder($address)) {
    //                //                    $this->anonymizeAddress($address);
    //                //                } else {
    //                //                    $address->forceDelete();
    //                //                }
    //                $this->deleteOrSoftDelete($address);
    //            }
    //
    //            $anonymizedData = self::ANONYMIZED_CLIENT_DATA;
    //            $anonymizedData['email_client'] =
    //                'anonyme_'.$client->id_client.'_'.time().'@deleted.local';
    //
    //            $client->update($anonymizedData);
    //
    //            // ❗ JAMAIS forceDelete ici
    //            $client->delete();
    //
    //            return 'Compte anonymisé (commandes conservées pour obligation légale).';
    //        }
    //
    //        // 4. Aucun historique → suppression totale
    //        //        $client->addresses()->forceDelete();
    //        //        $this->deleteOrAnonymizeClient($client);
    //
    //        return 'Compte supprimé définitivement.';
    //    }

    public function deleteOrSoftDelete(Address $address): string
    {
        $address = $address->refresh();

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
            ->exists() || Order::where('id_adresse_livraison', $address->id_adresse)->exists();
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
    private function exportClientData(Client $client): array
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

    public function generatePdfExport(Client $client): Response
    {
        $data = $this->exportClientData($client);
        $pdf = Pdf::loadView('pdf.export_gdpr', compact('data'))
            ->setPaper('a4')
            ->setOption([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->download('export_rgpd_'.$client->id_client.'.pdf');
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

        $orders = Order::where('date_commande', '<', $orderExpirationDate)
            ->get();

        $orderCount = $orders->count();

        foreach ($orders as $order) {
            //            \DB::delete('DELETE FROM evolue WHERE id_commande = ?', [$order->id_commande]);
            //            \DB::delete('DELETE FROM ligne_commande WHERE id_commande = ?', [$order->id_commande]);
            $order->states()->delete();
            $order->items()->delete();
            $order->delete();
        }

        return $orderCount;
    }
}
