<?php

return [
    'per_page' => env('ARTICLE_PER_PAGE', 15),
    'availability' => [
        'in_stock' => 'En Stock',
        'orderable' => 'Commandable',
        'in_shop' => ['En Stock', 'Commandable'],
    ],
];
