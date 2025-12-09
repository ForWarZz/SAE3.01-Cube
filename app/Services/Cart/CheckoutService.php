<?php

namespace App\Services\Cart;

use App\DTOs\Cart\ShippingModeDTO;
use App\Models\Client;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Cashier\Checkout;

class CheckoutService
{
    public function __construct(
        protected readonly CartService $cartService,
        protected readonly CartSessionManager $session,
    ) {}

    /**
     * @return array{
     *     billing_address_id: ?int,
     *     delivery_address_id: ?int,
     *     shipping_mode: ?ShippingModeDTO
     * }
     */
    public function getCheckoutData(): array
    {
        $sessionData = $this->session->getCheckoutData();

        $shippingModeId = $sessionData['shipping_mode_id'] ?? null;
        $shippingMode = $shippingModeId ? $this->cartService->findShippingMode($shippingModeId) : null;

        return [
            'billing_address_id' => $sessionData['billing_address_id'] ?? null,
            'delivery_address_id' => $sessionData['delivery_address_id'] ?? null,
            'shipping_mode' => $shippingMode,
        ];
    }

    public function updateCheckout(?int $billingAddressId, ?int $deliveryAddressId, ?int $shippingModeId): void
    {
        $this->session->setCheckoutData($billingAddressId, $deliveryAddressId, $shippingModeId);
    }

    public function clearCheckout(): void
    {
        $this->session->clearCheckout();
    }

    public function getCheckoutViewData(Client $client): array
    {
        $checkoutData = $this->getCheckoutData();
        $shippingPrice = $checkoutData['shipping_mode']?->price ?? 0;

        // Récupérer les données du panier avec le prix de livraison sélectionné
        $cartViewData = $this->cartService->getCartViewData($shippingPrice);

        $shippingModes = $this->cartService->getAvailableShippingModes(
            $cartViewData['summaryData']['subtotal'],
            $cartViewData['hasBikes']
        );

        return [
            'addresses' => $client->addresses()->with('ville')->get(),
            'deliveryModes' => $shippingModes->map(fn (ShippingModeDTO $mode) => $mode->toArray())->toArray(),
            'selectedShippingId' => $checkoutData['shipping_mode']?->id,
            'orderData' => [
                'billing_address_id' => $checkoutData['billing_address_id'],
                'delivery_address_id' => $checkoutData['delivery_address_id'],
                'shipping_mode' => $checkoutData['shipping_mode']?->toArray(),
            ],
            // Données du panier pour les vues
            'cartData' => $cartViewData['cartData'],
            'summaryData' => $cartViewData['summaryData'],
            'discountData' => $cartViewData['discountData'],
            'count' => $cartViewData['count'],
            'hasBikes' => $cartViewData['hasBikes'],
        ];
    }

    public function isReadyForPayment(): bool
    {
        $checkoutData = $this->getCheckoutData();

        return ! $this->cartService->isEmpty()
            && $checkoutData['billing_address_id'] !== null
            && $checkoutData['delivery_address_id'] !== null
            && $checkoutData['shipping_mode'] !== null;
    }

    /**
     * @throws \Throwable
     */
    public function createOrder(Client $client): Order
    {
        if (! $this->isReadyForPayment()) {
            throw new \DomainException('Checkout incomplet : adresses ou mode de livraison manquant.');
        }

        $checkoutData = $this->getCheckoutData();
        $cartViewData = $this->cartService->getCartViewData($checkoutData['shipping_mode']->price);

        return DB::transaction(function () use ($checkoutData, $cartViewData, $client) {
            $order = Order::create([
                'id_client' => $client->id_client,
                'id_adresse_facturation' => $checkoutData['billing_address_id'],
                'id_adresse_livraison' => $checkoutData['delivery_address_id'],
                'id_moyen_livraison' => $checkoutData['shipping_mode']->id,
                'num_commande' => $this->generateOrderNumber(),
                'frais_livraison' => $checkoutData['shipping_mode']->price,
                'date_commande' => now(),
                'id_code_promo' => $cartViewData['discountData']?->id_code_promo,
                'pourcentage_remise' => $cartViewData['discountData']?->pourcentage_remise,
                'id_type_paiement' => 1,
            ]);

            // Ajouter les articles du panier à la commande
            foreach ($cartViewData['cartData'] as $item) {
                $order->items()->create([
                    'id_reference' => $item['reference']->id_reference,
                    'quantite_ligne' => $item['quantity'],
                    'prix_unit_ligne' => $item['price_per_unit'],
                    'id_taille' => $item['size']->id_taille,
                ]);
            }

            return $order;
        });
    }

    public function initStripeCheckoutSession(Order $order): Checkout
    {
        $client = $order->client;
        $lineItems = [];

        foreach ($order->items as $item) {
            $article = $item->reference->bikeReference?->article ?? $item->reference->accessory?->article;
            $prixUnitaire = $item->prix_unit_ligne;

            if ($order->pourcentage_remise > 0) {
                $prixUnitaire = $prixUnitaire * (1 - ($order->pourcentage_remise / 100));
            }

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $article->nom_article,
                    ],
                    'unit_amount' => (int) round($prixUnitaire * 100),
                ],
                'quantity' => $item->quantite_ligne,
            ];
        }

        // Frais de livraison
        if ($order->frais_livraison > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Frais de livraison',
                    ],
                    'unit_amount' => (int) round($order->frais_livraison * 100),
                ],
                'quantity' => 1,
            ];
        }

        return $client->checkout($lineItems, [
            'success_url' => route('payment.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel'),
            'mode' => 'payment',
            'payment_method_types' => ['card', 'paypal'],
            'metadata' => [
                'order_id' => $order->id_commande,
                'client_id' => $order->id_client,
            ],
        ]);
    }

    public function validateAddressForClient(Client $client, ?int $addressId): ?int
    {
        if ($addressId === null) {
            return null;
        }

        return $client->addresses()->where('id_adresse', $addressId)->exists()
            ? $addressId
            : null;
    }

    public function validateShippingMode(?int $shippingModeId): ?int
    {
        if ($shippingModeId === null) {
            return null;
        }

        $mode = $this->cartService->findShippingMode($shippingModeId);

        return $mode ? $shippingModeId : null;
    }

    private function generateOrderNumber(): string
    {
        do {
            $code = Str::upper(Str::random(9));
        } while (Order::where('num_commande', $code)->exists());

        return $code;
    }
}
