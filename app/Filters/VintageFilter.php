<?php

namespace App\Filters;

use App\Models\Bike;
use App\Models\Vintage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class VintageFilter extends AbstractFilter
{
    protected string $key = 'vintage';

    public function apply(Builder $query, array $values): void
    {
        if (!empty($values)) {
            $query->whereHas('bike', fn($q) => $q->whereIn('id_millesime', $values));
        }
    }

    public function options(Builder $baseQuery): Collection
    {
        $articleIds = $baseQuery->pluck('id_article');

        return $this->format(
            Vintage::whereIn('id_millesime',
                Bike::whereIn('id_article', $articleIds)->pluck('id_millesime')
            )->get(),
            'id_millesime',
            'millesime_velo'
        );
    }
}
