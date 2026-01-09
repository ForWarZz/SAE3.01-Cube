<?php

namespace App\Http\Controllers;

use App\DTOs\Order\ReturnRequestItemDTO;
use App\Http\Requests\OrderReturnCreateRequest;
use App\Models\Order;
use App\Services\Order\OrderReturnService;

class OrderReturnController extends Controller
{
    public function __construct(
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
            'items.returnLines',
        ]);

        return view('dashboard.orders.make-return', [
            'order' => $order,
            'items' => $this->orderReturnService->getAvailableLinesToReturn($order),
        ]);
    }

    public function store(OrderReturnCreateRequest $request, Order $order)
    {
        $client = auth()->user();

        if ($order->id_client !== $client->id_client) {
            return redirect()->route('dashboard.orders.index')
                ->with('error', 'Accès non autorisé à cette commande.');
        }

        $validated = $request->validated();

        $items = collect($validated['items'])
            ->filter(fn ($item) => ($item['quantity'] ?? 0) > 0)
            ->map(fn ($item) => new ReturnRequestItemDTO(
                lineId: $item['line_id'],
                quantity: $item['quantity'],
            ))
            ->values()
            ->toArray();

        $returnRequest = $this->orderReturnService->createReturnRequest($order, $items, $validated['message'] ?? null);

        return redirect()->route('dashboard.orders.show', ['order' => $order->id_commande])
            ->with('success', 'Votre demande de retour a été soumise avec succès. Elle sera traitée par le service client dans les plus brefs délais.');
    }
}
