<?php

namespace App\Services;

use App\Filters\AccessoryMaterialFilter;
use App\Filters\ArticleFilter;
use App\Filters\AvailabilityFilter;
use App\Filters\BikeModelFilter;
use App\Filters\CategoryFilter;
use App\Filters\ColorFilter;
use App\Filters\DiscountFilter;
use App\Filters\FrameFilter;
use App\Filters\MaterialFilter;
use App\Filters\PriceFilter;
use App\Filters\SizeFilter;
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
        //        VintageFilter::class,
        //        FrameFilter::class,
        //        ColorFilter::class,
        //        UsageFilter::class,
        //        MaterialFilter::class,
        //        BikeModelFilter::class,
        //        DiscountFilter::class,
        //        AvailabilityFilter::class,
        //        PriceFilter::class,
        //        CategoryFilter::class,
        //        AccessoryMaterialFilter::class,
        //        SizeFilter::class,
    ];

    /**
     * @var array<ArticleFilter>
     */
    private array $filters;

    private array $context = [];

    public function __construct()
    {
        $this->filters = array_map(fn ($class) => new $class, $this->filterClasses);
    }

    public function setContext(array $context): self
    {
        $this->context = $context;

        return $this;
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
            $opts[$filter->key()] = $filter->options((clone $query), $this->context);
        }

        return $opts;
    }
}
