<?php

namespace App\Filters;

use App\Models\BikeReference;
use App\Models\Color;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ColorFilter extends AbstractFilter
{
    protected string $key = 'color';

    public function apply(Builder $query, array $values): void
    {
        if (! empty($values)) {
            $query->whereHas('bike.references', fn ($q) => $q->whereIn('id_couleur', $values));
        }
    }

    public function options(Builder $baseQuery, array $articleIds, array $context = []): Collection
    {
        return $this->format(
            Color::whereIn('id_couleur',
                BikeReference::whereIn('id_article', $articleIds)->pluck('id_couleur')
            )->get(),
            'id_couleur',
            'label_couleur'
        );
    }
}
