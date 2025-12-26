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
        $data = $this->articleService->searchArticles($request);
        $search = $data['search'];

        return view('article.index', [
            'search' => $search,
            'pageTitle' => 'Résultats de recherche : '.$search,
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
            ...$data,
        ]);
    }

    public function viewByModel(BikeModel $model, Request $request)
    {
        $data = $this->articleService->listByModel($model, $request);
        $breadcrumbs = $this->breadCrumbService->prepareBreadcrumbsByModel($model);

        return view('article.index', [
            'pageTitle' => $model->nom_modele_velo,
            'breadcrumbs' => $breadcrumbs,
            ...$data,
        ]);
    }

    public function viewByCategory(Category $category, Request $request)
    {
        $data = $this->articleService->listByCategory($category, $request);
        $breadcrumbs = $this->breadCrumbService->prepareBreadcrumbs($category);

        return view('article.index', [
            'pageTitle' => $category->nom_categorie,
            'breadcrumbs' => $breadcrumbs,
            'currentCategory' => $category,
            ...$data,
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
        $reference = ArticleReference::withFullRelations()->findOrFail($referenceId);

        $data = $this->articleService->prepareViewData($reference);

        return view('article.show', $data);
    }
}
