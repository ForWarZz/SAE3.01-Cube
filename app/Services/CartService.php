<?php

namespace App\Services;

use App\Models\Accessory;
use App\Models\Article;
use App\Models\ArticleReference;
use App\Models\BikeReference;
use App\Models\DeliveryMode;
use App\Models\DiscountCode;
use App\Models\Size;
use Illuminate\Support\Collection;
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
     *     hasBikes: bool,
     * }
     */
    public function getCartData(?float $shippingPrice = null): array
    {
        $cartItems = $this->getCartFromSession();

        if (empty($cartItems)) {
            return [
                'cartData' => [],
                'summaryData' => [
                    'subtotal' => 0,
                    'discount' => 0,
                    'shipping' => 0,
                    'tax' => 0,
                    'total' => 0,
                ],
                'discountData' => null,
                'count' => 0,
                'hasBikes' => false,
            ];
        }

        // Load all references and sizes at once
        $referenceIds = array_column($cartItems, 'reference_id');
        $sizeIds = array_column($cartItems, 'size_id');

        $references = ArticleReference::with([
            'bikeReference.color',
            'bikeReference.article',
            'accessory.article',
        ])->whereIn('id_reference', $referenceIds)->get()->keyBy('id_reference');

        $sizes = Size::whereIn('id_taille', $sizeIds)->get()->keyBy('id_taille');

        $cartData = [];
        $summaryData = [
            'subtotal' => 0,
            'discount' => 0,
        ];

        $discountData = $this->getAppliedDiscountCode();
        $hasBikes = false;

        foreach ($cartItems as $item) {
            $reference = $references->get($item['reference_id']);
            $size = $sizes->get($item['size_id']);

            // Remove invalid items from cart automatically
            if (! $reference || ! $size) {
                $this->removeItem($item['reference_id'], $item['size_id']);

                continue;
            }

            /** @var Article $article */
            $article = $reference->bikeReference->article ?? $reference->accessory->article;

            $cartData[] = [
                'reference' => $reference->bikeReference ?? $reference->accessory,
                'img_url' => $article->getCoverUrl($reference->bikeReference?->color->id_couleur ?? null),
                'size' => $size,
                'quantity' => $item['quantity'],
                'article' => $article,
                'price_per_unit' => $article->getDiscountedPrice(),
                'real_price' => $article->prix_article,
                'has_discount' => $article->hasDiscount(),
                'discount_percent' => $article->pourcentage_remise,
                'color' => $reference->bikeReference?->color->label_couleur,
                'article_url' => route('articles.show', [
                    'reference' => $reference->id_reference,
                    'article' => $article->id_article,
                ]),
            ];

            if ($reference->bikeReference && ! $hasBikes) {
                $hasBikes = true;
            }

            $summaryData['subtotal'] += $article->getDiscountedPrice() * $item['quantity'];
        }

        if ($discountData) {
            $summaryData['discount'] = $summaryData['subtotal'] * ($discountData->pourcentage_remise / 100);
        }

        if ($shippingPrice === null) {
            $availableShippingModes = $this->getAvailableShippingModes($summaryData['subtotal'], $hasBikes);
            $shippingPrice = $availableShippingModes->min('price') ?? 0;
        }

        $summaryData['shipping'] = $shippingPrice;
        $totalTTC = $summaryData['subtotal'] - $summaryData['discount'] + $summaryData['shipping'];

        $summaryData['tax'] = $totalTTC * 0.20;
        $summaryData['total'] = $totalTTC;

        return [
            'cartData' => $cartData,
            'summaryData' => $summaryData,
            'discountData' => $discountData,
            'count' => count($cartData),
            'hasBikes' => $hasBikes,
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

    /**
     * @return array{subtotal: float, hasBikes: bool}
     */
    private function calculateCartSubtotalAndBikes(): array
    {
        $cartItems = $this->getCartFromSession();
        $subtotal = 0;
        $hasBikes = false;

        if (empty($cartItems)) {
            return ['subtotal' => 0, 'hasBikes' => false];
        }

        $referenceIds = array_column($cartItems, 'reference_id');
        $references = ArticleReference::with([
            'bikeReference.article',
            'accessory.article',
        ])->whereIn('id_reference', $referenceIds)->get()->keyBy('id_reference');

        foreach ($cartItems as $item) {
            $reference = $references->get($item['reference_id']);
            if ($reference) {
                $article = $reference->bikeReference->article ?? $reference->accessory->article;
                $subtotal += $article->getDiscountedPrice() * $item['quantity'];

                if ($reference->bikeReference && ! $hasBikes) {
                    $hasBikes = true;
                }
            }
        }

        $discountData = $this->getAppliedDiscountCode();
        if ($discountData) {
            $discount = $subtotal * ($discountData->pourcentage_remise / 100);
            $subtotal -= $discount;
        }

        return ['subtotal' => $subtotal, 'hasBikes' => $hasBikes];
    }

    public function getAvailableShippingModes(?float $subtotal = null, ?bool $hasBikes = null): Collection
    {
        if ($subtotal === null || $hasBikes === null) {
            $calculated = $this->calculateCartSubtotalAndBikes();
            $subtotal = $calculated['subtotal'];
            $hasBikes = $calculated['hasBikes'];
        }

        $deliveryPrice = $subtotal >= 50 ? 0 : 6;

        return DeliveryMode::query()
            ->when($hasBikes, fn ($q) => $q->where('id_moyen_livraison', '=', 1))
            ->get()
            ->map(function ($mode) use ($deliveryPrice) {
                $price = $mode->id_moyen_livraison === 1 ? $deliveryPrice : 6;

                return [
                    'id' => $mode->id_moyen_livraison,
                    'name' => $mode->label_moyen_livraison,
                    'price' => $price,
                ];
            });
    }

    public function isCartEmpty(): bool
    {
        $cartItems = $this->getCartFromSession();

        return empty($cartItems);
    }
}
