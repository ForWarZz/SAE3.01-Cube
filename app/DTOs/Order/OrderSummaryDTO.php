<?php

namespace App\DTOs\Order;

use DateTimeInterface;

class OrderSummaryDTO
{
    public function __construct(
        public int $id,
        public string $number,
        public DateTimeInterface $date,
        public ?string $tracking,
        public string $statusLabel,
        public StatusStyleDTO $statusColors,
        public int $countArticles,
        public OrderFinancialsDTO $financials,
        public ?AddressDTO $address,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'date' => $this->date,
            'tracking' => $this->tracking,
            'statusLabel' => $this->statusLabel,
            'statusColors' => $this->statusColors->toArray(),
            'countArticles' => $this->countArticles,
            'financials' => $this->financials->toArray(),
            'address' => $this->address?->toArray(),
        ];
    }
}
