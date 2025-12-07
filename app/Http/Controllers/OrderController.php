<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderUpdateRequest;
use App\Services\CartService;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected OrderService $orderService,
    ) {}

    public function updateOrder(OrderUpdateRequest $request)
    {
        $validated = $request->validated();
        $client = auth()->user();

        $selectedBillingAddressId = $validated['billing_id'] ?? null;
        $selectedDeliveryAddressId = $validated['delivery_id'] ?? null;
        $selectedShippingModeId = $validated['shipping_id'] ?? null;

        if (! $client->addresses()->where('id_adresse', $selectedBillingAddressId)->exists()) {
            $selectedBillingAddressId = null;
        }

        if (! $client->addresses()->where('id_adresse', $selectedDeliveryAddressId)->exists()) {
            $selectedDeliveryAddressId = null;
        }

        $availableShippingModes = $this->cartService->getAvailableShippingModes();
        if (! collect($availableShippingModes)->pluck('id')->contains($selectedShippingModeId)) {
            $selectedShippingModeId = null;
        }

        $this->orderService->updateOrderSessionData(
            billingAddressId: $selectedBillingAddressId,
            deliveryAddressId: $selectedDeliveryAddressId,
            shippingModeId: $selectedShippingModeId,
        );

        return redirect()->route('cart.checkout');
    }

    public function checkout()
    {
        if ($this->cartService->isCartEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide. Veuillez ajouter des articles avant de passer à la caisse.');
        }

        $client = auth()->user();
        $checkoutData = $this->orderService->createOrderViewData($client);

        return view('order.checkout', $checkoutData);
    }
}
