<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderUpdateRequest;
use App\Models\Order;
use App\Services\Cart\CartService;
use App\Services\Cart\CheckoutService;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        protected readonly CartService $cartService,
        protected readonly CheckoutService $checkoutService,
        protected readonly OrderService $orderService,
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

    public function index()
    {
        $client = auth()->user();
        $orders = $this->orderService->getOrdersForClient($client);

        return view('dashboard.orders.index', [
            'orders' => $orders,
        ]);
    }

    public function show($id)
    {
        $client = auth()->user();

        $order = Order::where('id_commande', $id)
            ->where('id_client', $client->id_client)
            ->with([
                'items.reference.bikeReference.article.bike.bikeModel',
                'items.reference.bikeReference.color',
                'items.reference.accessory.article.category',
                'items.size',
                'billingAddress.ville',
                'deliveryAddress.ville',
                'deliveryMode',
                'states',
            ])
            ->firstOrFail();

        return view('dashboard.orders.order.show', [
            'order' => $order,
        ]);
    }
}