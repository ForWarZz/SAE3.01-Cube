<?php

namespace App\Services\Cart;

use App\DTOs\Cart\ShippingModeDTO;
use App\Models\Article;
use App\Models\ArticleReference;
use App\Models\DiscountCode;
use App\Models\ShippingMode;
use App\Models\Size;
use Illuminate\Support\Collection;

class CartService
{
    public function __construct(
        protected readonly CartSessionManager $session,
    ) {}

    public function addItem(int $referenceId, int $sizeId): void
    {
        $this->session->addItem($referenceId, $sizeId);
    }

    public function updateQuantity(int $referenceId, int $sizeId, int $quantity): void
    {
        $this->session->updateQuantity($referenceId, $sizeId, $quantity);
    }

    public function removeItem(int $referenceId, int $sizeId): void
    {
        $this->session->removeItem($referenceId, $sizeId);
    }

    public function clear(): void
    {
        $this->session->clearCart();
    }

    public function isEmpty(): bool
    {
        return $this->session->isEmpty();
    }

    public function applyDiscountCode(string $code): bool
    {
        $discountCode = DiscountCode::where('label_code_promo', $code)
            ->where('est_actif', true)
            ->first();

        if (! $discountCode) {
            return false;
        }

        $this->session->setDiscountCodeId($discountCode->id_code_promo);

        return true;
    }

    public function removeDiscountCode(): void
    {
        $this->session->clearDiscountCode();
    }

    public function getAppliedDiscountCode(): ?DiscountCode
    {
        $discountId = $this->session->getDiscountCodeId();

        if (! $discountId) {
            return null;
        }

        return DiscountCode::find($discountId);
    }

    /**
     * @return Collection<int, ShippingModeDTO>
     */
    public function getAvailableShippingModes(?float $subtotal = null, ?bool $hasBikes = null): Collection
    {
        // Calculer si non fourni
        if ($subtotal === null || $hasBikes === null) {
            $cartData = $this->getCartViewData(0);
            $subtotal = $cartData['summaryData']['subtotal'];
            $hasBikes = $cartData['hasBikes'];
        }

        $deliveryPrice = $subtotal >= 50 ? 0 : 6;

        return ShippingMode::query()
            ->when($hasBikes, fn ($q) => $q->where('id_moyen_livraison', '=', 1))
            ->get()
            ->map(function (ShippingMode $mode) use ($deliveryPrice) {
                $price = $mode->id_moyen_livraison === 1 ? $deliveryPrice : 6;

                return new ShippingModeDTO(
                    id: $mode->id_moyen_livraison,
                    name: $mode->label_moyen_livraison,
                    price: $price,
                );
            });
    }

    public function findShippingMode(int $id): ?ShippingModeDTO
    {
        $modes = $this->getAvailableShippingModes();

        return $modes->first(fn (ShippingModeDTO $mode) => $mode->id === $id);
    }

    /**
     * Retourne les données du panier dans le format attendu par les vues Blade
     */
    public function getCartViewData(?float $shippingPrice = null): array
    {
        $sessionItems = $this->session->getItems();

        if (empty($sessionItems)) {
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

        // Charger toutes les références et tailles en une seule requête
        $referenceIds = array_column($sessionItems, 'reference_id');
        $sizeIds = array_column($sessionItems, 'size_id');

        $references = ArticleReference::with([
            'bikeReference.color',
            'bikeReference.article',
            'accessory.article',
        ])->whereIn('id_reference', $referenceIds)->get()->keyBy('id_reference');

        $sizes = Size::whereIn('id_taille', $sizeIds)->get()->keyBy('id_taille');

        $cartData = [];
        $subtotal = 0;
        $hasBikes = false;

        foreach ($sessionItems as $sessionItem) {
            $reference = $references->get($sessionItem['reference_id']);
            $size = $sizes->get($sessionItem['size_id']);

            // Supprimer les items invalides automatiquement
            if (! $reference || ! $size) {
                $this->session->removeItem($sessionItem['reference_id'], $sessionItem['size_id']);

                continue;
            }

            /** @var Article $article */
            $article = $reference->bikeReference->article ?? $reference->accessory->article;

            $cartData[] = [
                'reference' => $reference->bikeReference ?? $reference->accessory,
                'img_url' => $article->getCoverUrl($reference->id_reference),
                'size' => $size,
                'quantity' => $sessionItem['quantity'],
                'article' => $article,
                'price_per_unit' => $article->getDiscountedPrice(),
                'real_price' => $article->prix_article,
                'has_discount' => $article->hasDiscount(),
                'discount_percent' => $article->pourcentage_remise,
                'color' => $reference->bikeReference?->color?->label_couleur,
                'article_url' => route('articles.show', [
                    'reference' => $reference->id_reference,
                    'article' => $article->id_article,
                ]),
            ];

            $subtotal += $article->getDiscountedPrice() * $sessionItem['quantity'];

            if ($reference->bikeReference) {
                $hasBikes = true;
            }
        }

        $discountCode = $this->getAppliedDiscountCode();
        $discountPercent = $discountCode?->pourcentage_remise ?? 0;
        $discountAmount = $subtotal * ($discountPercent / 100);

        // Si pas de shipping price fourni, prendre le minimum disponible
        if ($shippingPrice === null) {
            $shippingModes = $this->getAvailableShippingModes($subtotal, $hasBikes);
            $shippingPrice = $shippingModes->min(fn (ShippingModeDTO $mode) => $mode->price) ?? 0;
        }

        $totalTTC = $subtotal - $discountAmount + $shippingPrice;
        $tax = $totalTTC - ($totalTTC / 1.20);

        return [
            'cartData' => $cartData,
            'summaryData' => [
                'subtotal' => $subtotal,
                'discount' => $discountAmount,
                'shipping' => $shippingPrice,
                'tax' => $tax,
                'total' => $totalTTC,
            ],
            'discountData' => $discountCode,
            'count' => count($cartData),
            'hasBikes' => $hasBikes,
        ];
    }
}
