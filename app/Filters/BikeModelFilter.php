<?php

namespace App\Filters;

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
        $models = BikeModel::select('modele_velo.*')
            ->join('velo', 'modele_velo.id_modele_velo', '=', 'velo.id_modele_velo')
            ->whereIn('velo.id_article', $articleIds)
            ->distinct()
            ->orderBy('modele_velo.nom_modele_velo')
            ->get();

        return $this->format(
            $models,
            'id_modele_velo',
            'nom_modele_velo'
        );
    }
}
