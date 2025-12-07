<?php

namespace App\Filters;

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

        //        $allCategoryIds = [];
        //        foreach ($values as $categoryId) {
        //            $category = Category::find($categoryId);
        //
        //            if ($category) {
        //                $allCategoryIds = array_merge($allCategoryIds, $category->getAllChildrenIds());
        //            }
        //        }

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

    public function options(Builder $baseQuery, array $context = []): Collection
    {
        if (! isset($context['category'])) {
            return collect();
        }

        $currentCategory = $context['category'];

        $availableArticleIds = $baseQuery->pluck('id_article')->toArray();
        $directChildren = Category::where('id_categorie_parent', $currentCategory->id_categorie)->get();

        $validChildren = $directChildren->filter(function ($child) use ($availableArticleIds) {
            $ids = collect([$child->id_categorie])
                ->merge($child->getAllChildrenIds())
                ->toArray();

            return Category::whereIn('id_categorie', $ids)
                ->whereHas('articles', function ($q) use ($availableArticleIds) {
                    $q->whereIn('id_article', $availableArticleIds);
                })
                ->exists();
        })->values();

        return $this->format(
            $validChildren,
            'id_categorie',
            'nom_categorie'
        );
    }
}
