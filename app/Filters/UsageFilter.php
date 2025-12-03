<?php

namespace App\Filters;

use App\Models\Bike;
use App\Models\Usage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class UsageFilter extends AbstractFilter
{
    protected string $key = 'usage';

    public function apply(Builder $query, array $values): void
    {
        if (! empty($values)) {
            $query->whereHas('bike', fn ($q) => $q->whereIn('id_usage', $values));
        }
    }

    public function options(Builder $baseQuery, array $context = []): Collection
    {
        $articleIds = $baseQuery->pluck('id_article');

        return $this->format(
            Usage::whereIn('id_usage',
                Bike::whereIn('id_article', $articleIds)->pluck('id_usage')
            )->get(),
            'id_usage',
            'label_usage'
        );
    }
}
