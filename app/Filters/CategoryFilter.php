<?php

namespace App\Filters;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CategoryFilter extends AbstractFilter
{
    public string $key = 'category';

    public function apply(Builder $query, array $values): void
    {
        if (empty($values)) {
            return;
        }

        $allCategoryIds = collect($values)
            ->flatMap(function ($categoryId) {
                $category = Category::find($categoryId);

                return $category ? $category->getAllChildrenIds() : [];
            })
            ->unique()
            ->toArray();

        if (! empty($allCategoryIds)) {
            $query->whereIn('id_categorie', $allCategoryIds);
        }
    }

    public function options(Builder $baseQuery, array $articleIds, array $context = []): Collection
    {
        if (! isset($context['category'])) {
            return collect();
        }

        $currentCategory = $context['category'];
        $currentId = $currentCategory->id_categorie;

        $activeLeafIds = Article::whereIn('id_article', $articleIds)
            ->distinct()
            ->pluck('id_categorie')
            ->toArray();

        if (empty($activeLeafIds)) {
            return collect();
        }

        $activeCategories = Category::with('parentRecursive')
            ->whereIn('id_categorie', $activeLeafIds)
            ->get();

        $validChildIds = [];

        foreach ($activeCategories as $cat) {
            $iterator = $cat;

            while ($iterator) {
                if ($iterator->id_categorie_parent == $currentId) {
                    $validChildIds[] = $iterator->id_categorie;
                    break;
                }

                if (! $iterator->parentRecursive || $iterator->id_categorie == $currentId) {
                    break;
                }

                $iterator = $iterator->parentRecursive;
            }
        }

        $validChildIds = array_unique($validChildIds);

        if (empty($validChildIds)) {
            return collect();
        }

        return $this->format(
            Category::whereIn('id_categorie', $validChildIds)->orderBy('nom_categorie')->get(),
            'id_categorie',
            'nom_categorie'
        );
    }
}
