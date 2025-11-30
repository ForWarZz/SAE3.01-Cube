<?php

namespace App\Services;

use App\Models\Accessory;

class AccessoryService
{
    public function prepareAccessoryData(Accessory $accessory): array
    {
        $weight = $accessory->article->characteristics
            ->firstWhere('id_caracteristique', config('accessory.characteristics.weight'))
            ?->pivot->valeur_caracteristique ?? null;

        return [
            'isBike' => false,
            'currentReference' => $accessory,

            'frameOptions' => collect(),
            'colorOptions' => collect(),
            'batteryOptions' => collect(),
            'sizeOptions' => collect(),

            'geometries' => collect(),
            'geometrySizes' => collect(),

            'compatibleAccessories' => collect(),
            'weight' => $weight ?? 0,
        ];
    }
}
