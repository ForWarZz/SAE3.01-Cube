<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Bike;
use App\Models\BikeModel;
use App\Models\BikeReference;
use Illuminate\Support\Collection;

class BikeService
{
    public function __construct(
        protected BikeVariantService $bikeVariantService,
    ) {}

    public function prepareBikeData(BikeReference $currentReference): array
    {
        $currentReference->load([
            'bike.bikeModel.geometries.characteristic',
            'bike.bikeModel.geometries.size',
            'bike.characteristics.characteristicType',
            'ebike.battery',
            'color',
            'frame',
            'availableSizes',
        ]);

        $bike = $currentReference->bike;

        $variants = $this->bikeVariantService->getVariants($currentReference);
        $geometryData = $this->buildGeometryData($bike->bikeModel);

        $weight = $bike->characteristics
            ->firstWhere('id_caracteristique', config('bike.characteristics.weight'))
            ?->pivot->valeur_caracteristique ?? null;

        return [
            'isBike' => true,
            'currentReference' => $currentReference,
            'bike' => $bike,

            'frameOptions' => $this->bikeVariantService->buildFrameOptions($variants, $currentReference),
            'colorOptions' => $this->bikeVariantService->buildColorOptions($variants, $currentReference),
            'batteryOptions' => $this->bikeVariantService->buildBatteryOptions($variants, $currentReference),

            'geometries' => $geometryData['rows'],
            'geometrySizes' => $geometryData['headers'],

            'weight' => $weight,

            'compatibleAccessories' => $this->getCompatibleAccessories($bike),
        ];
    }

    //    /**
    //     * Prepare view data for bike detail page
    //     *
    //     * @return array{
    //     *     currentReference: BikeReference,
    //     *     article: Bike,
    //     *     isEbike: bool,
    //     *     frameOptions: Collection,
    //     *     colorOptions: Collection,
    //     *     batteryOptions: Collection,
    //     *     sizeOptions: Collection,
    //     *     geometries: Collection,
    //     *     geometrySizes: Collection,
    //     *     characteristics: Collection,
    //     *     weight: string,
    //     *     similarBikes: Collection,
    //     *     compatibleAccessories: Collection,
    //     *     realPrice: float,
    //     *     discountedPrice: float,
    //     *     hasDiscount: bool,
    //     *     discountPercent: int,
    //     * }
    //     */
    //    public function prepareViewData(BikeReference $currentReference): array
    //    {
    //        $currentReference->load([
    //            'bike.bikeModel.geometries.characteristic',
    //            'bike.bikeModel.geometries.size',
    //            'article.characteristics.characteristicType',
    //            'ebike.battery',
    //            'color',
    //            'frame',
    //            'availableSizes',
    //        ]);
    //
    //        $bike = $currentReference->bike;
    //        $isEbike = $currentReference->ebike !== null;
    //
    //        $variants = $this->bikeVariantService->getVariants($currentReference);
    //        $frameOptions = $this->bikeVariantService->buildFrameOptions($variants, $currentReference);
    //        $colorOptions = $this->bikeVariantService->buildColorOptions($variants, $currentReference);
    //        $batteryOptions = $this->bikeVariantService->buildBatteryOptions($variants, $currentReference) ?? collect();
    //
    //        $geometryData = $this->buildGeometryData($bike->bikeModel);
    //        $sizeOptions = $this->buildSizeOptions($currentReference);
    //
    //        $characteristicsGrouped = $bike->article->characteristics->groupBy('characteristicType.nom_type_carac');
    //
    //        $weightCharacteristicId = config('bike.characteristics.weight');
    //        $weight = $bike->article->characteristics->firstWhere('id_caracteristique', $weightCharacteristicId)
    //            ->pivot->valeur_caracteristique;
    //
    //        $similarBikes = $bike->article->similar()
    //            ->whereHas('bike')
    //            ->with('bike')
    //            ->limit(4)
    //            ->get();
    //
    //        $compatibleAccessories = $this->getCompatibleAccessories($bike);
    //
    //        return [
    //            'article' => $bike->article,
    //            'currentReference' => $currentReference,
    //
    //            'realPrice' => $currentReference->article->prix_article,
    //            'discountedPrice' => $currentReference->article->getDiscountedPrice(),
    //            'hasDiscount' => $currentReference->article->hasDiscount(),
    //            'discountPercent' => $currentReference->article->pourcentage_remise,
    //
    //            'bike' => $bike,
    //            'isEbike' => $isEbike,
    //            'frameOptions' => $frameOptions,
    //            'colorOptions' => $colorOptions,
    //            'batteryOptions' => $batteryOptions,
    //            'sizeOptions' => $sizeOptions,
    //
    //            'geometries' => $geometryData['rows'],
    //            'geometrySizes' => $geometryData['headers'],
    //
    //            'characteristics' => $characteristicsGrouped,
    //            'weight' => $weight,
    //            'similarBikes' => $similarBikes,
    //            'compatibleAccessories' => $compatibleAccessories,
    //        ];
    //    }

    private function getCompatibleAccessories(Bike $bike): Collection
    {
        return Article::query()
            ->whereHas('accessories')
            ->where('id_article', '!=', $bike->id_article)
            ->with(['category', 'accessories'])
            ->limit(8)
            ->get();
    }

    /**
     * Build geometry data for bike model
     *
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
                    'label' => $label,
                    'values' => $values,
                ];
            });

        return ['headers' => $headers, 'rows' => $rows];
    }
}
