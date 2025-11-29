<?php

return [
    'characteristics' => [
        'weight' => env('BIKE_CHARACTERISTIC_WEIGHT_ID', 22),
    ],
    'availability' => [
        'in_stock' => 'En Stock',
        'orderable' => 'Commandable',
        'in_shop' => ['En Stock', 'Commandable'],
    ],
];
