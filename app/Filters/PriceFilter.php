<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PriceFilter extends AbstractFilter
{
    public string $key = 'price';

    public function apply(Builder $query, array $values): void
    {
        if (! isset($values['min']) && ! isset($values['max'])) {
            return;
        }

        if (isset($values['min'])) {
            $query->where('prix_article', '>=', (float) $values['min']);
        }

        if (isset($values['max'])) {
            $query->where('prix_article', '<=', (float) $values['max']);
        }
    }

    public function options(Builder $baseQuery): Collection
    {
        return collect([
            'min' => (int) $baseQuery->min('prix_article'),
            'max' => (int) $baseQuery->max('prix_article'),
        ]);
    }
}
