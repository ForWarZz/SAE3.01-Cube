<?php

namespace App\DTOs\Order;

class OrderLineItemDTO
{
    public function __construct(
        public string $name,
        public string $subtitle,
        public ?string $image,
        public ?string $colorHex,
        public ?string $colorName,
        public ?string $size,
        public int $quantity,
        public float $unitPrice,
        public float $totalPrice,
        public int $articleId,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'subtitle' => $this->subtitle,
            'image' => $this->image,
            'colorHex' => $this->colorHex,
            'colorName' => $this->colorName,
            'size' => $this->size,
            'quantity' => $this->quantity,
            'unitPrice' => $this->unitPrice,
            'totalPrice' => $this->totalPrice,
            'articleId' => $this->articleId,
        ];
    }
}
