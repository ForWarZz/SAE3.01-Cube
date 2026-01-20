<?php

namespace App\Services\Cart;

use App\DTOs\Cart\CartItemDTO;
use App\DTOs\Cart\CartSummaryDTO;
use App\DTOs\Cart\CartViewDataDTO;
use App\DTOs\Cart\ShippingModeDTO;
use App\Models\ArticleReference;
use App\Models\DiscountCode;
use App\Models\ShippingMode;
use App\Models\Size;
use Illuminate\Support\Collection;

class CartService
{
    private const FREE_SHIPPING_THRESHOLD = 50;

    private const STANDARD_DELIVERY_PRICE = 6;

    private const CLICK_AND_COLLECT_ID = 1;

    public function __construct(
        protected readonly CartSessionManager $sessionManager,
    ) {}

    public function addItem(int $referenceId, int $sizeId): void
    {
        $this->sessionManager->addItem($referenceId, $sizeId);
    }

    public function updateQuantity(int $referenceId, int $sizeId, int $quantity): void
    {
        $this->sessionManager->updateQuantity($referenceId, $sizeId, $quantity);
    }

    public function removeItem(int $referenceId, int $sizeId): void
    {
        $this->sessionManager->removeItem($referenceId, $sizeId);
    }

    public function clear(): void
    {
        $this->sessionManager->clearCart();
    }

    public function isEmpty(): bool
    {
        return $this->sessionManager->isEmpty();
    }

    public function applyDiscountCode(string $code): bool
    {
        $discountCode = DiscountCode::where('label_code_promo', $code)->first();

        if (! $discountCode) {
            return false;
        }

        $this->sessionManager->setDiscountCodeId($discountCode->id_code_promo);

        return true;
    }

    public function removeDiscountCode(): void
    {
        $this->sessionManager->clearDiscountCode();
    }

    public function getCartData(?int $shippingModeId = null): CartViewDataDTO
    {
        $sessionItems = $this->sessionManager->getItems();

        if (empty($sessionItems)) {
            return $this->createEmptyCartData();
        }

        $cartItems = $this->loadCartItems($sessionItems);

        if ($cartItems->isEmpty()) {
            return $this->createEmptyCartData();
        }

        $subtotal = $this->calculateSubtotal($cartItems);
        $hasBikes = $this->containsBikes($cartItems);

        $discountCode = $this->getAppliedDiscountCode();
        $discountAmount = $this->calculateDiscount($subtotal, $discountCode);

        $shippingPrice = $shippingModeId
            ? $this->calculateShippingPrice($shippingModeId, $subtotal)
            : 0;

        $totalTTC = $subtotal - $discountAmount + $shippingPrice;
        $tax = $totalTTC - ($totalTTC / 1.20);

        return new CartViewDataDTO(
            items: $cartItems,
            summary: new CartSummaryDTO(
                subtotal: $subtotal,
                discount: $discountAmount,
                shipping: $shippingPrice,
                tax: $tax,
                total: $totalTTC,
            ),
            discountCode: $discountCode,
            count: $cartItems->count(),
            hasBikes: $hasBikes,
        );
    }

    /**
     * @return Collection<int, ShippingModeDTO>
     */
    public function getAvailableShippingModes(): Collection
    {
        $cartData = $this->getCartData();
        $subtotal = $cartData->summary->subtotal;
        $hasBikes = $cartData->hasBikes;

        $availableModes = ShippingMode::query()
            ->whereNot('id_moyen_livraison', 2)
            ->when($hasBikes, fn ($q) => $q->where('id_moyen_livraison', self::CLICK_AND_COLLECT_ID))
            ->get();

        return $availableModes->map(function ($mode) use ($subtotal) {
            $price = $this->calculateShippingPrice($mode->id_moyen_livraison, $subtotal);

            return new ShippingModeDTO(
                id: $mode->id_moyen_livraison,
                name: $mode->label_moyen_livraison,
                price: $price,
            );
        });
    }

    private function calculateShippingPrice(int $shippingModeId, float $subtotal): float
    {
        if ($shippingModeId === self::CLICK_AND_COLLECT_ID && $subtotal >= self::FREE_SHIPPING_THRESHOLD) {
            return 0;
        }

        return self::STANDARD_DELIVERY_PRICE;
    }

    private function createEmptyCartData(): CartViewDataDTO
    {
        return new CartViewDataDTO(
            items: collect(),
            summary: new CartSummaryDTO(
                subtotal: 0,
                discount: 0,
                shipping: 0,
                tax: 0,
                total: 0,
            ),
            discountCode: null,
            count: 0,
            hasBikes: false,
        );
    }

    /**
     * @return Collection<int, CartItemDTO>
     */
    private function loadCartItems(array $sessionItems): Collection
    {
        $referenceIds = array_column($sessionItems, 'reference_id');
        $sizeIds = array_column($sessionItems, 'size_id');

        $references = ArticleReference::whereIn('id_reference', $referenceIds)
            ->with(['article', 'bikeReference.color', 'accessory'])
            ->get()
            ->keyBy('id_reference');

        $sizes = Size::whereIn('id_taille', $sizeIds)
            ->get()
            ->keyBy('id_taille');

        $cartItems = collect();

        foreach ($sessionItems as $sessionItem) {
            $reference = $references->get($sessionItem['reference_id']);
            $size = $sizes->get($sessionItem['size_id']);

            if (! $reference || ! $size) {
                $this->sessionManager->removeItem($sessionItem['reference_id'], $sessionItem['size_id']);

                continue;
            }

            $article = $reference->article;

            $cartItems->push(new CartItemDTO(
                reference: $reference->variant(),
                img_url: $article->getCoverUrl($reference->id_reference),
                size: $size,
                quantity: $sessionItem['quantity'],
                article: $article,
                price_per_unit: $article->getDiscountedPrice(),
                real_price: $article->prix_article,
                has_discount: $article->hasDiscount(),
                discount_percent: $article->pourcentage_remise,
                color: $reference->bikeReference?->color->label_couleur,
                article_url: route('articles.show', [
                    'reference' => $reference->id_reference,
                    'article' => $article->id_article,
                ]),
            ));
        }

        return $cartItems;
    }

    private function calculateSubtotal(Collection $cartItems): float
    {
        return $cartItems->sum(fn (CartItemDTO $item) => $item->price_per_unit * $item->quantity);
    }

    private function containsBikes(Collection $cartItems): bool
    {
        return $cartItems->contains(fn (CartItemDTO $item) => $item->reference->bike !== null);
    }

    private function calculateDiscount(float $subtotal, ?DiscountCode $discountCode): float
    {
        if (! $discountCode) {
            return 0;
        }

        return $subtotal * ($discountCode->pourcentage_remise / 100);
    }

    private function getAppliedDiscountCode(): ?DiscountCode
    {
        $discountId = $this->sessionManager->getDiscountCodeId();

        if (! $discountId) {
            return null;
        }

        return DiscountCode::find($discountId);
    }
}
