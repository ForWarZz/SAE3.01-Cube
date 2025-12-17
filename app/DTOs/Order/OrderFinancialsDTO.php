<?php

namespace App\DTOs\Order;

class OrderFinancialsDTO
{
    public function __construct(
        public float $subtotal,
        public float $discount,
        public float $shipping,
        public float $total,
        public ?float $discountPercent,
        public int $count,
    ) {}

    public function toArray(): array
    {
        return [
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'shipping' => $this->shipping,
            'total' => $this->total,
            'discountPercent' => $this->discountPercent,
            'count' => $this->count,
        ];
    }
}
