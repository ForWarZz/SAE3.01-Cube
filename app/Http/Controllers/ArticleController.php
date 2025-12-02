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
        $article->load(['bike', 'accessory', 'bike.references']);

        if ($article->bike) {
            $defaultReference = $article->bike->references()->orderBy('id_reference')->firstOrFail();

            return redirect()->route('articles.show-reference', [
                'article' => $article->id_article,
                'reference' => $defaultReference->id_reference,
            ]);
        }

        return redirect()->route('articles.show-reference', [
            'article' => $article->id_article,
            'reference' => $article->accessory->id_reference,
        ]);
    }

    public function showByRef(Article $article, ArticleReference $reference)
    {
        $data = $this->articleService->prepareViewData($article, $reference);

        return view('article.show', $data);
    }
}
