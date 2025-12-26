<?php

namespace App\Filters;

use App\DTOs\Filter\FilterOptionDTO;
use App\Models\ShopAvailability;
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
            foreach ($values as $value) {
                switch ($value) {
                    case 'online':
                        $q->orWhereHas('bike.references.availableSizes', fn ($q2) => $q2->where('dispo_en_ligne', true))
                            ->orWhereHas('accessory.availableSizes', fn ($q2) => $q2->where('dispo_en_ligne', true));
                        break;

                    case 'in_stock':
                        $q->orWhereHas('bike.references.shopAvailabilities', fn ($q2) => $q2->where('statut', ShopAvailability::STATUS_IN_STOCK))
                            ->orWhereHas('accessory.shopAvailabilities', fn ($q2) => $q2->where('statut', ShopAvailability::STATUS_IN_STOCK));
                        break;

                    case 'orderable':
                        $q->orWhereHas('bike.references.shopAvailabilities', fn ($q2) => $q2->where('statut', ShopAvailability::STATUS_ORDERABLE))
                            ->orWhereHas('accessory.shopAvailabilities', fn ($q2) => $q2->where('statut', ShopAvailability::STATUS_ORDERABLE));
                        break;
                }
            }
        });
    }

    public function options(Builder $baseQuery, array $articleIds, array $context = []): Collection
    {
        $options = collect();

        // Check online availability
        // On encapsule la logique vélo OU accessoire dans un groupe ->where(function($q){...})
        $hasOnline = (clone $baseQuery)
            ->where(function ($q) {
                $q->whereHas('bike.references.availableSizes', fn ($q2) => $q2->where('dispo_en_ligne', true))
                    ->orWhereHas('accessory.availableSizes', fn ($q2) => $q2->where('dispo_en_ligne', true));
            })
            ->exists();

        if ($hasOnline) {
            $options->push(new FilterOptionDTO(id: 'online', label: 'Disponible en ligne'));
        }

        // Check in stock
        $hasStock = (clone $baseQuery)
            ->where(function ($q) {
                $q->whereHas('bike.references.shopAvailabilities', fn ($q2) => $q2->where('statut', ShopAvailability::STATUS_IN_STOCK))
                    ->orWhereHas('accessory.shopAvailabilities', fn ($q2) => $q2->where('statut', ShopAvailability::STATUS_IN_STOCK));
            })
            ->exists();

        if ($hasStock) {
            $options->push(new FilterOptionDTO(id: 'in_stock', label: 'En stock magasin'));
        }

        // Check orderable
        $hasOrderable = (clone $baseQuery)
            ->where(function ($q) {
                $q->whereHas('bike.references.shopAvailabilities', fn ($q2) => $q2->where('statut', ShopAvailability::STATUS_ORDERABLE))
                    ->orWhereHas('accessory.shopAvailabilities', fn ($q2) => $q2->where('statut', ShopAvailability::STATUS_ORDERABLE));
            })
            ->exists();

        if ($hasOrderable) {
            $options->push(new FilterOptionDTO(id: 'orderable', label: 'Commandable en magasin'));
        }

        return $options;
    }
}
