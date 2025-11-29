<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AvailabilityFilter extends AbstractFilter
{
    protected string $key = 'availability';

    public function apply(Builder $query, array $values): void
    {
        $orderableStatus = config('bike.availability.orderable');
        $inStockStatus = config('bike.availability.in_stock');

        if (! empty($values)) {
            $query->where(function ($q) use ($inStockStatus, $orderableStatus, $values) {
                if (in_array('online', $values)) {
                    $q->orWhereHas('bike.references.availableSizes', function ($q2) {
                        $q2->where('dispo_en_ligne', true);
                    });
                }

                if (in_array('in_stock', $values)) {
                    $q->orWhereHas('bike.references.shopAvailabilities', function ($q2) use ($orderableStatus) {
                        $q2->where('statut', $orderableStatus);
                    });
                }

                if (in_array('orderable', $values)) {
                    $q->orWhereHas('bike.references.shopAvailabilities', function ($q2) use ($inStockStatus) {
                        $q2->where('statut', $inStockStatus);
                    });
                }
            });
        }
    }

    public function options(Builder $baseQuery): Collection
    {
        $orderableStatus = config('bike.availability.orderable');
        $inStockStatus = config('bike.availability.in_stock');

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
            ->whereHas('bike.references.shopAvailabilities', function ($q) use ($inStockStatus) {
                $q->where('statut', $inStockStatus);
            })
            ->exists();

        if ($hasStock) {
            $options[] = [
                'id' => 'in_stock',
                'label' => 'En stock magasin',
            ];
        }

        $hasOrderable = (clone $baseQuery)
            ->whereHas('bike.references.shopAvailabilities', function ($q) use ($orderableStatus) {
                $q->where('statut', $orderableStatus);
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
