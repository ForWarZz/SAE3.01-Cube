<?php

namespace App\Http\Controllers;

use App\DTOs\BreadcrumbDTO;
use App\Models\Article;
use App\Models\ArticleReference;
use App\Models\BikeModel;
use App\Models\Category;
use App\Services\ArticleService;
use App\Services\BreadCrumbService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(
        private readonly ArticleService $articleService,
        private readonly BreadCrumbService $breadCrumbService,
    ) {}

    public function search(Request $request)
    {
        $search = $request->input('search', '');

        if (empty($search)) {
            return redirect()->route('articles.by-category', Category::BIKE_CATEGORY_ID);
        }

        $result = $this->articleService->searchArticles(
            search: $search,
            sortBy: $request->input('sortBy'),
            filters: $request->except(['search', 'sortBy', 'page']),
            page: (int) $request->input('page', 1),
        );

        return view('article.index', [
            'pageTitle' => 'Résultats de recherche : '.$result->search,
            'breadcrumbs' => [
                new BreadcrumbDTO(
                    label: 'Accueil',
                    url: route('home'),
                ),
                new BreadcrumbDTO(
                    label: 'Recherche',
                    url: null,
                ),
            ],
            ...$result->toArray(),
        ]);
    }

    public function viewByModel(BikeModel $model, Request $request)
    {
        $result = $this->articleService->listByModel(
            model: $model,
            sortBy: $request->input('sortBy'),
            filters: $request->except(['sortBy', 'page']),
            page: (int) $request->input('page', 1),
        );
        $breadcrumbs = $this->breadCrumbService->prepareBreadcrumbsByModel($model);

        return view('article.index', [
            'pageTitle' => $model->nom_modele_velo,
            'breadcrumbs' => $breadcrumbs,
            ...$result->toArray(),
        ]);
    }

    public function viewByCategory(Category $category, Request $request)
    {
        $result = $this->articleService->listByCategory(
            category: $category,
            sortBy: $request->input('sortBy'),
            filters: $request->except(['sortBy', 'page']),
            page: (int) $request->input('page', 1),
        );
        $breadcrumbs = $this->breadCrumbService->prepareBreadcrumbs($category);

        return view('article.index', [
            'pageTitle' => $category->nom_categorie,
            'breadcrumbs' => $breadcrumbs,
            'currentCategory' => $category,
            ...$result->toArray(),
        ]);
    }

    public function show(Article $article)
    {
        if ($article->bike()->exists()) {
            $defaultReference = $article->bike->references()->orderBy('id_reference')->firstOrFail();

            return redirect()->route('articles.show-reference', [
                'reference' => $defaultReference->id_reference,
            ]);
        }

        $article->load('accessory');

        return redirect()->route('articles.show-reference', [
            'reference' => $article->accessory->id_reference,
        ]);
    }

    public function showByRef(int $referenceId)
    {
        $reference = ArticleReference::with([
            'article.characteristics.characteristicType',

            'article.similar.bike.references',
            'article.similar.accessory',
            'article.similar.category',

            'availableSizes',
            'shopAvailabilities',

            'article.category.parentRecursive',
            'article.bike',
        ])->findOrFail($referenceId);

        $sizeId = request()->query('size_id');
        $result = $this->articleService->prepareViewData($reference, $sizeId);

        return view('article.show', $result->toArray());
    }
}
