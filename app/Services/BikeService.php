<?php

namespace App\Services;

use App\Models\BikeModel;
use App\Models\BikeReference;
use App\Models\Bike;
use App\Models\Article;
use Illuminate\Support\Collection;
use function PHPUnit\Framework\isNull;

class BikeService
{
    /**
     * Prepare view data for bike detail page
     *
     * @param BikeReference $currentReference
     * @return array{
     *     currentReference: BikeReference,
     *     bike: Bike,
     *     isEbike: bool,
     *     frameOptions: Collection,
     *     colorOptions: Collection,
     *     batteryOptions: Collection,
     *     sizeOptions: Collection,
     *     geometries: Collection,
     *     geometrySizes: Collection,
     *     characteristics: Collection,
     *     weight: string
     *     similarBikes: Collection,
     *     compatibleAccessories: Collection
     *
     * }
     */
    public function prepareViewData(BikeReference $currentReference): array
    {
        $currentReference->load([
            'bike.bikeModel.geometries.characteristic',
            'bike.bikeModel.geometries.size',
            'article.characteristics.characteristicType',
            'ebike.battery',
            'color',
            'frame',
            'availableSizes'
        ]);

        $bike = $currentReference->bike;
        $isEbike = $currentReference->ebike !== null;

        $variants = BikeReference::where('id_article', $bike->id_article)
            ->with(['color', 'frame', 'ebike.battery'])
            ->get();

        $frameOptions = $this->buildFrameOptions($variants, $currentReference);
        $colorOptions = $this->buildColorOptions($variants, $currentReference);

        $batteryOptions = collect();

        if ($isEbike) {
            $batteryOptions = $this->buildBatteryOptions($variants, $currentReference);
        }

        $geometryData = $this->buildGeometryData($bike->bikeModel);
        $sizeOptions = $this->buildSizeOptions($currentReference, $geometryData['headers']);

        $characteristicsGrouped = $bike->article->characteristics->groupBy('characteristicType.nom_type_carac');
        $weight = $bike->article->characteristics
            ->firstWhere('characteristicType.nom_type_carac', '=', 'Poids')
            ->pivot->valeur_caracteristique;


        $similarBikes = $bike->article->similar()
            ->whereHas('bike')  
            ->with('bike')     
            ->limit(4) //Limiter à 4 vélos similaires
            ->get();

        $compatibleAccessories = $this->getCompatibleAccessories($bike);


        return [
            'currentReference' => $currentReference,
            'bike' => $bike,
            'isEbike' => $isEbike,
            'frameOptions' => $frameOptions,
            'colorOptions' => $colorOptions,
            'batteryOptions' => $batteryOptions,
            'sizeOptions' => $sizeOptions,
            'geometries' => $geometryData['rows'],
            'geometrySizes' => $geometryData['headers'],
            'characteristics' => $characteristicsGrouped,
            'weight' => $weight,
            'similarBikes' => $similarBikes,
            'compatibleAccessories' => $compatibleAccessories
        ];
    }


    private function getCompatibleAccessories(Bike $bike): Collection
    {
        
        $accessories = Article::query()
            ->whereHas('accessories') 
            ->where('id_article', '!=', $bike->id_article) 
            ->with(['category', 'accessories'])
            ->limit(8)
            ->get();

        return $accessories;
    }
    /**
     * Build geometry data for bike model
     *
     * @param BikeModel $bikeModel
     * @return array{headers: Collection, rows: Collection}
     */
    private function buildGeometryData(BikeModel $bikeModel): array
    {
        $geometries = $bikeModel->geometries;
        $headers = $geometries->pluck('size')
            ->unique('id_taille')
            ->sortBy('id_taille')
            ->values();

        $rows = $geometries->groupBy('characteristic.label_carac_geo')
            ->map(function ($group, $label) use ($headers) {
                $values = $headers->map(function ($size) use ($group) {
                    $geo = $group->firstWhere('id_taille', $size->id_taille);
                    return $geo ? $geo->valeur_carac : '-';
                });

                return [
                    'label'  => $label,
                    'values' => $values
                ];
            });

        return ['headers' => $headers, 'rows' => $rows];
    }

