<?php

namespace App\Services\Cart;

use App\DTOs\Cart\CheckoutDataDTO;
use App\Models\Client;
use App\Models\Order;
use App\Models\PaymentType;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Cashier\Checkout;
use Throwable;

class CheckoutService
{
    public function __construct(
        protected readonly CartService $cartService,
        protected readonly CartSessionManager $session,
    ) {}

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
        $shippingPrice = $checkoutData->shipping_mode?->price ?? 0;

        // Récupérer les données du panier avec le prix de livraison sélectionné
        $cartViewData = $this->cartService->getCartViewData($shippingPrice);

        $shippingModes = $this->cartService->getAvailableShippingModes(
            $cartViewData['summaryData']->subtotal,
            $cartViewData['hasBikes']
        );

        return [
            'addresses' => $client->addresses()->with('city')->get(),
            'deliveryModes' => $shippingModes,
            'selectedShippingId' => $checkoutData->shipping_mode?->id,
            'orderData' => $checkoutData,
            // Données du panier pour les vues
            'cartData' => $cartViewData['cartData'],
            'summaryData' => $cartViewData['summaryData'],
            'discountData' => $cartViewData['discountData'],
            'count' => $cartViewData['count'],
            'hasBikes' => $cartViewData['hasBikes'],
        ];
    }

    public function getCheckoutData(): CheckoutDataDTO
    {
        $sessionData = $this->session->getCheckoutData();

        $shippingModeId = $sessionData['shipping_mode_id'] ?? null;
        $shippingMode = $shippingModeId ? $this->cartService->findShippingMode($shippingModeId) : null;

        return new CheckoutDataDTO(
            billing_address_id: $sessionData['billing_address_id'] ?? null,
            delivery_address_id: $sessionData['delivery_address_id'] ?? null,
            shipping_mode: $shippingMode,
        );
    }

    /**
     * @throws Throwable
     */
    public function createOrder(Client $client): Order
    {
        if (! $this->isReadyForPayment()) {
            throw new DomainException('Checkout incomplet : adresses ou mode de livraison manquant.');
        }

        $checkoutData = $this->getCheckoutData();
        $cartViewData = $this->cartService->getCartViewData($checkoutData->shipping_mode->price);

        return DB::transaction(function () use ($checkoutData, $cartViewData, $client) {
            $order = Order::create([
                'id_client' => $client->id_client,
                'id_adresse_facturation' => $checkoutData->billing_address_id,
                'id_adresse_livraison' => $checkoutData->delivery_address_id,
                'id_moyen_livraison' => $checkoutData->shipping_mode->id,
                'num_commande' => $this->generateOrderNumber(),
                'frais_livraison' => $checkoutData->shipping_mode->price,
                'date_commande' => now(),
                'id_code_promo' => $cartViewData['discountData']?->id_code_promo,
                'pourcentage_remise' => $cartViewData['discountData']?->pourcentage_remise,
                'id_type_paiement' => PaymentType::UNKNOWN,
            ]);

            // Ajouter les articles du panier à la commande
            foreach ($cartViewData['cartData'] as $item) {
                $order->items()->create([
                    'id_reference' => $item->reference->id_reference,
                    'quantite_ligne' => $item->quantity,
                    'prix_unit_ligne' => $item->price_per_unit,
                    'id_taille' => $item->size->id_taille,
                ]);
            }

            return $order;
        });
    }

    public function isReadyForPayment(): bool
    {
        $checkoutData = $this->getCheckoutData();

        return ! $this->cartService->isEmpty()
            && $checkoutData->billing_address_id !== null
            && $checkoutData->delivery_address_id !== null
            && $checkoutData->shipping_mode !== null;
    }

    private function generateOrderNumber(): string
    {
        do {
            $code = Str::upper(Str::random(9));
        } while (Order::where('num_commande', $code)->exists());

        return $code;
    }

    public function initStripeCheckoutSession(Order $order): Checkout
    {
        $client = $order->client;
        $lineItems = [];

        // Eager load les relations pour éviter les requêtes N+1
        $order->load([
            'items.reference.article',
            'items.reference.accessory',
        ]);

        foreach ($order->items as $item) {
            $article = $item->reference->article;
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

        $checkout = $client->checkout($lineItems, [
            'success_url' => route('payment.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel'),
            'mode' => 'payment',
            'payment_method_types' => ['card', 'paypal'],
            'metadata' => [
                'order_id' => $order->id_commande,
                'client_id' => $order->id_client,
            ],
        ]);

        $order->stripe_session_id = $checkout->asStripeCheckoutSession()->id;
        $order->save();

        return $checkout;
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
}
