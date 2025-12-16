<?php

namespace App\DTOs\Order;

class StatusStyleDTO
{
    public function __construct(
        public string $bg,
        public string $text,
    ) {}

    public function toArray(): array
    {
        return [
            'bg' => $this->bg,
            'text' => $this->text,
        ];
    }
}
