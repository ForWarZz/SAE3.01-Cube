<?php

namespace App\Services;

use App\Models\Accessory;

class AccessoryService
{
    public function prepareAccessoryData(Accessory $accessory): array
    {
        $weight = $accessory->characteristics
            ->firstWhere('id_caracteristique', Accessory::WEIGHT_CHARACTERISTIC_ID)
            ?->pivot->valeur_caracteristique ?? null;

        return [
            'isBike' => false,
            'currentReference' => $accessory,

            'frameOptions' => collect(),
            'colorOptions' => collect(),
            'batteryOptions' => collect(),

            'geometries' => collect(),
            'geometrySizes' => collect(),

            'compatibleAccessories' => collect(),
            'weight' => $weight ?? 0,
        ];
    }
}
