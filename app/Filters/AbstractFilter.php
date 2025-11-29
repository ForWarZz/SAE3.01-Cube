<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class AbstractFilter implements ArticleFilter
{
    protected string $key;

    public function key(): string
    {
        return $this->key;
    }

    public function values(Request $request): array
    {
        return (array) $request->input($this->key(), []);
    }

    protected function format(Collection $collection, string $idField, string $labelField): Collection
    {
        return $collection->map(fn($item) => [
            'id' => (string)$item->$idField,
            'label' => $item->$labelField,
        ]);
    }
}