    /**
     * Build frame options for current reference
     *
     * @param Collection $variants
     * @param BikeReference $currentReference
     * @return Collection
     */
    private function buildFrameOptions(Collection $variants, BikeReference $currentReference): Collection
    {
        return $variants
            ->pluck('frame')
            ->unique('id_cadre_velo')
            ->sortBy('label_cadre_velo')
            ->map(function ($item) use ($variants, $currentReference) {
                $target = $variants->first(function ($ref) use ($item, $currentReference) {
                    if ($ref->id_cadre_velo != $item->id_cadre_velo) return false;
                    if ($ref->id_couleur != $currentReference->id_couleur) return false;

                    return true;
                });

                if (isNull($target)) {
                    $target = $variants->firstWhere('id_cadre_velo', '=', $item->id_cadre_velo);
                }

                return [
                    'label' => $item->label_cadre_velo,
                    'url' => route('articles.bikes.show', $target->id_reference),
                    'active' => $currentReference->id_cadre_velo == $item->id_cadre_velo,
                ];
            });
    }

    /**
     * Build color options for current reference
     *
     * @param Collection $variants
     * @param BikeReference $currentReference
     * @return Collection
     */
    private function buildColorOptions(Collection $variants, BikeReference $currentReference): Collection
    {
        return $variants
            ->pluck('color')
            ->unique('id_couleur')
            ->sortBy('label_couleur')
            ->map(function ($item) use ($variants, $currentReference) {
                $target = $variants->first(function ($ref) use ($item, $currentReference) {
                    if ($ref->id_couleur != $item->id_couleur) return false;
                    if ($ref->id_cadre_velo != $currentReference->id_cadre_velo) return false;

                    return true;
                });

                if (isNull($target)) {
                    $target = $variants->firstWhere('id_couleur', '=', $item->id_couleur);
                }

                return [
                    'label' => $item->label_couleur,
                    'url' => route('articles.bikes.show', $target->id_reference),
                    'active' => $currentReference->id_couleur == $item->id_couleur,
                ];
            });
    }

    /**
     * Build battery options for ebikes
     *
     * @param Collection $variants
     * @param BikeReference $currentReference
     * @return Collection
     */
    private function buildBatteryOptions(Collection $variants, BikeReference $currentReference): Collection
    {
        $batteries = $variants->map(fn($ref) => $ref->ebike?->battery)
            ->filter()
            ->unique('id_batterie')
            ->sortBy('capacite_batterie');

        return $batteries->map(function ($battery) use ($variants, $currentReference) {
            $target = $variants->first(function ($ref) use ($battery, $currentReference) {
                if ($ref->ebike?->id_batterie != $battery->id_batterie) return false;
                if ($ref->id_couleur != $currentReference->id_couleur) return false;
                if ($ref->id_cadre_velo != $currentReference->id_cadre_velo) return false;

                return true;
            });

            if (!$target) {
                $target = $variants->first(fn($r) => $r->ebike?->id_batterie == $battery->id_batterie);
            }

            return [
                'label' => $battery->capacite_batterie . ' Wh',
                'url' => route('articles.bikes.show', $target->id_reference),
                'active' => $currentReference->ebike->id_batterie == $battery->id_batterie
            ];
        });
    }

    /**
     * Build size options for current reference
     *
     * @param BikeReference $currentReference
     * @param Collection|null $geometrySizes
     * @return Collection
     */
    private function buildSizeOptions(BikeReference $currentReference, ?Collection $geometrySizes): Collection
    {
        $sizeList = ($geometrySizes && $geometrySizes->isNotEmpty())
            ? $geometrySizes
            : $currentReference->availableSizes;

        if (!$sizeList) return collect();

        return $sizeList->map(function ($size) use ($currentReference) {
            $availableRef = $currentReference->availableSizes->firstWhere('id_taille', $size->id_taille);
            $inStock = $availableRef && $availableRef->pivot->dispo_en_ligne;

            return [
                'id' => $size->id_taille,
                'label' => $size->nom_taille,
                'disabled' => !$inStock
            ];
        });
    }
}
