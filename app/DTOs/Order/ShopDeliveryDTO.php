<?php

namespace App\DTOs\Order;

use App\Models\Shop;

class ShopDeliveryDTO
{
    public function __construct(
        public string $shopName,
        public string $street,
        public string $postalCode,
        public string $city,
    ) {}

    public static function fromModel(Shop $shop): ShopDeliveryDTO
    {
        return new self(
            shopName: $shop->nom_magasin,
            street: $shop->full_address,
            postalCode: $shop->city->cp_ville,
            city: ($shop->city->cp_ville).' '.($shop->city->nom_ville),
        );
    }

    public function toArray(): array
    {
        return [
            'shopName' => $this->shopName,
            'street' => $this->street,
            'postalCode' => $this->postalCode,
            'city' => $this->city,
        ];
    }
}
