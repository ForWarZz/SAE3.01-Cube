<?php

namespace App\Filters;

use App\Models\Bike;
use App\Models\BikeFrameMaterial;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MaterialFilter extends AbstractFilter
{
    protected string $key = 'material';

    public function apply(Builder $query, array $values): void
    {
        if (! empty($values)) {
            $query->whereHas('bike', fn ($q) => $q->whereIn('id_materiau_cadre', $values));
        }
    }

    public function options(Builder $baseQuery, array $context = []): Collection
    {
        $articleIds = $baseQuery->pluck('id_article');

        return $this->format(
            BikeFrameMaterial::whereIn('id_materiau_cadre',
                Bike::whereIn('id_article', $articleIds)->pluck('id_materiau_cadre')
            )->get(),
            'id_materiau_cadre',
            'label_materiau_cadre'
        );
    }
}
