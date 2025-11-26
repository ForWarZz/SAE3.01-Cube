<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Categorie;
use App\Models\Velo;
use App\Models\ModeleVelo;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function viewByCat(Categorie $categorie){
        $articles = Article::whereIn('id_categorie', $categorie->allChildren());

        return view("article.index", [
            'articles' => $articles->paginate(15)
        ]);
    }

    public function viewByModel(ModeleVelo $model){
        $articles = Article::query()->whereHas('velo', function($query) use ($model) {
            $query->where('id_modele_velo', '=', $model->id_modele_velo);
        });

        return view("article.index", [
            'articles' => $articles->paginate(15)
        ]);
    }
}
