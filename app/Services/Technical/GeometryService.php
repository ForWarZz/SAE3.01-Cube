<?php

namespace App\Services\Technical;

use App\Models\BikeModel;
use App\Models\Geometry;
use App\Models\GeometryCharacteristic;
use App\Models\Size;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GeometryService
{
    public function getModelEditData(BikeModel $model): array
    {
        $sizes = $this->getSizesForModel($model);
        $geometries = $this->getGeometriesForModel($model);

        $matrixCharacteristics = $this->getMatrixCharacteristics($geometries);
        $availableCharacteristics = $this->getAvailableCharacteristics($matrixCharacteristics);
        $matrix = $this->buildMatrix($geometries);

        return [
            'model' => $model,
            'sizes' => $sizes,
            'matrixCharacteristics' => $matrixCharacteristics,
            'availableCharacteristics' => $availableCharacteristics,
            'matrix' => $matrix,
        ];
    }

    public function updateGeometries(BikeModel $model, array $data): void
    {
        DB::transaction(function () use ($model, $data) {
            $model->geometries()->delete();

            $insertData = [];
            foreach ($data as $caracId => $sizes) {
                foreach ($sizes as $sizeId => $value) {
                    if ($value !== null && $value !== '') {
                        $insertData[] = [
                            'id_modele_velo' => $model->id_modele_velo,
                            'id_carac_geo' => $caracId,
                            'id_taille' => $sizeId,
                            'valeur_carac' => $value,
                        ];
                    }
                }
            }

            if (! empty($insertData)) {
                Geometry::insert($insertData);
            }
        });
    }

    public function addCharacteristic(BikeModel $model, string $actionType, ?int $existingId, ?string $newName): void
    {
        $caracId = $this->resolveCharacteristicId($actionType, $existingId, $newName);
        $sizes = $this->getSizesForModel($model);

        if ($sizes->isEmpty()) {
            throw new RuntimeException('Aucune taille disponible pour ce modèle.');
        }

        $insertData = $sizes->map(fn ($size) => [
            'id_modele_velo' => $model->id_modele_velo,
            'id_carac_geo' => $caracId,
            'id_taille' => $size->id_taille,
            'valeur_carac' => null,
        ])->toArray();

        DB::transaction(function () use ($insertData) {
            foreach ($insertData as $data) {
                Geometry::firstOrCreate(
                    [
                        'id_modele_velo' => $data['id_modele_velo'],
                        'id_carac_geo' => $data['id_carac_geo'],
                        'id_taille' => $data['id_taille'],
                    ],
                    ['valeur_carac' => $data['valeur_carac']]
                );
            }
        });
    }

    private function getSizesForModel(BikeModel $model): Collection
    {
        return Size::bike()
            ->whereHas('references.bikeReference.bike', function ($query) use ($model) {
                $query->where('id_modele_velo', $model->id_modele_velo);
            })
            ->orderBy('id_taille')
            ->get();
    }

    private function getGeometriesForModel(BikeModel $model): Collection
    {
        return $model->geometries()
            ->with(['characteristic', 'size'])
            ->get();
    }

    private function getMatrixCharacteristics(Collection $geometries): Collection
    {
        return $geometries
            ->pluck('characteristic')
            ->filter()
            ->unique('id_carac_geo')
            ->sortBy('label_carac_geo')
            ->values();
    }

    private function getAvailableCharacteristics(Collection $matrixCharacteristics): Collection
    {
        $usedCaracIds = $matrixCharacteristics->pluck('id_carac_geo');

        return GeometryCharacteristic::whereNotIn('id_carac_geo', $usedCaracIds)
            ->orderBy('label_carac_geo')
            ->get();
    }

    private function buildMatrix(Collection $geometries): array
    {
        $matrix = [];
        foreach ($geometries as $geometry) {
            $matrix[$geometry->id_carac_geo][$geometry->id_taille] = $geometry->valeur_carac;
        }

        return $matrix;
    }

    private function resolveCharacteristicId(string $actionType, ?int $existingId, ?string $newName): int
    {
        if ($actionType === 'existing') {
            return $existingId;
        }

        return GeometryCharacteristic::firstOrCreate(
            ['label_carac_geo' => $newName],
            ['label_carac_geo' => $newName]
        )->id_carac_geo;
    }
}
