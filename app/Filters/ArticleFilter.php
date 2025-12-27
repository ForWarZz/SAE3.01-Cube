<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface ArticleFilter
{
    public function key(): string;

    public function values(Request $request): array;

    public function apply(Builder $query, array $values): void;

    public function options(Builder $baseQuery, array $articleIds, array $context = []): Collection;
}
