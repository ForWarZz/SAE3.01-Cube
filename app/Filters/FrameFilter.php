<?php

namespace App\Filters;

use App\Models\BikeFrame;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FrameFilter extends AbstractFilter
{
    protected string $key = 'frame';

    public function apply(Builder $query, array $values): void
    {
        if (! empty($values)) {
            $query->whereHas('bike.references', fn ($q) => $q->whereIn('id_cadre_velo', $values));
        }
    }

    public function options(Builder $baseQuery, array $articleIds, array $context = []): Collection
    {
        $frames = BikeFrame::select('cadre_velo.*')
            ->join('reference_velo', 'cadre_velo.id_cadre_velo', '=', 'reference_velo.id_cadre_velo')
            ->whereIn('reference_velo.id_article', $articleIds)
            ->distinct()
            ->orderBy('cadre_velo.label_cadre_velo')
            ->get();

        return $this->format(
            $frames,
            'id_cadre_velo',
            'label_cadre_velo'
        );
    }
}
