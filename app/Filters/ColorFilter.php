<?php

namespace App\Filters;

use App\Models\Color;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ColorFilter extends AbstractFilter
{
    protected string $key = 'color';

    public function apply(Builder $query, array $values): void
    {
        if (! empty($values)) {
            $query->whereHas('bike.references', fn ($q) => $q->whereIn('id_couleur', $values));
        }
    }

    public function options(Builder $baseQuery, array $articleIds, array $context = []): Collection
    {
        $colors = Color::select('couleur.*')
            ->join('reference_velo', 'couleur.id_couleur', '=', 'reference_velo.id_couleur')
            ->whereIn('reference_velo.id_article', $articleIds)
            ->distinct()
            ->orderBy('couleur.label_couleur')
            ->get();

        return $this->format(
            $colors,
            'id_couleur',
            'label_couleur'
        );
    }
}
