<?php

namespace App\DTOs\Article;

use Illuminate\Support\Collection;

class GeometryRowDTO
{
    /**
     * @param  Collection<int, string>  $values
     */
    public function __construct(
        public string $label,
        public Collection $values,
    ) {}

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'values' => $this->values->toArray(),
        ];
    }
}
