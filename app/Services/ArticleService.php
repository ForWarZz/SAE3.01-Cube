<?php

namespace App\Services;

use App\DTOs\Article\SizeOptionDTO;
use App\Models\Article;
use App\Models\ArticleReference;
use App\Models\BikeModel;
use App\Models\Category;
use App\Models\ShopAvailability;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ArticleService
{
    public function __construct(
        protected FilterEngineService $filterEngineService,
        protected BikeService $bikeService,
        protected AccessoryService $accessoryService,
        protected BreadCrumbService $breadCrumbService,
    ) {}

    /**
     * @return array{
     *     results: Collection<Article>,
     *     search: string,
     *     sortBy: string,
     *     filterOptions: array,
     *     activeFilters: array,
     *     sortBy: string,
     *     sortOptions: array
     * }
     */
    public function searchArticles(Request $request): array
    {
        $search = $request->input('search', '');

        $query = Article::query()
//            ->whereHas('bike')
            ->with(['bike.bikeModel', 'bike.references', 'category', 'accessory']);

        $keywords = explode(' ', trim($search));
        $keywords = array_filter($keywords);

        foreach ($keywords as $word) {
            $term = "%$word%";

            $query->where(function ($q) use ($term) {
                $q->orWhere('nom_article', 'ILIKE', $term)
                    ->orWhere('resumer_article', 'ILIKE', $term)
                    ->orWhere('description_article', 'ILIKE', $term)
                    ->orWhereHas('category', function ($q2) use ($term) {
                        $q2->where('nom_categorie', 'ILIKE', $term);
                    })
                    ->orWhereHas('bike.bikeModel', function ($q2) use ($term) {
                        $q2->where('nom_modele_velo', 'ILIKE', $term);
                    });
            });
        }

        $data = $this->finalizeQuery($query, $request);

        return [
            'search' => $search,
            ...$data,
        ];
    }

    /**
     * @return array{
     *     articles: LengthAwarePaginator,
     *     activeFilters: array,
     *     filterOptions: array,
     *     sortBy: string,
     *     sortOptions: array
     * }
     */
    private function finalizeQuery($baseQuery, Request $request): array
    {
        $perPage = config('article.per_page');

        $sortBy = $request->input('sortBy');
        $filtersSelected = $this->filterEngineService->retrieveSelectedFilters($request);

        $query = $this->filterEngineService->apply($baseQuery, $filtersSelected);
        $filterOptions = $this->filterEngineService->getFilterOptions($query);

        $this->applySorting($query, $sortBy);
        $articles = $query
            ->paginate($perPage)
            ->appends($request->except('page'));

        return [
            'articles' => $articles,
            'activeFilters' => $filtersSelected,
            'filterOptions' => $filterOptions,
            'sortBy' => $sortBy,
            'sortOptions' => $this->getSortOptions(),
        ];
    }

    private function applySorting($query, $sortBy): void
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
            case 'selling_desc':
                $query->orderBy('nombre_vente_article', 'desc');
                break;
            case 'name_desc':
                $query->orderBy('nom_article', 'desc');
                break;
            case 'name_asc':
            default:
                $query->orderBy('nom_article', 'asc');
                break;
        }
    }

    private function getSortOptions(): array
    {
        return [
            'name_asc' => 'Nom (A-Z)',
            'name_desc' => 'Nom (Z-A)',
            'price_asc' => 'Prix (croissant)',
            'price_desc' => 'Prix (décroissant)',
            'selling_desc' => 'Meilleures ventes (décroissant)',
            'reference_asc' => 'Référence (croissant)',
            'reference_desc' => 'Référence (décroissant)',
        ];
    }

    /**
     * @return array{
     *     articles: LengthAwarePaginator,
     *     activeFilters: array,
     *     filterOptions: array,
     *     sortBy: string,
     *     sortOptions: array
     * }
     */
    public function listByModel(BikeModel $model, Request $request): array
    {
        $baseQuery = Article::whereHas('bike', function ($q) use ($model) {
            $q->where('id_modele_velo', $model->id_modele_velo);
        })->with(['bike.bikeModel', 'bike.references', 'category']);

        return $this->finalizeQuery($baseQuery, $request);
    }

    /**
     * @return array{
     *     articles: LengthAwarePaginator,
     *     activeFilters: array,
     *     filterOptions: array,
     *     sortBy: string,
     *     sortOptions: array
     * }
     */
    public function listByCategory(Category $category, Request $request): array
    {
        $baseQuery = Article::query()
            ->whereIn('id_categorie', $category->getAllChildrenIds())
            ->with(['bike.bikeModel', 'bike.references', 'category', 'accessory']);
        $this->filterEngineService->setContext(['category' => $category]);

        return $this->finalizeQuery($baseQuery, $request);
    }

    public function prepareViewData(ArticleReference $reference): array
    {
        $article = $reference->article;
        $sizeOptions = $this->buildSizeOptions($reference);

        if ($article->bike) {
            $reference->loadMissing([
                'article.bike.bikeModel.geometries.characteristic',
                'article.bike.bikeModel.geometries.size',

                'article.bike.vintage',
                'article.bike.usage',
                'article.bike.frameMaterial',

                'article.bike.references.frame',
                'article.bike.references.color',
                'article.bike.references.ebike.battery',

                'article.bike.compatibleAccessories.category',

                'bikeReference.color',
                'bikeReference.frame',
                'bikeReference.ebike.battery',
            ]);
        } else {
            $reference->loadMissing([
                'article.accessory',
            ]);
        }

        $base = [
            'article' => $article,
            'sizeOptions' => $sizeOptions,

            'availableSizes' => $reference->availableSizes,

            'realPrice' => $article->prix_article,
            'discountedPrice' => $article->getDiscountedPrice(),
            'hasDiscount' => $article->hasDiscount(),
            'discountPercent' => $article->pourcentage_remise,

            'characteristics' => $article->characteristics
                ->groupBy('characteristicType.nom_type_carac'),

            'description' => $article->description_article,
            'resume' => $article->resumer_article,

            'similarArticles' => $article->similar,

            'isBike' => false,
            'breadcrumbs' => $this->breadCrumbService->prepareBreadcrumbs($article->category),

            'weight' => $article->poids_article,
        ];

        if ($article->bike) {
            $bike = $article->bike;
            $bikeReference = $reference->bikeReference;

            $bikeData = $this->bikeService->prepareBikeData($bike, $bikeReference);

            return array_merge($base, $bikeData);
        }

        $accessoryData = $this->accessoryService->prepareAccessoryData($article->accessory);

        return array_merge($base, $accessoryData);
    }

    /**
     * Build size options for current reference
     *
     * @return Collection<int, SizeOptionDTO>
     */
    public function buildSizeOptions(ArticleReference $reference): Collection
    {
        $sizeList = $reference->availableSizes;
        $allShopAvailabilities = $reference->shopAvailabilities
            ->groupBy('pivot.id_taille');

        return $sizeList->map(function ($size) use ($allShopAvailabilities) {
            $availableOnline = $size->pivot->dispo_en_ligne;
            $storeStatuses = $allShopAvailabilities->get($size->id_taille, collect())
                ->pluck('pivot.statut');

            if ($storeStatuses->contains(ShopAvailability::STATUS_IN_STOCK)) {
                $shopStatus = SizeOptionDTO::SHOP_STATUS_IN_STOCK;
            } elseif ($storeStatuses->contains(ShopAvailability::STATUS_ORDERABLE)) {
                $shopStatus = SizeOptionDTO::SHOP_STATUS_ORDERABLE;
            } else {
                $shopStatus = SizeOptionDTO::SHOP_STATUS_UNAVAILABLE;
            }

            return new SizeOptionDTO(
                id: $size->id_taille,
                label: $size->label,
                availableOnline: $availableOnline,
                shopStatus: $shopStatus,
                disabled: ! $availableOnline && $shopStatus === SizeOptionDTO::SHOP_STATUS_UNAVAILABLE,
            );
        });
    }
}
