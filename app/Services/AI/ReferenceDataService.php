<?php

namespace App\Services\AI;

use App\Models\AccessoryMaterial;
use App\Models\BikeFrameMaterial;
use App\Models\Category;
use App\Models\Usage;

class ReferenceDataService
{
    public function getReferenceData(): array
    {
        return [
            'categories' => $this->categories(),
            'usages' => $this->usages(),
            'bike_frame_materials' => $this->bikeFrameMaterials(),
            'accessory_materials' => $this->accessoryMaterials(),
        ];
    }

    private function categories(): array
    {
        return Category::whereNull('id_categorie_parent')
            ->with('childrenRecursive:id_categorie,id_categorie_parent,nom_categorie')
            ->get(['id_categorie', 'nom_categorie'])
            ->map(fn (Category $category) => $this->mapCategory($category))
            ->toArray();
    }

    private function mapCategory(Category $category): array
    {
        return [
            'id' => $category->id_categorie,
            'label' => $category->nom_categorie,
            'children' => $category->childrenRecursive
                ->map(fn ($child) => $this->mapCategory($child))
                ->values()
                ->toArray(),
        ];
    }

    private function usages(): array
    {
        return Usage::orderBy('label_usage')
            ->get(['id_usage', 'label_usage'])
            ->map(fn ($usage) => [
                'id' => $usage->id_usage,
                'label' => $usage->label_usage,
            ])
            ->toArray();
    }

    private function bikeFrameMaterials(): array
    {
        return BikeFrameMaterial::orderBy('label_materiau_cadre')
            ->get(['id_materiau_cadre', 'label_materiau_cadre'])
            ->map(fn ($material) => [
                'id' => $material->id_materiau_cadre,
                'label' => $material->label_materiau_cadre,
            ])
            ->toArray();
    }

    private function accessoryMaterials(): array
    {
        return AccessoryMaterial::orderBy('nom_matiere_accessoire')
            ->get(['id_matiere_accessoire', 'nom_matiere_accessoire'])
            ->map(fn ($material) => [
                'id' => $material->id_matiere_accessoire,
                'label' => $material->nom_matiere_accessoire,
            ])
            ->toArray();
    }
}
