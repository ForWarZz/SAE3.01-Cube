<?php

namespace App\Filters;

use App\Filters\AbstractFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AvailabilityFilter extends AbstractFilter
{
    protected string $key = 'availability';

    public function apply(Builder $query, array $values): void
    {
        if (!empty($values)) {
            $query->where(function ($q) use ($values) {
                if (in_array('online', $values)) {
                    $q->orWhereHas('bike.references.availableSizes', function ($q2) {
                        $q2->where('dispo_en_ligne', true);
                    });
                }

                if (in_array('in_stock', $values)) {
                    $q->orWhereHas('bike.references.shopAvailabilities', function ($q2) {
                        $q2->where('statut', 'En Stock');
                    });
                }

                if (in_array('orderable', $values)) {
                    $q->orWhereHas('bike.references.shopAvailabilities', function ($q2) {
                        $q2->where('statut', 'Commandable');
                    });
                }
            });
        }
    }

    public function options(Builder $baseQuery): Collection
    {
        $options = collect();

        $hasOnline = (clone $baseQuery)
            ->whereHas('bike.references.availableSizes', function ($q) {
                $q->where('dispo_en_ligne', true);
            })
            ->exists();

        if ($hasOnline) {
            $options[] = [
                'id' => 'online',
                'label' => 'Disponible en ligne',
            ];
        }

        $hasStock = (clone $baseQuery)
            ->whereHas('bike.references.shopAvailabilities', function ($q) {
                $q->where('statut', 'En Stock');
            })
            ->exists();

        if ($hasStock) {
            $options[] = [
                'id' => 'in_stock',
                'label' => 'En stock magasin',
            ];
        }

        $hasOrderable = (clone $baseQuery)
            ->whereHas('bike.references.shopAvailabilities', function ($q) {
                $q->where('statut', 'Commandable');
            })
            ->exists();

        if ($hasOrderable) {
            $options[] = [
                'id' => 'orderable',
                'label' => 'Commandable en magasin',
            ];
        }

        return $options;
    }
}
