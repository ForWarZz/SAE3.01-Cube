<?php

namespace App\Http\Controllers;

use App\Models\Article;
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

        return view('article.index', [
            'pageTitle' => $model->nom_modele_velo,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('home')],
                ['label' => $model->nom_modele_velo, 'url' => null],
            ],
            ...$data,
        ]);
    }

    public function viewByCategory(Category $category, Request $request)
    {
        $data = $this->articleService->listByCategory($category, $request);

        return view('article.index', [
            'pageTitle' => $category->nom_categorie,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('home')],
                ['label' => $category->nom_categorie, 'url' => null],
            ],
            ...$data,
        ]);
    }

    public function show(Article $article)
    {
        $article->load(['bike', 'accessories']);

        if ($article->bike()->exists()) {
            return redirect()->route(
                'articles.bikes.redirect-to-default',
                ['bike' => $article->bike]
            );
        }
    }
}
