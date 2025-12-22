<?php

namespace App\Services\Commercial\Accessory;

use App\Models\Accessory;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CommercialAccessoryService
{
    public function getPaginatedAccessories($perPage = 10): LengthAwarePaginator
    {
        return Accessory::paginate($perPage);
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
        $accessory->update($validated);
    }
}
