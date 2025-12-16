<?php

namespace App\DTOs\Cart;

class CheckoutDataDTO
{
    public function __construct(
        public ?int $billing_address_id,
        public ?int $delivery_address_id,
        public ?ShippingModeDTO $shipping_mode,
    ) {}

    public function toArray(): array
    {
        return [
            'billing_address_id' => $this->billing_address_id,
            'delivery_address_id' => $this->delivery_address_id,
            'shipping_mode' => $this->shipping_mode?->toArray(),
        ];
    }
}
