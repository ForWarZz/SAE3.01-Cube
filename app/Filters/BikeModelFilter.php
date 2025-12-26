<?php

namespace App\Filters;

use App\Models\Bike;
use App\Models\BikeModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BikeModelFilter extends AbstractFilter
{
    protected string $key = 'bike_model';

    public function apply(Builder $query, array $values): void
    {
        if (! empty($values)) {
            $query->whereHas('bike', fn ($q) => $q->whereIn('id_modele_velo', $values));
        }
    }

    public function options(Builder $baseQuery, array $articleIds, array $context = []): Collection
    {
        return $this->format(
            BikeModel::whereIn('id_modele_velo',
                Bike::whereIn('id_article', $articleIds)->pluck('id_modele_velo')
            )->get(),
            'id_modele_velo',
            'nom_modele_velo'
        );
    }
}
