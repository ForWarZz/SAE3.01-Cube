<?php

namespace App\Services;

use App\Models\Bike;
use App\Models\BikeFrame;
use App\Models\BikeFrameMaterial;
use App\Models\BikeReference;
use App\Models\Characteristic;
use App\Models\Color;
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
            'prix_max' => $request->input('prix_max'),
            'poids_max' => $request->input('poids_max'),
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

        if (!empty($selectedFilters['prix_max'])) {
            $query->where('prix_article', '<=', $selectedFilters['prix_max']);
        }

        if (!empty($selectedFilters['poids_max'])) {
            $poidsCharacteristic = Characteristic::where('nom_caracteristique', 'Poids du vélo')->first();
            if ($poidsCharacteristic) {
                $query->whereExists(function($sub) use ($poidsCharacteristic, $selectedFilters) {
                    $sub->selectRaw('1')
                        ->from('caracterise')
                        ->whereColumn('caracterise.id_article', 'article.id_article')
                        ->where('id_caracteristique', $poidsCharacteristic->id_caracteristique)
                        ->whereRaw("CAST(REPLACE(REGEXP_REPLACE(valeur_caracteristique, '[^0-9,.]', '', 'g'), ',', '.') AS DECIMAL) <= ?", [$selectedFilters['poids_max']]);
                });
            }
        }

        return $query;
    }

    public function getFilterOptions($query): array
    {
        $articleIds = $query->pluck('id_article');
        $format = fn($collection, $idField, $labelField) => $collection->map(fn($item) => [
            'id' => (string)$item->$idField,
            'label' => $item->$labelField,
        ]);

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
        ];
    }
}
