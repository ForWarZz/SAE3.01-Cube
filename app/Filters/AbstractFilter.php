<?php

namespace App\Filters;

use App\DTOs\Filter\FilterOptionDTO;
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

    /**
     * @return Collection<int, FilterOptionDTO>
     */
    protected function format(Collection $collection, string $idField, string $labelField): Collection
    {
        return $collection->map(fn ($item) => new FilterOptionDTO(
            id: (string) $item->$idField,
            label: $item->$labelField,
        ));
    }
}
