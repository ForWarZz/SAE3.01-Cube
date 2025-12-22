<?php

namespace App\Services\Commercial\Accessory;

use App\Models\Accessory;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CommercialAccessoryService
{
    public function getPaginatedAccessories($perPage = 10): LengthAwarePaginator
    {
        return Accessory::with([
            'category',
            'material',
        ])->paginate($perPage);
    }

    /**
     * @return Collection<int, Category>
     */
    public function getAccessoryCategories(): Collection
    {
        $accessoryCategory = Category::find(Category::ACCESSORY_CATEGORY_ID);
        $accessoryChildrenIds = $accessoryCategory?->getAllChildrenIds() ?? [];

        return Category::with(['parent', 'children'])
            ->whereDoesntHave('children')
            ->whereIn('id_categorie', $accessoryChildrenIds)
            ->get()
            ->sortBy(fn (Category $cat) => $cat->getFullPath(), SORT_NATURAL);
    }

    public function updateAccessory(Accessory $accessory, array $validated): void
    {
        DB::select('CALL update_accessoire(?, ?, ?, ?, ?, ?, ?, ?)', [
            $accessory->id_article,
            $validated['nom_article'],
            $validated['description_article'],
            $validated['resumer_article'],
            $validated['prix_article'],
            $validated['pourcentage_remise'],
            $validated['id_categorie'],
            $validated['id_matiere_accessoire'],
        ]);
    }
}
