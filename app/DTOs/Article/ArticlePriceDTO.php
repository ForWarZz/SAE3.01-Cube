<?php

namespace App\DTOs\Article;

use App\Models\Article;

class ArticlePriceDTO
{
    public function __construct(
        public float $realPrice,
        public float $discountedPrice,
        public bool $hasDiscount,
        public ?int $discountPercent,
    ) {}

    public static function fromArticle(Article $article): self
    {
        return new self(
            realPrice: $article->prix_article,
            discountedPrice: $article->getDiscountedPrice(),
            hasDiscount: $article->hasDiscount(),
            discountPercent: $article->pourcentage_remise,
        );
    }

    public function getSavings(): float
    {
        return $this->realPrice - $this->discountedPrice;
    }

    public function toArray(): array
    {
        return [
            'realPrice' => $this->realPrice,
            'discountedPrice' => $this->discountedPrice,
            'hasDiscount' => $this->hasDiscount,
            'discountPercent' => $this->discountPercent,
        ];
    }
}
