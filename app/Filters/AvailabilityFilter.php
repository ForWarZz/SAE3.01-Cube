<?php

namespace App\Filters;

use App\DTOs\Filter\FilterOptionDTO;
use App\Models\ShopAvailability;
use App\Models\Size;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AvailabilityFilter extends AbstractFilter
{
    protected string $key = 'availability';

    public function apply(Builder $query, array $values): void
    {
        if (empty($values)) {
            return;
        }

        $query->where(function ($q) use ($values) {
            if (in_array('online', $values)) {
                $q->orWhereHas('references.availableSizes', fn ($q2) => $q2->where('dispo_en_ligne', true));
            }

            $shopStatuses = [];

            if (in_array('in_stock', $values)) {
                $shopStatuses[] = ShopAvailability::STATUS_IN_STOCK;
            }

            if (in_array('orderable', $values)) {
                $shopStatuses[] = ShopAvailability::STATUS_ORDERABLE;
            }

            if (! empty($shopStatuses)) {
                $q->orWhereHas('references.shopAvailabilities', fn ($q2) => $q2->whereIn('statut', $shopStatuses));
            }
        });
    }

    public function options(Builder $baseQuery, array $articleIds, array $context = []): Collection
    {
        if (empty($articleIds)) {
            return collect();
        }

        $options = collect();

        $hasOnline = Size::query()
            ->whereHas('references', fn ($q) => $q->whereIn('id_article', $articleIds)
                ->where('dispo_en_ligne', true))
            ->exists();

        if ($hasOnline) {
            $options->push(new FilterOptionDTO(id: 'online', label: 'Disponible en ligne'));
        }

        $existingShopStatuses = ShopAvailability::query()
            ->select('statut')
            ->distinct()
            ->whereHas('reference', fn ($q) => $q->whereIn('id_article', $articleIds))
            ->whereIn('statut', [ShopAvailability::STATUS_IN_STOCK, ShopAvailability::STATUS_ORDERABLE])
            ->pluck('statut')
            ->toArray();

        if (in_array(ShopAvailability::STATUS_IN_STOCK, $existingShopStatuses)) {
            $options->push(new FilterOptionDTO(id: 'in_stock', label: 'En stock magasin'));
        }

        if (in_array(ShopAvailability::STATUS_ORDERABLE, $existingShopStatuses)) {
            $options->push(new FilterOptionDTO(id: 'orderable', label: 'Commandable en magasin'));
        }

        return $options;
    }
}
