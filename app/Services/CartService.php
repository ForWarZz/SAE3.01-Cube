<?php

namespace App\Services;

use App\Models\Accessory;
use App\Models\Article;
use App\Models\ArticleReference;
use App\Models\BikeReference;
use App\Models\DiscountCode;
use App\Models\Size;
use Illuminate\Support\Facades\Session;

class CartService
{
    private string $cart = 'cart';

    private string $discount = 'discount_code';

    public function __construct() {}

    public function getCartFromSession(): array
    {
        return Session::get($this->cart, []);
    }

    /**
     * @return array{
     *     cartData: array{
     *         reference: BikeReference|Accessory,
     *         img_url: string,
     *         size: Size,
     *         quantity: int,
     *         article: Article,
     *     },
     *     summaryData: array{
     *        subtotal: float,
     *        tax: float,
     *        total: float,
     *     },
     *     count: int,
     * }
     */
    public function getCartData(): array
    {
        $cartItems = $this->getCartFromSession();

        $cartData = [];
        $summaryData = [
            'subtotal' => 0,
            'discount' => 0,
        ];

        $discountData = $this->getAppliedDiscountCode();

        foreach ($cartItems as $item) {
            $reference = ArticleReference::with(['article', 'bikeReference', 'bikeReference.color'])->find($item['reference_id']);
            $size = Size::find($item['size_id']);
            $article = $reference->bikeReference->article ?? $reference->accessory->article;

            $cartData[] = [
                'reference' => $reference->bikeReference ?? $reference->accessory,
                'img_url' => $article->getCoverUrl($reference->bikeReference?->color->id_couleur ?? null),
                'size' => $size,
                'quantity' => $item['quantity'],
                'article' => $article,
                'color' => $reference->bikeReference?->color->label_couleur,
                'article_url' => route('articles.show', [
                    'reference' => $reference->id_reference,
                    'article' => $article->id_article,
                ]),
            ];

            $summaryData['subtotal'] += $article->prix_article * $item['quantity'];
        }

        if ($discountData) {
            $summaryData['discount'] = $summaryData['subtotal'] * ($discountData->pourcentage_remise / 100);
        }

        $summaryData['shipping'] = $summaryData['subtotal'] > 50 ? 0 : ($summaryData['subtotal'] == 0 ? 0 : 6);
        $totalTTC = $summaryData['subtotal'] - $summaryData['discount'] + $summaryData['shipping'];

        $summaryData['tax'] = $totalTTC * 0.20;
        $summaryData['total'] = $totalTTC;

        return [
            'cartData' => $cartData,
            'summaryData' => $summaryData,
            'discountData' => $discountData,
            'count' => count($cartData),
        ];
    }

    public function addItem(int $reference_id, int $size_id): void
    {
        $cart = $this->getCartFromSession();
        $key = $reference_id.'_'.$size_id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += 1;
        } else {
            $cart[$key] = [
                'reference_id' => $reference_id,
                'size_id' => $size_id,
                'quantity' => 1,
            ];
        }

        Session::put($this->cart, $cart);
    }

    public function removeItem(int $reference_id, int $size_id): void
    {
        $cart = $this->getCartFromSession();
        $key = $reference_id.'_'.$size_id;

        if (isset($cart[$key])) {
            unset($cart[$key]);

            Session::put($this->cart, $cart);
        }
    }

    public function updateItemQuantity(mixed $reference_id, mixed $size_id, mixed $quantity): void
    {
        $cart = $this->getCartFromSession();
        $key = $reference_id.'_'.$size_id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $quantity;

            Session::put($this->cart, $cart);
        }
    }

    public function applyDiscountCode(string $code): void
    {
        $discountCode = DiscountCode::where('label_code_promo', $code)
            ->where('est_actif', true)
            ->first();

        Session::put($this->discount, $discountCode->id_code_promo);
    }

    public function getAppliedDiscountCode(): ?DiscountCode
    {
        $discountId = Session::get($this->discount);

        if (! $discountId) {
            return null;
        }

        return DiscountCode::find($discountId);
    }

    public function removeDiscountCode(): void
    {
        Session::forget($this->discount);
    }
}
