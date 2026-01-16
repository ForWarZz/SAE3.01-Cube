<?php

namespace App\DTOs\Shop;

class SelectedShopDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $city,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'city' => $this->city,
        ];
    }

    public static function fromShop($shop): self
    {
        return new self(
            id: $shop->id_magasin,
            name: $shop->nom_magasin,
            city: trim($shop->city->nom_ville)
        );
    }
}
