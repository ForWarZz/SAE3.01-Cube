<?php

namespace App\DTOs\Order;

use Illuminate\Support\Collection;

class CreateReturnRequestDTO
{
    /**
     * @param  Collection<int, ReturnRequestItemDTO>  $items
     */
    public function __construct(
        public readonly Collection $items,
        public readonly ?string $message,
    ) {}

    public function toArray(): array
    {
        return [
            'items' => $this->items->map(fn ($item) => $item->toArray())->all(),
            'message' => $this->message,
        ];
    }
}
