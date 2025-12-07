<?php

namespace App\Services;

use App\Models\Client;

class OrderService
{
    private const ORDER_SESSION_KEY = 'order_data';

    public function __construct(
        protected CartService $cartService,
    ) {}

    public function getOrderSessionData(): array
    {
        return session(self::ORDER_SESSION_KEY, []);
    }

    public function createOrderViewData(Client $client): array
    {
        $addresses = $client->addresses()->with('ville')->get();

        $orderSessionData = $this->getOrderSessionData();
        $shippingModes = $this->cartService->getAvailableShippingModes();

        $selectedShippingMode = $orderSessionData['shipping_mode'] ?? null;
        $shippingPrice = $selectedShippingMode['price'] ?? 0;

        $cartData = $this->cartService->getCartData($shippingPrice);

        return [
            'addresses' => $addresses,
            'deliveryModes' => $shippingModes,
            'selectedShippingId' => $selectedShippingMode['id'] ?? null,
            'orderData' => $orderSessionData,
            ...$cartData,
        ];
    }

    public function updateOrderSessionData(?int $billingAddressId, ?int $deliveryAddressId, ?int $shippingModeId): void
    {
        $availableShippingModes = $this->cartService->getAvailableShippingModes();
        $selectedShippingMode = null;

        foreach ($availableShippingModes as $mode) {
            if ($mode['id'] === $shippingModeId) {
                $selectedShippingMode = $mode;
                break;
            }
        }

        $orderData = [
            'billing_address_id' => $billingAddressId,
            'delivery_address_id' => $deliveryAddressId,
            'shipping_mode' => $selectedShippingMode,
        ];

        session([self::ORDER_SESSION_KEY => $orderData]);
    }

    public function clearOrderSessionData(): void
    {
        session()->forget(self::ORDER_SESSION_KEY);
    }
}
