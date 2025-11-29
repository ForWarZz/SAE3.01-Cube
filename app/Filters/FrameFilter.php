<?php

namespace App\Filters;

use App\Models\BikeFrame;
use App\Models\BikeReference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class FrameFilter extends AbstractFilter
{
    protected string $key = 'frame';

    public function apply(Builder $query, array $values): void
    {
        if (!empty($values)) {
            $query->whereHas('bike.references', fn($q) => $q->whereIn('id_cadre_velo', $values));
        }
    }

    public function options(Builder $baseQuery): Collection
    {
        $articleIds = $baseQuery->pluck('id_article');

        return $this->format(
            BikeFrame::whereIn('id_cadre_velo',
                BikeReference::whereIn('id_article', $articleIds)->pluck('id_cadre_velo')
            )->get(),
            'id_cadre_velo',
            'label_cadre_velo'
        );
    }
}
