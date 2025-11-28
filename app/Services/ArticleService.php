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

    public function applyFilters($query, array $filters)
    {
        // Only apply bike-related filters if at least one bike filter is set
        $hasBikeFilters = !empty($filters['millesime']) || !empty($filters['materiau']) || !empty($filters['usage']);
        $hasReferenceFilters = !empty($filters['cadre']) || !empty($filters['couleur']);

        // Filter by millesime (vintage) - supports multiple values
        if (!empty($filters['millesime'])) {
            $millesimes = is_array($filters['millesime']) ? $filters['millesime'] : [$filters['millesime']];
            $query->where(function($q) use ($millesimes) {
                $q->whereHas('bike', function ($subq) use ($millesimes) {
                    $subq->whereIn('id_millesime', $millesimes);
                });
            });
        }

        // Filter by cadre (frame type) - supports multiple values
        if (!empty($filters['cadre'])) {
            $cadres = is_array($filters['cadre']) ? $filters['cadre'] : [$filters['cadre']];
            $query->where(function($q) use ($cadres) {
                $q->whereHas('bike', function($bikeQuery) use ($cadres) {
                    $bikeQuery->whereHas('references', function ($refQuery) use ($cadres) {
                        $refQuery->whereIn('id_cadre_velo', $cadres);
                    });
                });
            });
        }

        // Filter by max price
        if (!empty($filters['prix_max'])) {
            $query->where('prix_article', '<=', $filters['prix_max']);
        }

        // Filter by material - supports multiple values
        if (!empty($filters['materiau'])) {
            $materiaux = is_array($filters['materiau']) ? $filters['materiau'] : [$filters['materiau']];
            $query->where(function($q) use ($materiaux) {
                $q->whereHas('bike', function ($subq) use ($materiaux) {
                    $subq->whereIn('id_materiau_cadre', $materiaux);
                });
            });
        }

        // Filter by color - supports multiple values
        if (!empty($filters['couleur'])) {
            $couleurs = is_array($filters['couleur']) ? $filters['couleur'] : [$filters['couleur']];
            $query->where(function($q) use ($couleurs) {
                $q->whereHas('bike', function($bikeQuery) use ($couleurs) {
                    $bikeQuery->whereHas('references', function ($refQuery) use ($couleurs) {
                        $refQuery->whereIn('id_couleur', $couleurs);
                    });
                });
            });
        }

        // Filter by usage - supports multiple values
        if (!empty($filters['usage'])) {
            $usages = is_array($filters['usage']) ? $filters['usage'] : [$filters['usage']];
            $query->where(function($q) use ($usages) {
                $q->whereHas('bike', function ($subq) use ($usages) {
                    $subq->whereIn('id_usage', $usages);
                });
            });
        }

        // Filter by promotion
        if (!empty($filters['promotion'])) {
            $query->where('pourcentage_remise', '>', 0);
        }

        // Filter by max weight (poids) - specifically "Poids du vélo"
        if (!empty($filters['poids_max'])) {
            $poidsCharacteristic = \App\Models\Characteristic::where('nom_caracteristique', 'Poids du vélo')->first();
            if ($poidsCharacteristic) {
                $query->whereExists(function ($subquery) use ($filters, $poidsCharacteristic) {
                    $subquery->select(\Illuminate\Support\Facades\DB::raw(1))
                        ->from('caracterise')
                        ->whereColumn('caracterise.id_article', 'article.id_article')
                        ->where('caracterise.id_caracteristique', $poidsCharacteristic->id_caracteristique)
                        ->whereRaw("CAST(REPLACE(REGEXP_REPLACE(caracterise.valeur_caracteristique, '[^0-9,.]', '', 'g'), ',', '.') AS DECIMAL) <= ?", [$filters['poids_max']]);
                });
            }
        }

        return $query;
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
