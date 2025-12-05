<?php

namespace App\Services;

use App\Models\Article;
use App\Models\BikeModel;
use App\Models\Category;

class BreadCrumbService
{
    public function prepareBreadcrumbs(Category $category): array
    {
        $breadcrumbs = [
            ['label' => 'Accueil', 'url' => route('home')],
        ];

        $ancestors = $category->getAncestors();

        foreach ($ancestors as $ancestor) {
            $breadcrumbs[] = [
                'label' => $ancestor->nom_categorie,
                'url' => $this->buildCategoryUrl($ancestor),
            ];
        }

        $breadcrumbs[] = [
            'label' => $category->nom_categorie,
            'url' => $this->buildCategoryUrl($category),
        ];

        return $breadcrumbs;
    }

    public function prepareBreadcrumbsByModel(BikeModel $model): array
    {
        $category = $model->bikes->first()?->category;

        return $this->prepareBreadcrumbs($category);
    }

    public function prepareBreadcrumbsForArticle(Article $article): array
    {
        $breadcrumbs = $this->prepareBreadcrumbs($article->category);

        if ($article->bike) {
            $breadcrumbs[] = [
                'label' => $article->bike->bikeModel->nom_modele_velo,
                'url' => route('articles.by-model', ['model' => $article->bike->bikeModel->id_modele_velo]),
            ];
        }

        $breadcrumbs[] = [
            'label' => $article->nom_article,
            'url' => null,
        ];

        return $breadcrumbs;
    }

    private function buildCategoryUrl(Category $category): string
    {
        return route('articles.by-category', ['category' => $category->id_categorie]);
    }
}
