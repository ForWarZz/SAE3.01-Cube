<?php

namespace App\Services;

use App\DTOs\Article\ArticleListResultDTO;
use App\DTOs\Article\ArticleSearchResultDTO;
use App\DTOs\Article\ArticleViewDataDTO;
use App\DTOs\Article\SizeOptionDTO;
use App\Models\Article;
use App\Models\ArticleReference;
use App\Models\BikeModel;
use App\Models\Category;
use App\Models\ShopAvailability;
use Illuminate\Support\Collection;

class ArticleService
{
    public function __construct(
        protected FilterEngineService $filterEngineService,
        protected BikeService $bikeService,
        protected AccessoryService $accessoryService,
        protected BreadCrumbService $breadCrumbService,
    ) {}

    public function searchArticles(string $search, ?string $sortBy = null, array $filters = [], int $page = 1): ArticleSearchResultDTO
    {
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

        $listResult = $this->finalizeQuery($query, $sortBy, $filters, $page);

        return new ArticleSearchResultDTO(
            search: $search,
            listResult: $listResult,
        );
    }

    private function finalizeQuery($baseQuery, ?string $sortBy, array $filters, int $page): ArticleListResultDTO
    {
        $perPage = config('article.per_page');

        $filtersSelected = $this->filterEngineService->retrieveSelectedFilters($filters);

        $filterOptions = $this->filterEngineService->getFilterOptions($baseQuery);
        $query = $this->filterEngineService->apply(clone $baseQuery, $filtersSelected);

        $this->applySorting($query, $sortBy);

        $articles = $query->paginate($perPage, ['*'], 'page', $page);

        return new ArticleListResultDTO(
            articles: $articles,
            activeFilters: $filtersSelected,
            filterOptions: $filterOptions,
            sortBy: $sortBy ?? 'name_asc',
            sortOptions: $this->getSortOptions(),
        );
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

    public function listByModel(BikeModel $model, ?string $sortBy = null, array $filters = [], int $page = 1): ArticleListResultDTO
    {
        $baseQuery = Article::whereHas('bike', function ($q) use ($model) {
            $q->where('id_modele_velo', $model->id_modele_velo);
        })->with(['bike.bikeModel', 'bike.references', 'category']);

        return $this->finalizeQuery($baseQuery, $sortBy, $filters, $page);
    }

    public function listByCategory(Category $category, ?string $sortBy = null, array $filters = [], int $page = 1): ArticleListResultDTO
    {
        $baseQuery = Article::query()
            ->whereIn('id_categorie', $category->getAllChildrenIds())
            ->with(['bike.bikeModel', 'bike.references', 'category', 'accessory']);
        $this->filterEngineService->setContext(['category' => $category]);

        return $this->finalizeQuery($baseQuery, $sortBy, $filters, $page);
    }

    public function prepareViewData(ArticleReference $reference, ?int $sizeId): ArticleViewDataDTO
    {
        $article = $reference->article;
        $sizes = $reference->availableSizes;

        if (! $sizeId) {
            $sizeId = $sizes->first()?->id_taille;
        }

        $sizeOptions = $this->buildSizeOptions($reference, $sizeId);

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

        $additionalData = [];

        if ($article->bike) {
            $bike = $article->bike;
            $bikeReference = $reference->bikeReference;
            $additionalData = $this->bikeService->prepareBikeData($bike, $bikeReference);
        } else {
            $additionalData = $this->accessoryService->prepareAccessoryData($article->accessory);
        }

        return new ArticleViewDataDTO(
            article: $article,
            sizeOptions: $sizeOptions,
            currentSize: $sizeOptions->where('id', $sizeId)->first(),
            availableSizes: $reference->availableSizes,
            realPrice: $article->prix_article,
            discountedPrice: $article->getDiscountedPrice(),
            hasDiscount: $article->hasDiscount(),
            discountPercent: $article->pourcentage_remise,
            characteristics: $article->characteristics->groupBy('characteristicType.nom_type_carac'),
            description: $article->description_article,
            resume: $article->resumer_article,
            similarArticles: $article->similar,
            isBike: $article->bike !== null,
            breadcrumbs: $this->breadCrumbService->prepareBreadcrumbs($article->category),
            weight: $article->poids_article,
            additionalData: $additionalData,
        );
    }

    private function buildSizeOptions(ArticleReference $reference, ?int $sizeId): Collection
    {
        $sizeList = $reference->availableSizes;
        $allShopAvailabilities = $reference->shopAvailabilities
            ->groupBy('pivot.id_taille');

        return $sizeList->map(function ($size) use ($sizeId, $reference, $allShopAvailabilities) {
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
                url: route('articles.show-reference', [
                    'reference' => $reference->id_reference,
                    'size' => $size->id_taille,
                ]),
                label: $size->label,
                availableOnline: $availableOnline ?? false,
                shopStatus: $shopStatus,
                active: $sizeId == $size->id_taille,
            );
        });
    }
}
