<?php

namespace App\DTOs;

class BreadcrumbDTO
{
    public function __construct(
        public string $label,
        public ?string $url = null,
    ) {}

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'url' => $this->url,
        ];
    }
}
