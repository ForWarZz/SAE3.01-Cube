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
    )
    { }

    public function search(Request $request)
    {
        $search = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'name_asc');
        $articles = $this->articleService->searchArticles($search, $sortBy);

        return view('article.search', [
            'articles' => $articles,
            'search' => $search,
            'sortBy' => $sortBy,
            'pageTitle' => 'Résultats de recherche : ' . $search,
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('home')],
                ['label' => 'Recherche', 'url' => null],
            ]
        ]);
    }

    public function viewByCategory(Category $category)
    {
        $sortBy = request()->input('sort_by', 'name_asc');
        $query = Article::whereIn('id_categorie', $category->getAllChildrenIds());

        $query = $this->articleService->applySorting($query, $sortBy);
        $articles = $query->paginate(15)->appends(['sort_by' => $sortBy]);

        return view("article.index", [
            'articles' => $articles,
            'sortBy' => $sortBy,
            'pageTitle' => $category->nom_categorie,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('home')],
                ['label' => $category->nom_categorie, 'url' => null],
            ]
        ]);
    }

    public function viewByModel(BikeModel $bikeModel)
    {
        $sortBy = request()->input('sort_by', 'name_asc');
        $query = Article::query()->whereHas('bike', function($query) use ($bikeModel) {
            $query->where('id_modele_velo', '=', $bikeModel->id_modele_velo);
        });

        $query = $this->articleService->applySorting($query, $sortBy);
        $articles = $query->paginate(15)->appends(['sort_by' => $sortBy]);

        return view("article.index", [
            'articles' => $articles,
            'sortBy' => $sortBy,
            'pageTitle' => $bikeModel->nom_modele_velo,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('home')],
                ['label' => $bikeModel->nom_modele_velo, 'url' => null],
            ]
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
