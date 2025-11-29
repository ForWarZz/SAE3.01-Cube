<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Bike;
use App\Models\BikeFrame;
use App\Models\BikeFrameMaterial;
use App\Models\BikeModel;
use App\Models\BikeReference;
use App\Models\BikeSize;
use App\Models\Characteristic;
use App\Models\Color;
use App\Models\Shop;
use App\Models\Usage;
use App\Models\Vintage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FilterEngineService
{
    public function retrieveSelectedFilters(Request $request): array
    {
        return [
            'millesime' => $request->input('millesime', []),
            'cadre' => $request->input('cadre', []),
            'couleur' => $request->input('couleur', []),
            'usage' => $request->input('usage', []),
            'materiau' => $request->input('materiau', []),
            'modele_velo' => $request->input('modele_velo', []),
            'disponibilite' => $request->input('disponibilite', []),
            'promotion' => $request->input('promotion', []),
//            'prix_max' => $request->input('prix_max'),
//            'poids_max' => $request->input('poids_max'),
        ];
    }

    public function apply($query, array $selectedFilters): Builder
    {
        if (!empty($selectedFilters['millesime'])) {
            $millesimes = (array) $selectedFilters['millesime'];
            $query->whereHas('bike', fn($q) => $q->whereIn('id_millesime', $millesimes));
        }

        if (!empty($selectedFilters['cadre'])) {
            $cadres = (array) $selectedFilters['cadre'];
            $query->whereHas('bike.references', fn($q) => $q->whereIn('id_cadre_velo', $cadres));
        }

        if (!empty($selectedFilters['couleur'])) {
            $couleurs = (array) $selectedFilters['couleur'];
            $query->whereHas('bike.references', fn($q) => $q->whereIn('id_couleur', $couleurs));
        }

        if (!empty($selectedFilters['usage'])) {
            $usages = (array) $selectedFilters['usage'];
            $query->whereHas('bike', fn($q) => $q->whereIn('id_usage', $usages));
        }

        if (!empty($selectedFilters['materiau'])) {
            $materiaux = (array) $selectedFilters['materiau'];
            $query->whereHas('bike', fn($q) => $q->whereIn('id_materiau_cadre', $materiaux));
        }

        if (!empty($selectedFilters['modele'])) {
            $modeles = (array) $selectedFilters['modele'];
            $query->whereHas('bike', fn($q) => $q->whereIn('id_modele_velo', $modeles));
        }

        if (!empty($selectedFilters['promotion'])) {
            if (in_array('on_promotion', $selectedFilters['promotion'])) {
                $query->where('pourcentage_remise', '>', 0);
            }

            if (in_array('no_promotion', $selectedFilters['promotion'])) {
                $query->where('pourcentage_remise', '=', 0);
            }
        }

        if (!empty($selectedFilters['disponibilite'])) {
            if (in_array('online', $selectedFilters['disponibilite'])) {
                $query->whereHas('bike.references.availableSizes',
                    fn($q) => $q->where('dispo_en_ligne', true));
            }

            if (in_array('in_stock', $selectedFilters['disponibilite'])) {
                $query->whereHas('bike.references.shopAvailabilities',
                    fn($q) => $q->where('statut', 'En stock'));
            }

            if (in_array('orderable', $selectedFilters['disponibilite'])) {
                $query->whereHas('bike.references.shopAvailabilities',
                    fn($q) => $q->where('statut', 'Commandable'));
            }
        }
//
//        if (!empty($selectedFilters['prix_max'])) {
//            $query->where('prix_article', '<=', $selectedFilters['prix_max']);
//        }
//
//        if (!empty($selectedFilters['poids_max'])) {
//            $poidsCharacteristic = Characteristic::where('nom_caracteristique', 'Poids du vélo')->first();
//            if ($poidsCharacteristic) {
//                $query->whereExists(function($sub) use ($poidsCharacteristic, $selectedFilters) {
//                    $sub->selectRaw('1')
//                        ->from('caracterise')
//                        ->whereColumn('caracterise.id_article', 'article.id_article')
//                        ->where('id_caracteristique', $poidsCharacteristic->id_caracteristique)
//                        ->whereRaw("CAST(REPLACE(REGEXP_REPLACE(valeur_caracteristique, '[^0-9,.]', '', 'g'), ',', '.') AS DECIMAL) <= ?", [$selectedFilters['poids_max']]);
//                });
//            }
//        }

        return $query;
    }

    public function getFilterOptions($query): array
    {
        $articleIds = $query->pluck('id_article');
        $format = fn($collection, $idField, $labelField) => $collection->map(fn($item) => [
            'id' => (string)$item->$idField,
            'label' => $item->$labelField,
        ]);

        $hasPromotions = (clone $query)->where('pourcentage_remise', '>', 0)->exists();

        $promotions = collect();
        if ($hasPromotions) {
            $promotions->push([
                'id' => 'on_promotion',
                'label' => 'En promotion'
            ]);

            $promotions->push([
                'id' => 'no_promotion',
                'label' => 'Sans promotion'
            ]);
        }

        $availabilities = collect();

        $hasStock = (clone $query)->whereHas('bike.references.shopAvailabilities', function ($q) {
            $q->where('statut', 'En Stock');
        })->exists();

        if ($hasStock) {
            $availabilities->push(['id' => 'in_stock', 'label' => 'En stock en magasin']);
        }

        $isOrderable = (clone $query)->whereHas('bike.references.shopAvailabilities', function ($q) {
            $q->where('statut', 'Commandable');
        })->exists();

        if ($isOrderable) {
            $availabilities->push(['id' => 'orderable', 'label' => 'Commandable en magasin']);
        }

        $isWeb = (clone $query)->whereHas('bike.references.availableSizes', function ($q) {
            $q->where('dispo_en_ligne', true);
        })->exists();

        if ($isWeb) {
            $availabilities->push(['id' => 'online', 'label' => 'Disponible en ligne']);
        }

        return [
            'vintages' => $format(
                Vintage::whereIn('id_millesime',
                    Bike::whereIn('id_article', $articleIds)
                        ->distinct()
                        ->pluck('id_millesime'))
                    ->get(),
                'id_millesime',
                'millesime_velo'
            ),
            'frames' => $format(
                BikeFrame::whereIn('id_cadre_velo',
                    BikeReference::whereIn('id_article', $articleIds)
                        ->distinct()
                        ->pluck('id_cadre_velo'))
                    ->get(),
                'id_cadre_velo',
                'label_cadre_velo'
            ),
            'materials' => $format(
                BikeFrameMaterial::whereIn('id_materiau_cadre',
                    Bike::whereIn('id_article', $articleIds)
                        ->distinct()
                        ->pluck('id_materiau_cadre'))
                    ->get(),
                'id_materiau_cadre',
                'label_materiau_cadre'
            ),
            'colors' => $format(
                Color::whereIn('id_couleur',
                    BikeReference::whereIn('id_article', $articleIds)
                        ->distinct()
                        ->pluck('id_couleur'))
                    ->get(),
                'id_couleur',
                'label_couleur'
            ),
            'usages' => $format(
                Usage::whereIn('id_usage',
                    Bike::whereIn('id_article', $articleIds)
                        ->distinct()
                        ->pluck('id_usage'))
                    ->get(),
                'id_usage',
                'label_usage'
            ),
            'bikeModels' => $format(
                BikeModel::whereIn('id_modele_velo',
                    Bike::whereIn('id_article', $articleIds)
                        ->distinct()
                        ->pluck('id_modele_velo'))
                    ->get(),
                'id_modele_velo',
                'nom_modele_velo'
            ),
            'promotions' => $promotions,
            'availabilities' => $availabilities
        ];
    }
}
