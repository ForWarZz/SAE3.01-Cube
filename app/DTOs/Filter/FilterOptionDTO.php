<?php

namespace App\DTOs\Filter;

class FilterOptionDTO
{
    public function __construct(
        public string $id,
        public string $label,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
        ];
    }
}
