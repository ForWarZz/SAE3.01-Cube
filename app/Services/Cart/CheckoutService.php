<?php

namespace App\Services\Cart;

use App\DTOs\Cart\CheckoutDataDTO;
use App\DTOs\Cart\ShippingModeDTO;
use App\DTOs\ShopDTO;
use App\Models\Client;
use App\Models\Order;
use App\Models\PaymentType;
use App\Models\ShippingMode;
use App\Models\Shop;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Cashier\Checkout;

class CheckoutService
{
    public function __construct(
        protected readonly CartService $cartService,
        protected readonly CartSessionManager $sessionManager,
    ) {}

    public function updateCheckout(?int $billingAddressId, ?int $deliveryAddressId, ?int $shippingModeId): void
    {
        $this->sessionManager->setCheckoutData($billingAddressId, $deliveryAddressId, $shippingModeId);
    }

    public function getCheckoutViewData(Client $client): array
    {
        $checkoutData = $this->buildCheckoutData();
        $shippingModeId = $checkoutData->shipping_mode?->id;
        $cartData = $this->cartService->getCartData($shippingModeId);

        return [
            'addresses' => $client->addresses()->with('city')->get(),
            'deliveryModes' => $this->cartService->getAvailableShippingModes(),
            'selectedShippingId' => $checkoutData->shipping_mode?->id,
            'orderData' => $checkoutData,
            'selectedShop' => $checkoutData->shop,
            ...$cartData->toViewData(),
        ];
    }

    public function buildCheckoutData(): CheckoutDataDTO
    {
        $sessionData = $this->sessionManager->getCheckoutData();
        $shippingModeId = $sessionData['shipping_mode_id'];

        $shippingMode = $this->findShippingMode($shippingModeId);
        $isClickAndCollect = $shippingMode?->id === ShippingMode::CLICK_AND_COLLECT;

        $shopDTO = null;
        if ($isClickAndCollect) {
            $shopId = $this->sessionManager->getSelectedShopId();
            if ($shopId) {
                $shop = Shop::with('city')->find($shopId);
                if ($shop) {
                    $shopDTO = ShopDTO::fromModel($shop);
                }
            }
        }

        return new CheckoutDataDTO(
            billing_address_id: $sessionData['billing_address_id'],
            delivery_address_id: $isClickAndCollect ? null : $sessionData['delivery_address_id'],
            shipping_mode: $shippingMode,
            shop: $shopDTO,
        );
    }

    public function createOrder(Client $client): Order
    {
        if (! $this->isReadyForPayment()) {
            throw new DomainException('Checkout incomplet : adresses ou mode de livraison manquant.');
        }

        $checkoutData = $this->buildCheckoutData();
        $cartData = $this->cartService->getCartData($checkoutData->shipping_mode->id);

        return DB::transaction(function () use ($checkoutData, $cartData, $client) {
            $order = Order::create([
                'id_client' => $client->id_client,
                'id_adresse_facturation' => $checkoutData->billing_address_id,
                'id_adresse_livraison' => $checkoutData->delivery_address_id,
                'id_moyen_livraison' => $checkoutData->shipping_mode->id,
                'id_magasin' => $checkoutData->shop?->id,
                'num_commande' => $this->generateOrderNumber(),
                'frais_livraison' => $checkoutData->shipping_mode->price,
                'date_commande' => now(),
                'id_code_promo' => $cartData->discountCode?->id_code_promo,
                'pourcentage_remise' => $cartData->discountCode?->pourcentage_remise,
                'id_type_paiement' => PaymentType::UNKNOWN,
            ]);

            foreach ($cartData->items as $item) {
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

    public function initStripeCheckoutSession(Order $order): Checkout
    {
        $order->load([
            'items.reference.article',
            'client',
        ]);

        $lineItems = [];

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

        $checkout = $order->client->checkout($lineItems, [
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

    public function isReadyForPayment(): bool
    {
        if ($this->cartService->isEmpty()) {
            return false;
        }

        $checkoutData = $this->buildCheckoutData();

        if (! $checkoutData->billing_address_id) {
            return false;
        }

        if ($checkoutData->shipping_mode?->id === ShippingMode::CLICK_AND_COLLECT) {
            return $checkoutData->shop !== null;
        }

        return $checkoutData->delivery_address_id !== null && $checkoutData->shipping_mode !== null;
    }

    public function validateAddressForClient(Client $client, ?int $addressId): ?int
    {
        if ($addressId === null) {
            return null;
        }

        return $client->addresses()->where('id_adresse', $addressId)->exists() ? $addressId : null;
    }

    public function validateShippingMode(?int $shippingModeId): ?int
    {
        if ($shippingModeId === null) {
            return null;
        }

        return ShippingMode::where('id_moyen_livraison', $shippingModeId)->exists() ? $shippingModeId : null;
    }

    private function findShippingMode(?int $shippingModeId): ?ShippingModeDTO
    {
        if (! $shippingModeId) {
            return null;
        }

        $availableModes = $this->cartService->getAvailableShippingModes();

        return $availableModes->firstWhere('id', $shippingModeId);
    }

    private function generateOrderNumber(): string
    {
        do {
            $code = Str::upper(Str::random(9));
        } while (Order::where('num_commande', $code)->exists());

        return $code;
    }
}
