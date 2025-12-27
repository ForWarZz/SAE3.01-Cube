<?php

namespace App\Filters;

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

    public function options(Builder $baseQuery, array $articleIds, array $context = []): Collection
    {
        $materials = BikeFrameMaterial::select('materiau_cadre_velo.*')
            ->join('velo', 'materiau_cadre_velo.id_materiau_cadre', '=', 'velo.id_materiau_cadre')
            ->whereIn('velo.id_article', $articleIds)
            ->distinct()
            ->orderBy('materiau_cadre_velo.label_materiau_cadre')
            ->get();

        return $this->format(
            $materials,
            'id_materiau_cadre',
            'label_materiau_cadre'
        );
    }
}
