<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ArticleService
{

    /**
     * @param string $search
     * @return Article[]
     */
    public function searchArticles(string $search): array
    {
        $query = Article::query();

        if (empty($search)) {
            return $query->get();
        }

        $keywords = explode(' ', $search);

        $query
            ->with(['categorie', 'velo.modeleVelo', 'caracteristiques', ''])
            ->where(function (Builder $mainQuery) use ($keywords) {
                foreach ($keywords as $word) {
                    $term = "%$word%";

                    $mainQuery->where(function (Builder $subQuery) use ($term) {
                        $subQuery->where('nom_article', 'ILIKE', $term)
                            ->orWhere('description_article', 'ILIKE', $term)
                            ->orWhere('resumer_article', 'ILIKE', $term)
                            ->orWhere('nom_categorie', 'ILIKE', $term)
                            ->orWhere('valeur_caracteristique', 'ILIKE', $term);

                        $subQuery->orWhereHas('velo', function ($q) use ($term) {
                            $q->where('nom_modele_velo', 'ILIKE', $term)
                                ->orWhere('label_materiau_cadre', 'ILIKE', $term);
                        });
                    });
                }
            });

        return $query->get();
    }
}
