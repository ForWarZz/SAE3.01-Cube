<?php

namespace App\Filters;

use App\Filters\AbstractFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PromotionFilter extends AbstractFilter
{
    protected string $key = 'promotion';

    public function apply(Builder $query, array $values): void
    {
        if (in_array('on_promotion', $values)) {
            $query->where('pourcentage_remise', '>', 0);
        }

        if (in_array('no_promotion', $values)) {
            $query->where('pourcentage_remise', '=', 0);
        }
    }

    public function options(Builder $baseQuery): Collection
    {
        $hasPromo = (clone $baseQuery)->where('pourcentage_remise', '>', 0)->exists();
        $options = collect();

        if (!$hasPromo) return $options;

        $options->push([
            'id' => 'on_promotion',
            'label' => 'En promotion',
        ]);

        return $options;
    }
}
