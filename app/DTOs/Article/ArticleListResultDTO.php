<?php

namespace App\DTOs\Article;

use Illuminate\Pagination\LengthAwarePaginator;

class ArticleListResultDTO
{
    public function __construct(
        public readonly LengthAwarePaginator $articles,
        public readonly array $activeFilters,
        public readonly array $filterOptions,
        public readonly string $sortBy,
        public readonly array $sortOptions,
    ) {}

    public function toArray(): array
    {
        return [
            'articles' => $this->articles,
            'activeFilters' => $this->activeFilters,
            'filterOptions' => $this->filterOptions,
            'sortBy' => $this->sortBy,
            'sortOptions' => $this->sortOptions,
        ];
    }
}
