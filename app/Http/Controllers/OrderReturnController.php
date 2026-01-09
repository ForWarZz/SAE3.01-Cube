<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Order\OrderReturnService;
use App\Services\OrderService;

class OrderReturnController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly OrderReturnService $orderReturnService
    ) {}

    public function create(Order $order)
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
        ]);

        return view('dashboard.orders.make-return', [
            'order' => $order,
            'items' => $this->orderService->formatLineItems($order->items),
        ]);
    }
}
