<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleReference;
use App\Models\BikeModel;
use App\Models\Category;
use App\Services\ArticleService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(
        protected ArticleService $articleService,
    ) {}

    public function search(Request $request)
    {
        $data = $this->articleService->searchArticles($request);
        $search = $data['search'];

        return view('article.index', [
            'search' => $search,
            'pageTitle' => 'Résultats de recherche : '.$search,
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('home')],
                ['label' => 'Recherche', 'url' => null],
            ],
            ...$data,
        ]);
    }

    public function viewByModel(BikeModel $model, Request $request)
    {
        $data = $this->articleService->listByModel($model, $request);
        $category = $model->bikes->first()?->category;

        $breadcrumbs = $this->prepareBreadcrumbs($category);
        $breadcrumbs[] = [
            'label' => $model->nom_modele_velo, 'url' => route('articles.by-model', ['model' => $model->id_modele_velo]),
        ];

        return view('article.index', [
            'pageTitle' => $model->nom_modele_velo,
            'breadcrumbs' => $breadcrumbs,
            ...$data,
        ]);
    }

    public function viewByCategory(Category $category, Request $request)
    {
        $data = $this->articleService->listByCategory($category, $request);
        $breadcrumbs = $this->prepareBreadcrumbs($category);

        return view('article.index', [
            'pageTitle' => $category->nom_categorie,
            'breadcrumbs' => $breadcrumbs,
            ...$data,
        ]);
    }

    public function show(Article $article)
    {
        if ($article->bike()->exists()) {
            $defaultReference = $article->bike->references()->orderBy('id_reference')->firstOrFail();

            return redirect()->route('articles.show-reference', [
                'article' => $article->id_article,
                'reference' => $defaultReference->id_reference,
            ]);
        }

        $article->load('accessory');

        return redirect()->route('articles.show-reference', [
            'article' => $article->id_article,
            'reference' => $article->accessory->id_reference,
        ]);
    }

    public function showByRef(Article $article, ArticleReference $reference)
    {
        $article->load([
            'bike.bikeModel',
            'bike.vintage',
            'bike.frameMaterial',
            'bike.usage',
            'accessory',
            'characteristics.characteristicType',
            'category',
        ]);

        $reference->load([
            'bikeReference.bike.bikeModel',
            'bikeReference.color',
            'bikeReference.frame',
            'bikeReference.ebike.battery',
            'bikeReference.baseReference.availableSizes',
            'availableSizes',
        ]);

        $data = $this->articleService->prepareViewData($article, $reference);

        return view('article.show', $data);
    }

    private function prepareBreadcrumbs(Category $category): array
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

    private function buildCategoryUrl(Category $category): string
    {
        return route('articles.by-category', ['category' => $category->id_categorie]);
    }
}
