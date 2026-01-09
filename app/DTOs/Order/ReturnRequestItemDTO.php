<?php

namespace App\DTOs\Order;

class ReturnRequestItemDTO
{
    public function __construct(
        public readonly int $lineId,
        public readonly int $quantity,
    ) {}

    public function toArray(): array
    {
        return [
            'lineId' => $this->lineId,
            'quantity' => $this->quantity,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            lineId: (int) $data['item_id'],
            quantity: (int) $data['quantity'],
        );
    }
}
