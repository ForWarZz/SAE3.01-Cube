<?php

namespace App\DTOs\Article;

class SizeOptionDTO
{
    public const SHOP_STATUS_IN_STOCK = 'in_stock';

    public const SHOP_STATUS_ORDERABLE = 'orderable';

    public const SHOP_STATUS_UNAVAILABLE = 'unavailable';

    public bool $disabled;

    public function __construct(
        public int $id,
        public string $label,
        public bool $availableOnline,
        public string $shopStatus,
    ) {
        $this->disabled = ! $this->isAvailable();
    }

    public function isAvailable(): bool
    {
        return $this->availableOnline || $this->shopStatus !== self::SHOP_STATUS_UNAVAILABLE;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'availableOnline' => $this->availableOnline,
            'shopStatus' => $this->shopStatus,
            'disabled' => $this->disabled,
        ];
    }
}