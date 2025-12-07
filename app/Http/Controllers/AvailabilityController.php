<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\ShopAvailability;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function show(Request $request, $referenceId)
    {

        // On a besoin de l'associations taille et id_reference pour récupérer les disponibilités
        $sizeId = $request->query('size');
        $shops = Shop::all();

        // Récupérer TOUTES les disponibilités pour afficher toutes les tailles
        $allAvailabilities = ShopAvailability::where('id_reference', $referenceId)
            ->with(['size', 'shop'])
            ->get();

        $shopAvailabilities = [];
        foreach ($shops as $shop) {
            $shopStock = $allAvailabilities->where('id_magasin', $shop->id_magasin);

            // Si un filtre de taille est appliqué, on l'utilise pour calculer le statut global
            if ($sizeId) {
                $filteredStock = $shopStock->where('id_taille', $sizeId);
                $hasInStock = $filteredStock->where('statut', ShopAvailability::STATUS_IN_STOCK)->count() > 0;
                $hasOrderable = $filteredStock->where('statut', ShopAvailability::STATUS_ORDERABLE)->count() > 0;
            } else {
                $hasInStock = $shopStock->where('statut', ShopAvailability::STATUS_IN_STOCK)->count() > 0;
                $hasOrderable = $shopStock->where('statut', ShopAvailability::STATUS_ORDERABLE)->count() > 0;
            }

            $globalStatus = 'unavailable';
            if ($hasInStock) {
                $globalStatus = 'in_stock';
            } elseif ($hasOrderable) {
                $globalStatus = 'orderable';
            }

            $shopAvailabilities[] = [
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
                'sizes' => $shopStock->sortBy(function ($item) {
                    // Tri par nom de taille
                    return $item->size->nom_taille;
                })->map(function ($item) {
                    return [
                        'size_id' => $item->id_taille,
                        'size_name' => $item->size->nom_taille,
                        'status' => $item->statut,
                    ];
                })->values(),
            ];
        }
        // Trie par logique : in_stock > orderable > unavailable
        usort($shopAvailabilities, function ($a, $b) {
            $order = ['in_stock' => 0, 'orderable' => 1, 'unavailable' => 2];

            return ($order[$a['status']] ?? 3) <=> ($order[$b['status']] ?? 3);
        });

        return response()->json([
            'success' => true,
            'reference_id' => $referenceId,
            'size_filter' => $sizeId,
            'availabilities' => $shopAvailabilities,
        ]);
    }
}
