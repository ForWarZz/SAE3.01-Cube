<?php

namespace App\Services;

use App\Models\Shop;
use App\Models\ShopAvailability;

class AvailabilityService
{
    public function getAvailabilities(int $referenceId, ?int $sizeFilter = null): array
    {
        $shops = Shop::all();

        // Charge toutes les disponibilités du modèle
        $allAvailabilities = ShopAvailability::where('id_reference', $referenceId)
            ->with(['size', 'shop'])
            ->get();

        $results = [];

        foreach ($shops as $shop) {
            $shopStock = $allAvailabilities->where('id_magasin', $shop->id_magasin);

            // Gestion du filtre taille
            if ($sizeFilter) {
                $filteredStock = $shopStock->where('id_taille', $sizeFilter);
                $hasInStock = $filteredStock->where('statut', ShopAvailability::STATUS_IN_STOCK)->isNotEmpty();
                $hasOrderable = $filteredStock->where('statut', ShopAvailability::STATUS_ORDERABLE)->isNotEmpty();
            } else {
                $hasInStock = $shopStock->where('statut', ShopAvailability::STATUS_IN_STOCK)->isNotEmpty();
                $hasOrderable = $shopStock->where('statut', ShopAvailability::STATUS_ORDERABLE)->isNotEmpty();
            }

            // Déduction du statut global
            $globalStatus = $hasInStock ? 'in_stock' : ($hasOrderable ? 'orderable' : 'unavailable');

            // Organisation des tailles
            //            $sizes = $shopStock->sortBy(fn ($item) => $item->size->nom_taille)
            //                ->map(function ($item) {
            //                    return [
            //                        'size_id' => $item->id_taille,
            //                        'size_name' => $item->size->nom_taille,
            //                        'status' => $item->statut,
            //                    ];
            //                })->values();

            // Construction du bloc shop
            $results[] = [
                'shop' => [
                    'id' => $shop->id_magasin,
                    'name' => $shop->nom_magasin,
                    'address' => trim($shop->num_voie_magasin.' '.$shop->rue_magasin),
                    'complement' => $shop->complement_magasin,
                    'lat' => $shop->latitude ? (float) $shop->latitude : null,
                    'lng' => $shop->longitude ? (float) $shop->longitude : null,
                    'isOpen' => true,
                    'hours' => '09:00 - 19:00',
                ],
                'status' => $globalStatus,
                //                'sizes' => $sizes,
            ];
        }

        // Tri final : in_stock > orderable > unavailable
        usort($results, function ($a, $b) {
            $order = ['in_stock' => 0, 'orderable' => 1, 'unavailable' => 2];

            return ($order[$a['status']] ?? 3) <=> ($order[$b['status']] ?? 3);
        });

        return $results;
    }
}
