<?php

namespace App\Services;

use App\DTOs\Article\GeometryRowDTO;
use App\Models\Bike;
use App\Models\BikeModel;
use App\Models\BikeReference;
use Illuminate\Support\Collection;

class BikeService
{
    public function __construct(
        protected BikeVariantService $bikeVariantService,
    ) {}

    // Maintenant on reçoit explicitement le Bike et la BikeReference (pré-chargés en amont)
    public function prepareBikeData(Bike $bike, BikeReference $bikeReference): array
    {
        $variants = $bike->references;
        $geometryData = $this->buildGeometryData($bike->bikeModel);

        return [
            'isBike' => true,
            'currentReference' => $bikeReference,
            'bike' => $bike,

            'frameOptions' => $this->bikeVariantService->buildFrameOptions($variants, $bikeReference),
            'colorOptions' => $this->bikeVariantService->buildColorOptions($variants, $bikeReference),
            'batteryOptions' => $this->bikeVariantService->buildBatteryOptions($variants, $bikeReference),

            'geometries' => $geometryData['rows'],
            'geometrySizes' => $geometryData['headers'],

            'weight' => 20,

            'compatibleAccessories' => $bike->compatibleAccessories,
            'images' => $bikeReference->getImagesUrls(),
        ];
    }

    /**
     * Build geometry data for bike model
     *
     * @return array{headers: Collection, rows: Collection<int, GeometryRowDTO>}
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

                return new GeometryRowDTO(
                    label: $label,
                    values: $values,
                );
            });

        return ['headers' => $headers, 'rows' => $rows];
    }
}
