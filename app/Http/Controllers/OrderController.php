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
        private readonly CartService $cartService,
        private readonly CheckoutService $checkoutService,
        private readonly OrderService $orderService,
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

        return redirect()->route('checkout.index');
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
        $orders = $client->orders()
            ->with(['items', 'deliveryAddress.city', 'states'])
            ->orderByDesc('date_commande')
            ->paginate(6)
            ->through(fn ($order) => $this->orderService->formatForIndex($order));

        return view('dashboard.orders.index', [
            'orders' => $orders,
        ]);
    }

    public function show(Order $order)
    {
        $client = auth()->user();

        if ($order->id_client !== $client->id_client) {
            return redirect()->route('dashboard.orders.index')
                ->with('error', 'Accès non autorisé à cette commande.');
        }

        $order->load([
            'items.reference.article.category',

            'items.reference.article.bike.bikeModel',

            'items.reference.bikeReference.color',
            'items.reference.accessory',

            'items.size',

            'paymentType',
            'shippingMode',
            'shop.city',
            'states',

            'deliveryAddress.city',
            'billingAddress.city',
        ]);

        $currentState = $order->currentState();

        return view('dashboard.orders.show', [
            'order' => $order,
            'statusName' => $currentState->label_etat,
            'statusColors' => $this->orderService->getStatusStyle($currentState->id_etat),
            'financials' => $this->orderService->calculateFinancials($order),
            'items' => $this->orderService->formatLineItems($order->items),
            'paymentType' => $order->paymentType?->label_type_paiement,
            'shop' => $order->shop,
        ]);
    }
}
