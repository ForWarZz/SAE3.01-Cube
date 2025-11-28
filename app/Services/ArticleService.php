<?php

namespace App\Services;

use App\Models\Article;
use App\Models\BikeModel;
use App\Models\Category;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ArticleService
{
    public function __construct(
        protected FilterEngineService $filterEngineService,
    )
    { }

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

        $query = Article::query()->select([
            'id_article',
            'nom_article',
            'prix_article',
            'id_categorie'
        ]);

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

        $data = $this->finalizeQuery($query, $request);

        return [
            'search' => $search,
            ...$data
        ];
    }

    /**
     * @param BikeModel $model
     * @param Request $request
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
        $articles = Article::whereHas('bikes', function ($query) use ($model) {
            $query->where('id_modele_velo', '=', $model->id_modele_velo);
        });

        return $this->finalizeQuery($articles, $request);
    }

    /**
     * @param Category $category
     * @param Request $request
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
        $baseQuery = Article::whereIn('id_categorie', $category->getAllChildrenIds());

        return $this->finalizeQuery($baseQuery, $request);
    }

    /**
     * @param $baseQuery
     * @param Request $request
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
        $sortBy = $request->input('sortBy');
        $filtersSelected = $this->filterEngineService->retrieveSelectedFilters($request);

        $query = $this->filterEngineService->apply($baseQuery, $filtersSelected);
        $filterOptions = $this->filterEngineService->getFilterOptions($baseQuery);

        $this->applySorting($query, $sortBy);
        $articles = $query
            ->paginate(15)
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
            'reference_asc' => 'Référence (croissant)',
            'reference_desc' => 'Référence (décroissant)',
        ];
    }
}
