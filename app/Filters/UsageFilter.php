<?php

namespace App\Filters;

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

    public function options(Builder $baseQuery, array $articleIds, array $context = []): Collection
    {
        if (empty($articleIds)) {
            return collect();
        }

        $usages = Usage::select('usage_velo.*')
            ->join('velo', 'usage_velo.id_usage', '=', 'velo.id_usage')
            ->whereIn('velo.id_article', $articleIds)
            ->distinct()
            ->orderBy('usage_velo.label_usage')
            ->get();

        return $this->format(
            $usages,
            'id_usage',
            'label_usage'
        );
    }
}
