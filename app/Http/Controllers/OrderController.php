<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderUpdateRequest;
use App\Services\Cart\CartService;
use App\Services\Cart\CheckoutService;

class OrderController extends Controller
{
    public function __construct(
        protected readonly CartService $cartService,
        protected readonly CheckoutService $checkoutService,
    ) {}

    public function updateOrder(OrderUpdateRequest $request)
    {
        $validated = $request->validated();
        $client = auth()->user();

        // Validation des adresses
        $billingAddressId = $this->checkoutService->validateAddressForClient(
            $client,
            $validated['billing_id'] ?? null
        );
        $deliveryAddressId = $this->checkoutService->validateAddressForClient(
            $client,
            $validated['delivery_id'] ?? null
        );

        // Validation du mode de livraison
        $shippingModeId = $this->checkoutService->validateShippingMode(
            $validated['shipping_id'] ?? null
        );

        $this->checkoutService->updateCheckout($billingAddressId, $deliveryAddressId, $shippingModeId);

        return redirect()->route('cart.checkout');
    }

    public function checkout()
    {
        if ($this->cartService->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide. Veuillez ajouter des articles avant de passer à la caisse.');
        }

        $client = auth()->user();

        return view('order.checkout', $this->checkoutService->getCheckoutViewData($client));
    }
}
