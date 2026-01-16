<?php

namespace App\DTOs\Article;

use App\Models\Article;
use Illuminate\Support\Collection;

class ArticleViewDataDTO
{
    public function __construct(
        public readonly Article $article,
        public readonly Collection $sizeOptions,
        public readonly ?SizeOptionDTO $currentSize,
        public readonly Collection $availableSizes,
        public readonly float $realPrice,
        public readonly float $discountedPrice,
        public readonly bool $hasDiscount,
        public readonly ?int $discountPercent,
        public readonly Collection $characteristics,
        public readonly ?string $description,
        public readonly ?string $resume,
        public readonly Collection $similarArticles,
        public readonly bool $isBike,
        public readonly array $breadcrumbs,
        public readonly ?float $weight,
        public readonly array $additionalData = [],
    ) {}

    public function toArray(): array
    {
        return [
            'article' => $this->article,
            'sizeOptions' => $this->sizeOptions,
            'currentSize' => $this->currentSize,
            'availableSizes' => $this->availableSizes,
            'realPrice' => $this->realPrice,
            'discountedPrice' => $this->discountedPrice,
            'hasDiscount' => $this->hasDiscount,
            'discountPercent' => $this->discountPercent,
            'characteristics' => $this->characteristics,
            'description' => $this->description,
            'resume' => $this->resume,
            'similarArticles' => $this->similarArticles,
            'isBike' => $this->isBike,
            'breadcrumbs' => $this->breadcrumbs,
            'weight' => $this->weight,
            ...$this->additionalData,
        ];
    }
}
