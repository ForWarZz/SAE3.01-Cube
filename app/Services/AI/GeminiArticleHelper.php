<?php

namespace App\Services\AI;

use App\Models\Article;
use App\Models\ArticleReference;
use App\Models\BikeReference;
use App\Models\Category;

class GeminiArticleHelper
{
    /**
     * Recherche d'articles selon des critères variés
     * L'IA peut utiliser cette fonction pour chercher des produits
     */
    public function searchArticles(array $criteria): array
    {
        $query = Article::query()
            ->with([
                'category',
                'bike.bikeModel',
                'bike.usage',
                'bike.ebike',
                'bike.frameMaterial',
                'accessory.material',
            ]);

        // Recherche par mots-clés
        if (! empty($criteria['query'])) {
            $keywords = array_filter(explode(' ', trim($criteria['query'])));
            foreach ($keywords as $word) {
                $term = "%$word%";
                $query->where(function ($q) use ($term) {
                    $q->where('nom_article', 'ILIKE', $term)
                        ->orWhere('resumer_article', 'ILIKE', $term)
                        ->orWhere('description_article', 'ILIKE', $term)
                        ->orWhereHas('bike.bikeModel', function ($q2) use ($term) {
                            $q2->where('nom_modele_velo', 'ILIKE', $term);
                        });
                });
            }
        }

        // Filtrage par catégories (inclut les sous-catégories)
        if (! empty($criteria['category_ids'])) {
            $categories = Category::whereIn('id_categorie', $criteria['category_ids'])
                ->get()
                ->load('childrenRecursive');

            $allCategoryIds = [];
            foreach ($categories as $category) {
                $allCategoryIds[] = $category->id_categorie;
                $descendants = $category->getAllChildrenIds();
                $allCategoryIds = array_merge($allCategoryIds, $descendants);
            }

            $allCategoryIds = array_unique($allCategoryIds);
            $query->whereIn('id_categorie', $allCategoryIds);
        }

        // Filtrage par prix
        if (isset($criteria['min_price'])) {
            $query->where('prix_article', '>=', $criteria['min_price']);
        }
        if (isset($criteria['max_price'])) {
            $query->where('prix_article', '<=', $criteria['max_price']);
        }

        // Filtrage par promotion
        if (! empty($criteria['has_discount'])) {
            $query->where('pourcentage_remise', '>', 0);
        }

        // Filtrage vélo/accessoire
        if (isset($criteria['is_bike'])) {
            if ($criteria['is_bike'] === true) {
                $query->whereHas('bike');
            } elseif ($criteria['is_bike'] === false) {
                $query->whereHas('accessory');
            }
        }

        // Filtrage vélo électrique
        if (isset($criteria['is_ebike'])) {
            if ($criteria['is_ebike'] === true) {
                $query->whereHas('bike.ebike');
            } elseif ($criteria['is_ebike'] === false) {
                $query->whereHas('bike')->whereDoesntHave('bike.ebike');
            }
        }

        // Filtrage par usage
        if (! empty($criteria['usage_ids'])) {
            $query->whereHas('bike', function ($q) use ($criteria) {
                $q->whereIn('id_usage', $criteria['usage_ids']);
            });
        }

        // Filtrage par matériau du cadre
        if (! empty($criteria['frame_material_ids'])) {
            $query->whereHas('bike', function ($q) use ($criteria) {
                $q->whereIn('id_materiau_cadre', $criteria['frame_material_ids']);
            });
        }

        // Tri des résultats
        $sortBy = $criteria['sort_by'] ?? 'relevance';
        switch ($sortBy) {
            case 'price_asc':
                $query->orderBy('prix_article', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('prix_article', 'desc');
                break;
            case 'sales':
                $query->orderBy('nombre_vente_article', 'desc');
                break;
            case 'name_desc':
                $query->orderBy('nom_article', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('nom_article', 'asc');
                break;
            case 'relevance':
            default:
                $query->orderByRaw('CASE WHEN pourcentage_remise > 0 THEN 0 ELSE 1 END')
                    ->orderBy('nombre_vente_article', 'desc');
                break;
        }

        $limit = min($criteria['limit'] ?? 10, 50);
        $articles = $query->limit($limit)->get();

        return [
            'success' => true,
            'count' => $articles->count(),
            'articles' => $articles->map(function ($article) {
                $data = [
                    'id_article' => $article->id_article,
                    'name' => $article->nom_article,
                    'price' => $article->prix_article,
                    'discounted_price' => $article->getDiscountedPrice(),
                    'has_discount' => $article->hasDiscount(),
                    'discount_percentage' => $article->pourcentage_remise,
                    'summary' => $article->resumer_article,
                    'category' => $article->category?->nom_categorie,
                    'sales_count' => $article->nombre_vente_article,
                    'weight' => $article->poids_article,
                    'url' => route('articles.show', $article->id_article),
                ];

                if ($article->relationLoaded('bike') && $article->bike) {
                    $bike = $article->bike;
                    $data['type'] = 'bike';
                    $data['is_ebike'] = $bike->ebike !== null;
                    $data['model'] = $bike->bikeModel?->nom_modele_velo;
                    $data['usage'] = $bike->usage?->label_usage;
                    $data['frame_material'] = $bike->frameMaterial?->label_materiau_cadre;
                }

                if ($article->relationLoaded('accessory') && $article->accessory) {
                    $data['type'] = 'accessory';
                    $data['material'] = $article->accessory->material?->nom_matiere_accessoire;
                }

                return $data;
            })->toArray(),
        ];
    }

    /**
     * Récupère les détails complets d'une référence d'article
     * Utilisé quand l'utilisateur est sur une fiche produit spécifique
     */
    public function getReferenceDetails(int $referenceId): array
    {
        $reference = ArticleReference::with([
            'article.category',
            'article.characteristics.characteristicType',
            'bikeReference.color',
            'bikeReference.frame',
            'bikeReference.ebike.battery',
            'bikeReference.bike.bikeModel',
            'bikeReference.bike.usage',
            'bikeReference.bike.frameMaterial',
            'accessory.material',
        ])->findOrFail($referenceId);

        $article = $reference->article;

        $data = [
            'id_reference' => $reference->id_reference,
            'id_article' => $article->id_article,
            'name' => $article->nom_article,
            'description' => $article->description_article,
            'summary' => $article->resumer_article,
            'price' => $article->prix_article,
            'discounted_price' => $article->getDiscountedPrice(),
            'has_discount' => $article->hasDiscount(),
            'discount_percentage' => $article->pourcentage_remise,
            'category' => $article->category?->nom_categorie,
            'weight' => $article->poids_article,
            'sales_count' => $article->nombre_vente_article,
            'characteristics' => $article->characteristics->map(fn ($charac) => [
                'type' => $charac->characteristicType->nom_type_carac,
                'name' => $charac->nom_caracteristique,
                'value' => $charac->pivot->valeur_caracteristique,
            ])->toArray(),
        ];

        // Informations spécifiques aux vélos
        if ($reference->bikeReference) {
            $bikeRef = $reference->bikeReference;
            $data['type'] = 'bike';
            $data['color'] = $bikeRef->color?->label_couleur;
            $data['frame'] = $bikeRef->frame?->label_cadre_velo;

            if ($bikeRef->bike) {
                $data['model'] = $bikeRef->bike->bikeModel?->nom_modele_velo;
                $data['usage'] = $bikeRef->bike->usage?->label_usage;
                $data['frame_material'] = $bikeRef->bike->frameMaterial?->label_materiau_cadre;
            }

            if ($bikeRef->ebike) {
                $data['is_ebike'] = true;
                $data['battery'] = $bikeRef->ebike->battery?->label_batterie;
            } else {
                $data['is_ebike'] = false;
            }
        }

        // Informations spécifiques aux accessoires
        if ($reference->accessory) {
            $data['type'] = 'accessory';
            $data['material'] = $reference->accessory->material?->nom_matiere_accessoire;
        }

        return $data;
    }

    /**
     * Récupère les disponibilités en ligne et en magasin pour une référence
     * Essentiel pour répondre aux questions sur la disponibilité
     */
    public function getReferenceAvailability(int $referenceId): array
    {
        $reference = ArticleReference::with([
            'availableSizes',
            'shopAvailabilities.city',
        ])->findOrFail($referenceId);

        // Disponibilités en ligne par taille
        $onlineAvailability = $reference->availableSizes->map(function ($size) {
            return [
                'id_taille' => $size->id_taille,
                'size_label' => $size->nom_taille,
                'available_online' => (bool) $size->pivot->dispo_en_ligne,
            ];
        })->toArray();

        // Disponibilités en magasin par taille et magasin
        //        $shopAvailability = $reference->shopAvailabilities
        //            ->groupBy('id_magasin')
        //            ->map(function ($availabilities, $shopId) {
        //                $shop = $availabilities->first();
        //
        //                return [
        //                    'id_magasin' => $shopId,
        //                    'shop_name' => $shop->nom_magasin,
        //                    'shop_city' => $shop->city?->nom_ville,
        //                    'sizes' => $availabilities->map(function ($availability) {
        //                        return [
        //                            'id_taille' => $availability->pivot->id_taille,
        //                            'status' => $availability->pivot->statut,
        //                        ];
        //                    })->toArray(),
        //                ];
        //            })
        //            ->values()
        //            ->toArray();

        $shopAvailabilities = $reference->shopAvailabilities->map(function ($shopAvailability) {
            return [
                'id_magasin' => $shopAvailability->id_magasin,
                'shop_name' => $shopAvailability->nom_magasin,
                'shop_city' => $shopAvailability->city?->nom_ville,
                'id_taille' => $shopAvailability->pivot->id_taille,
                'status' => $shopAvailability->pivot->statut,
            ];
        });

        return [
            'id_reference' => $referenceId,
            'online_availability' => $onlineAvailability,
            'shop_availabilities' => $shopAvailabilities,
        ];
    }

    /**
     * Récupère toutes les variantes disponibles pour un article
     * Utile pour comparer les options (couleurs, tailles, etc.)
     */
    public function getArticleVariants(int $articleId): array
    {
        $article = Article::with([
            'bike.references.color',
            'bike.references.frame',
            'bike.references.ebike.battery',
            'bike.references.baseReference',
        ])->findOrFail($articleId);

        if (! $article->bike) {
            return [
                'success' => false,
                'message' => 'Cet article ne possède pas de variantes',
            ];
        }

        $variants = $article->bike->references->map(function (BikeReference $reference) {
            return [
                'id_reference' => $reference->id_reference,
                'color' => $reference->color?->label_couleur,
                'frame' => $reference->frame?->label_cadre_velo,
                'battery' => $reference->ebike?->battery?->label_batterie,
                'price' => $reference->article->prix_article,
                'discounted_price' => $reference->article->getDiscountedPrice(),
                'has_discount' => $reference->article->hasDiscount(),
            ];
        })->toArray();

        return [
            'success' => true,
            'id_article' => $articleId,
            'article_name' => $article->nom_article,
            'variants_count' => count($variants),
            'variants' => $variants,
        ];
    }

    /**
     * Récupère les articles similaires pour des recommandations
     */
    public function getSimilarArticles(int $articleId): array
    {
        $article = Article::with('similar')->findOrFail($articleId);

        return [
            'success' => true,
            'articles' => $article->similar->map(fn ($similar) => [
                'id_article' => $similar->id_article,
                'name' => $similar->nom_article,
                'price' => $similar->prix_article,
                'discounted_price' => $similar->getDiscountedPrice(),
                'has_discount' => $similar->hasDiscount(),
                'summary' => $similar->resumer_article,
                'url' => route('articles.show', $similar->id_article),
            ])->toArray(),
        ];
    }

    /**
     * Récupère les accessoires compatibles avec un vélo
     */
    public function getCompatibleAccessories(int $articleId): array
    {
        $article = Article::with(['bike.compatibleAccessories'])->findOrFail($articleId);

        if (! $article->bike) {
            return [
                'success' => false,
                'message' => 'Cet article n\'est pas un vélo',
            ];
        }

        return [
            'success' => true,
            'accessories' => $article->bike->compatibleAccessories->map(fn ($accessory) => [
                'id_article' => $accessory->id_article,
                'name' => $accessory->nom_article,
                'price' => $accessory->prix_article,
                'discounted_price' => $accessory->getDiscountedPrice(),
                'has_discount' => $accessory->hasDiscount(),
                'summary' => $accessory->resumer_article,
                'category' => $accessory->category?->nom_categorie,
                'url' => route('articles.show', $accessory->id_article),
            ])->toArray(),
        ];
    }

    /**
     * Liste les catégories disponibles pour aider l'IA à filtrer
     */
    public function getCategories(): array
    {
        $categories = Category::with('childrenRecursive')
            ->whereNull('id_categorie_parent')
            ->get();

        return [
            'success' => true,
            'categories' => $this->formatCategoriesTree($categories),
        ];
    }

    /**
     * Formatte l'arbre de catégories de manière récursive
     */
    private function formatCategoriesTree($categories): array
    {
        return $categories->map(function ($category) {
            $data = [
                'id_categorie' => $category->id_categorie,
                'name' => $category->nom_categorie,
            ];

            if ($category->childrenRecursive && $category->childrenRecursive->isNotEmpty()) {
                $data['children'] = $this->formatCategoriesTree($category->childrenRecursive);
            }

            return $data;
        })->toArray();
    }
}
