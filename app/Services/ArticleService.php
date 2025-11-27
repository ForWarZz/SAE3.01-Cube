<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ArticleService
{
    public function searchArticles(string $search, string $sortBy = 'name_asc'): Collection
    {
        $query = Article::query()->select('id_article', 'nom_article', 'prix_article', 'id_categorie');

        if (empty($search)) {
            $this->applySorting($query, $sortBy);
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

        $this->applySorting($query, $sortBy);
        return $query->get();
    }


    public function applySorting($query, $sortBy)
    {
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('prix_article', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('prix_article', 'desc');
                break;
            case 'reference_asc':
                $query->orderBy('id_article', 'asc');
                break;
            case 'reference_desc':
                $query->orderBy('id_article', 'desc');
                break;
            case 'name_desc':
                $query->orderBy('nom_article', 'desc');
                break;
            case 'name_asc':
            default:
                $query->orderBy('nom_article', 'asc');
                break;
        }

        return $query;
    }
}
