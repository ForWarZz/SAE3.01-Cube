<?php

namespace App\Filters;

use App\DTOs\Filter\FilterOptionDTO;
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

    public function options(Builder $baseQuery, array $articleIds, array $context = []): Collection
    {
        if (empty($articleIds)) {
            return collect();
        }

        $hasPromo = $baseQuery->where('pourcentage_remise', '>', 0)->exists();
        $options = collect();

        if (! $hasPromo) {
            return $options;
        }

        $options->push(new FilterOptionDTO(
            id: 'in_discount',
            label: 'En promotion',
        ));

        return $options;
    }
}
