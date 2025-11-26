<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ArticleService
{
    public function searchArticles(string $search): Collection
    {
        $query = Article::query()->select('id_article', 'nom_article', 'prix_article', 'id_categorie');

        if (empty($search)) {
            return $query->with(['category', 'bike.bikeModel'])->get();
        }

        $keywords = explode(' ', trim($search));
        $keywords = array_filter($keywords);

        $query
            ->with(['category', 'bike.bikeModel'])
            ->where(function (Builder $mainQuery) use ($keywords) {
                foreach ($keywords as $word) {
                    $term = "%{$word}%";

                    $mainQuery->orWhere('nom_article', 'ILIKE', $term)
                        ->orWhere('description_article', 'ILIKE', $term)
                        ->orWhere('resumer_article', 'ILIKE', $term)
                        ->orWhereHas('category', function ($q) use ($term) {
                            $q->where('nom_categorie', 'ILIKE', $term);
                        })
                        ->orWhereHas('bike.bikeModel', function ($q) use ($term) {
                            $q->where('nom_modele_velo', 'ILIKE', $term);
                        });
                }
            });

        return $query->get();
    }
}
