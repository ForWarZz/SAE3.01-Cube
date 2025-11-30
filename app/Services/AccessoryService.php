<?php

namespace App\Services;

use App\Models\Accessory;

class AccessoryService
{
    public function prepareAccessoryData(Accessory $accessory): array
    {
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
            'weight' => '25',
        ];
    }
}
