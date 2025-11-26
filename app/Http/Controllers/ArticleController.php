<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Categorie;
use App\Models\Velo;
use App\Models\ModeleVelo;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function viewByCat(Categorie $categorie)
    {
        $articles = Article::whereIn('id_categorie', $categorie->allChildren())->paginate(15);

        // TODO: Refaire le breadcrumb recursivement
        return view("article.index", [
            'articles' => $articles,
            'pageTitle' => $categorie->nom_categorie,
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('home')],
                ['label' => $categorie->nom_categorie, 'url' => null],
            ]
        ]);
    }

    public function viewByModel(ModeleVelo $model)
    {
        $articles = Article::query()->whereHas('velo', function($query) use ($model) {
            $query->where('id_modele_velo', '=', $model->id_modele_velo);
        })->paginate(15);

        // TODO: Refaire le breadcrumb recursivement
        return view("article.index", [
            'articles' => $articles,
            'pageTitle' => $model->nom_modele_velo,
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('home')],
                ['label' => $model->nom_modele_velo, 'url' => null],
            ]
        ]);
    }

    public function show(Article $article)
    {
        $article->load(['velo', 'accessoires']);

        if ($article->velo()->exists()) {
            return redirect()->route(
                'articles.bikes.redirect-to-default',
                ['bike' => $article->velo]
            );
        }
    }
}
