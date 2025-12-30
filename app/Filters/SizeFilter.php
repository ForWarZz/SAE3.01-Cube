<?php

namespace App\Filters;

use App\Models\Size;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SizeFilter extends AbstractFilter
{
    protected string $key = 'size';

    public function apply(Builder $query, array $values): void
    {
        if (empty($values)) {
            return;
        }

        $query->where(function (Builder $globalQuery) use ($values) {
            $globalQuery->whereHas('references.availableSizes', function ($q) use ($values) {
                $q->whereIn('taille.id_taille', $values);
            });
        });
    }

    public function options(Builder $baseQuery, array $articleIds, array $context = []): Collection
    {
        if (empty($articleIds)) {
            return collect();
        }

        $availableSizes = Size::query()
            ->whereHas('references', function ($q) use ($articleIds) {
                $q->whereIn('id_article', $articleIds);
            })
            ->orderBy('nom_taille')
            ->get();

        return $this->format($availableSizes, 'id_taille', 'label');
    }
}
