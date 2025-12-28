<?php

namespace App\Filters;

use App\Models\Vintage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class VintageFilter extends AbstractFilter
{
    protected string $key = 'vintage';

    public function apply(Builder $query, array $values): void
    {
        if (! empty($values)) {
            $query->whereHas('bike', fn ($q) => $q->whereIn('id_millesime', $values));
        }
    }

    public function options(Builder $baseQuery, array $articleIds, array $context = []): Collection
    {
        if (empty($articleIds)) {
            return collect();
        }

        $vintages = Vintage::select('millesime.*')
            ->join('velo', 'millesime.id_millesime', '=', 'velo.id_millesime')
            ->whereIn('velo.id_article', $articleIds)
            ->distinct()
            ->orderBy('millesime.millesime_velo', 'desc')
            ->get();

        return $this->format(
            $vintages,
            'id_millesime',
            'millesime_velo'
        );
    }
}
