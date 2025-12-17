<?php

namespace App\DTOs\Article;

class VariantOptionDTO
{
    public function __construct(
        public string $label,
        public string $url,
        public bool $active,
        public ?string $hex = null,
    ) {}

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'url' => $this->url,
            'active' => $this->active,
            'hex' => $this->hex,
        ];
    }
}
