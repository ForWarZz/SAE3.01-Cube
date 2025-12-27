<?php

namespace App\DTOs\Cart;

use App\Models\DiscountCode;
use Illuminate\Support\Collection;

class CartViewDataDTO
{
    /**
     * @param  Collection<int, CartItemDTO>  $items
     */
    public function __construct(
        public Collection $items,
        public CartSummaryDTO $summary,
        public ?DiscountCode $discountCode,
        public int $count,
        public bool $hasBikes,
    ) {}

    public function isEmpty(): bool
    {
        return $this->count === 0;
    }

    public function toArray(): array
    {
        return [
            'cartData' => $this->items->map(fn (CartItemDTO $item) => $item->toArray())->toArray(),
            'summaryData' => $this->summary->toArray(),
            'discountData' => $this->discountCode,
            'count' => $this->count,
            'hasBikes' => $this->hasBikes,
        ];
    }

    public function toViewData(): array
    {
        return [
            'cartData' => $this->items,
            'summaryData' => $this->summary,
            'discountData' => $this->discountCode,
            'count' => $this->count,
            'hasBikes' => $this->hasBikes,
        ];
    }
}
