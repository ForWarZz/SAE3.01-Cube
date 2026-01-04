<?php

namespace App\DTOs\Cart;

use App\DTOs\ShopDTO;

class CheckoutDataDTO
{
    public function __construct(
        public ?int $billing_address_id,
        public ?int $delivery_address_id,
        public ?ShippingModeDTO $shipping_mode,
        public ?ShopDTO $shop = null,
    ) {}

    public function toArray(): array
    {
        return [
            'billing_address_id' => $this->billing_address_id,
            'delivery_address_id' => $this->delivery_address_id,
            'shipping_mode' => $this->shipping_mode?->toArray(),
            'shop' => $this->shop?->toArray(),
        ];
    }
}
