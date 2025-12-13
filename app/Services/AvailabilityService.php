<?php

namespace App\Services;

use App\Models\Shop;
use App\Models\ShopAvailability;

class AvailabilityService
{
    public function getAvailabilities(int $referenceId, ?int $sizeFilter = null): array
    {
        $shops = Shop::with('city')->get();

        $allAvailabilities = ShopAvailability::where('id_reference', $referenceId)
            ->with(['size', 'shop'])
            ->get();

        $results = [];

        foreach ($shops as $shop) {
            $shopStock = $allAvailabilities->where('id_magasin', $shop->id_magasin);

            if ($sizeFilter) {
                $filteredStock = $shopStock->where('id_taille', $sizeFilter);
                $hasInStock = $filteredStock->where('statut', ShopAvailability::STATUS_IN_STOCK)->isNotEmpty();
                $hasOrderable = $filteredStock->where('statut', ShopAvailability::STATUS_ORDERABLE)->isNotEmpty();
            } else {
                $hasInStock = $shopStock->where('statut', ShopAvailability::STATUS_IN_STOCK)->isNotEmpty();
                $hasOrderable = $shopStock->where('statut', ShopAvailability::STATUS_ORDERABLE)->isNotEmpty();
            }

            $globalStatus = $hasInStock ? 'in_stock' : ($hasOrderable ? 'orderable' : 'unavailable');

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
                    'city' => $shop->city ? trim($shop->city->nom_ville) : null,
                    'postalCode' => $shop->city ? trim($shop->city->cp_ville) : null,
                    'country' => $shop->city ? $shop->city->pays_ville : null,
                ],
                'status' => $globalStatus,

            ];
        }

        usort($results, function ($a, $b) {
            $order = ['in_stock' => 0, 'orderable' => 1, 'unavailable' => 2];

            return ($order[$a['status']] ?? 3) <=> ($order[$b['status']] ?? 3);
        });

        return $results;
    }
}
