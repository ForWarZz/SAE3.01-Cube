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
        if (empty($articleIds)) {
            return collect();
        }

        $materials = AccessoryMaterial::select('matiere_accessoire.*')
            ->join('accessoire', 'matiere_accessoire.id_matiere_accessoire', '=', 'accessoire.id_matiere_accessoire')
            ->whereIn('accessoire.id_article', $articleIds)
            ->distinct()
            ->orderBy('matiere_accessoire.nom_matiere_accessoire')
            ->get();

        return $this->format(
            $materials,
            'id_matiere_accessoire',
            'nom_matiere_accessoire'
        );
    }
}
