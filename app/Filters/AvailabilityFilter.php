<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AvailabilityFilter extends AbstractFilter
{
    protected string $key = 'availability';

    public function apply(Builder $query, array $values): void
    {
        $orderableStatus = config('article.availability.orderable');
        $inStockStatus = config('article.availability.in_stock');

        if (empty($values)) {
            return;
        }

        $query->where(function ($q) use ($values, $orderableStatus, $inStockStatus) {
            foreach ($values as $value) {
                switch ($value) {
                    case 'online':
                        $q->orWhereHas('bike.references.availableSizes', fn ($q2) => $q2->where('dispo_en_ligne', true))
                            ->orWhereHas('accessory.availableSizes', fn ($q2) => $q2->where('dispo_en_ligne', true));
                        break;

                    case 'in_stock':
                        $q->orWhereHas('bike.references.shopAvailabilities', fn ($q2) => $q2->where('statut', $inStockStatus))
                            ->orWhereHas('accessory.shopAvailabilities', fn ($q2) => $q2->where('statut', $inStockStatus));
                        break;

                    case 'orderable':
                        $q->orWhereHas('bike.references.shopAvailabilities', fn ($q2) => $q2->where('statut', $orderableStatus))
                            ->orWhereHas('accessory.shopAvailabilities', fn ($q2) => $q2->where('statut', $orderableStatus));
                        break;
                }
            }
        });
    }

    public function options(Builder $baseQuery, array $context = []): Collection
    {
        $orderableStatus = config('article.availability.orderable');
        $inStockStatus = config('article.availability.in_stock');

        $options = collect();

        // Check online availability
        $hasOnline = (clone $baseQuery)
            ->whereHas('bike.references.availableSizes', fn ($q) => $q->where('dispo_en_ligne', true))
            ->orWhereHas('accessory.availableSizes', fn ($q) => $q->where('dispo_en_ligne', true))
            ->exists();

        if ($hasOnline) {
            $options[] = ['id' => 'online', 'label' => 'Disponible en ligne'];
        }

        // Check in stock
        $hasStock = (clone $baseQuery)
            ->whereHas('bike.references.shopAvailabilities', fn ($q) => $q->where('statut', $inStockStatus))
            ->orWhereHas('accessory.shopAvailabilities', fn ($q) => $q->where('statut', $inStockStatus))
            ->exists();

        if ($hasStock) {
            $options[] = ['id' => 'in_stock', 'label' => 'En stock magasin'];
        }

        // Check orderable
        $hasOrderable = (clone $baseQuery)
            ->whereHas('bike.references.shopAvailabilities', fn ($q) => $q->where('statut', $orderableStatus))
            ->orWhereHas('accessory.shopAvailabilities', fn ($q) => $q->where('statut', $orderableStatus))
            ->exists();

        if ($hasOrderable) {
            $options[] = ['id' => 'orderable', 'label' => 'Commandable en magasin'];
        }

        return $options;
    }
}
