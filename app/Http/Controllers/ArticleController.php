<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Bike;
use App\Models\BikeModel;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
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

        // Default: show article (if not a bike)
        abort(404);
    }
}
