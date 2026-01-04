<?php

namespace App\DTOs;

use App\Models\Shop;

class ShopDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $address,
        public readonly string $postalCode,
        public readonly string $city,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
    ) {}

    public static function fromModel(Shop $shop): self
    {
        return new self(
            id: $shop->id_magasin,
            name: $shop->nom_magasin,
            address: $shop->full_address,
            postalCode: $shop->city->cp_ville,
            city: $shop->city->nom_ville,
            latitude: $shop->latitude,
            longitude: $shop->longitude,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'postal_code' => $this->postalCode,
            'city' => $this->city,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
