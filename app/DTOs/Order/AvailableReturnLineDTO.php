<?php

namespace App\DTOs\Order;

class AvailableReturnLineDTO
{
    public function __construct(
        public readonly int $lineId,
        public readonly string $name,
        public readonly ?string $subtitle,
        public readonly ?string $image,
        public readonly ?string $colorHex,
        public readonly ?string $colorName,
        public readonly ?string $size,
        public readonly int $orderedQuantity,
        public readonly int $returnedQuantity,
        public readonly int $availableQuantity,
        public readonly float $unitPrice,
        public readonly float $totalPrice,
        public readonly ?int $articleId,
    ) {}

    public function toArray(): array
    {
        return [
            'lineId' => $this->lineId,
            'name' => $this->name,
            'subtitle' => $this->subtitle,
            'image' => $this->image,
            'colorHex' => $this->colorHex,
            'colorName' => $this->colorName,
            'size' => $this->size,
            'orderedQuantity' => $this->orderedQuantity,
            'returnedQuantity' => $this->returnedQuantity,
            'availableQuantity' => $this->availableQuantity,
            'unitPrice' => $this->unitPrice,
            'totalPrice' => $this->totalPrice,
            'articleId' => $this->articleId,
        ];
    }
}
