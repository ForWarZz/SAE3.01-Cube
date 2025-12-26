<?php

namespace App\DTOs\Cart;

use App\Models\BaseArticle;
use App\Models\Size;

class CartItemDTO
{
    public function __construct(
        public object $reference,
        public string $img_url,
        public Size $size,
        public int $quantity,
        public BaseArticle $article,
        public float $price_per_unit,
        public float $real_price,
        public bool $has_discount,
        public ?int $discount_percent,
        public ?string $color,
        public string $article_url,
    ) {}

    public function getLineTotal(): float
    {
        return $this->price_per_unit * $this->quantity;
    }

    public function toArray(): array
    {
        return [
            'reference' => $this->reference,
            'img_url' => $this->img_url,
            'size' => $this->size,
            'quantity' => $this->quantity,
            'article' => $this->article,
            'price_per_unit' => $this->price_per_unit,
            'real_price' => $this->real_price,
            'has_discount' => $this->has_discount,
            'discount_percent' => $this->discount_percent,
            'color' => $this->color,
            'article_url' => $this->article_url,
        ];
    }
}
