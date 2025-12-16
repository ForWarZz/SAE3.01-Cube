<?php

namespace App\DTOs\Cart;

class CartSummaryDTO
{
    public function __construct(
        public float $subtotal,
        public float $discount,
        public float $shipping,
        public float $tax,
        public float $total,
    ) {}

    public function toArray(): array
    {
        return [
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'shipping' => $this->shipping,
            'tax' => $this->tax,
            'total' => $this->total,
        ];
    }
}
