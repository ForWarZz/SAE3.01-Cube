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
            $globalQuery->whereHas('bike.references.availableSizes', function ($q) use ($values) {
                $q->whereIn('taille.id_taille', $values);
            })
                ->orWhereHas('accessory.availableSizes', function ($q) use ($values) {
                    $q->whereIn('taille.id_taille', $values);
                });
        });
    }

    public function options(Builder $baseQuery, array $context = []): Collection
    {
        $availableSizes = Size::query()
            ->whereHas('references.bikeReference', function (Builder $q) use ($baseQuery) {
                $q->whereIn('id_article', $baseQuery->pluck('id_article'));
            })
            ->orWhereHas('references.accessory', function (Builder $q) use ($baseQuery) {
                $q->whereIn('id_article', $baseQuery->pluck('id_article'));
            })
            ->orderBy('nom_taille')
            ->get();

        return $this->format(
            $availableSizes,
            'id_taille',
            'nom_taille'
        );
    }
}
