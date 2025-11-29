<?php

namespace App\Services;

use App\Filters\ArticleFilter;
use App\Filters\AvailabilityFilter;
use App\Filters\BikeModelFilter;
use App\Filters\ColorFilter;
use App\Filters\FrameFilter;
use App\Filters\MaterialFilter;
use App\Filters\PromotionFilter;
use App\Filters\UsageFilter;
use App\Filters\VintageFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FilterEngineService
{
    /**
     * @var array{
     *     class-string<ArticleFilter>
     * }
     */
    private array $filterClasses = [
        VintageFilter::class,
        FrameFilter::class,
        ColorFilter::class,
        UsageFilter::class,
        MaterialFilter::class,
        BikeModelFilter::class,
        PromotionFilter::class,
        AvailabilityFilter::class,
    ];

    /**
     * @var array<ArticleFilter>
     */
    private array $filters;

    public function __construct()
    {
        $this->filters = array_map(fn($class) => new $class, $this->filterClasses);
    }

    public function retrieveSelectedFilters(Request $request): array
    {
        $selected = [];

        foreach ($this->filters as $filter) {
            $selected[$filter->key()] = $filter->values($request);
        }

        return $selected;
    }

    public function apply(Builder $query, array $selectedFilters): Builder
    {
        foreach ($this->filters as $filter) {
            $filter->apply($query, $selectedFilters[$filter->key()] ?? []);
        }

        return $query;
    }

    public function getFilterOptions(Builder $query): array
    {
        $opts = [];

        foreach ($this->filters as $filter) {
            $opts[$filter->key()] = $filter->options((clone $query));
        }

        return $opts;
    }
}
