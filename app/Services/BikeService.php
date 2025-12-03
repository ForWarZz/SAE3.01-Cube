<?php

namespace App\Services;

use App\Models\Accessory;
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
        $currentReference->bike->bikeModel->load([
            'geometries.characteristic',
            'geometries.size',
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

    /**
     * @return Collection<Accessory>
     */
    private function getCompatibleAccessories(Bike $bike): Collection
    {
        return $bike->compatibleAccessories()
            ->with('article')
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
