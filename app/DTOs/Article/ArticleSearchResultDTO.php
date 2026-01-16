<?php

namespace App\DTOs\Article;

class ArticleSearchResultDTO extends ArticleListResultDTO
{
    public function __construct(
        public readonly string $search,
        ArticleListResultDTO $listResult,
    ) {
        parent::__construct(
            articles: $listResult->articles,
            activeFilters: $listResult->activeFilters,
            filterOptions: $listResult->filterOptions,
            sortBy: $listResult->sortBy,
            sortOptions: $listResult->sortOptions,
        );
    }

    public function toArray(): array
    {
        return [
            'search' => $this->search,
            ...parent::toArray(),
        ];
    }
}
