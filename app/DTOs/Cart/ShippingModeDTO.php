<?php

namespace App\DTOs\Cart;

class ShippingModeDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public float $price,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            price: (float) $data['price'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
        ];
    }
}
