<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\BikeModel;
use App\Models\Category;
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
        $articles = $this->articleService->searchArticles($search);

        return response()->json($articles);
    }

    public function viewByCategory(Category $category)
    {
        $articles = Article::whereIn('id_categorie', $category->getAllChildrenIds())->paginate(15);

        return view("article.index", [
            'articles' => $articles,
            'pageTitle' => $category->nom_categorie,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('home')],
                ['label' => $category->nom_categorie, 'url' => null],
            ]
        ]);
    }

    public function viewByModel(BikeModel $bikeModel)
    {
        $articles = Article::query()->whereHas('bike', function($query) use ($bikeModel) {
            $query->where('id_modele_velo', '=', $bikeModel->id_modele_velo);
        })->paginate(15);

        return view("article.index", [
            'articles' => $articles,
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
