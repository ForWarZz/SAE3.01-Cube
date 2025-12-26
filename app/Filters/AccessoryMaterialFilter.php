<?php

namespace App\Filters;

use App\Models\AccessoryMaterial;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AccessoryMaterialFilter extends AbstractFilter
{
    protected string $key = 'accessory_material';

    public function apply(Builder $query, array $values): void
    {
        if (empty($values)) {
            return;
        }

        $query->whereHas('accessory', fn ($q) => $q->whereIn('id_matiere_accessoire', $values));
    }

    public function options(Builder $baseQuery, array $articleIds, array $context = []): Collection
    {
        $availableMaterials = $baseQuery->with('accessory')
            ->get()
            ->pluck('accessory.id_matiere_accessoire')
            ->unique()
            ->toArray();

        $materials = AccessoryMaterial::whereIn('id_matiere_accessoire', $availableMaterials)
            ->get();

        return $this->format(
            $materials,
            'id_matiere_accessoire',
            'nom_matiere_accessoire'
        );
    }
}
