<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DiscountFilter extends AbstractFilter
{
    protected string $key = 'discount';

    public function apply(Builder $query, array $values): void
    {
        if (in_array('in_discount', $values)) {
            $query->where('pourcentage_remise', '>', 0);
        }
    }

    public function options(Builder $baseQuery, array $context = []): Collection
    {
        $hasPromo = (clone $baseQuery)->where('pourcentage_remise', '>', 0)->exists();
        $options = collect();

        if (! $hasPromo) {
            return $options;
        }

        $options->push([
            'id' => 'in_discount',
            'label' => 'En promotion',
        ]);

        return $options;
    }
}
