<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    /**
     * Récupère les commandes paginées pour un client donné
     */
    public function getOrdersForClient(Client $client, int $perPage = 10): LengthAwarePaginator
    {
        return Order::where('id_client', $client->id_client)
            ->with([
                'items.reference.bikeReference.article',
                'items.reference.accessory',
                'items.size',
                'deliveryAddress.ville',
                'billingAddress.ville',
                'deliveryMode',
                'states',
            ])
            ->orderByDesc('date_commande')
            ->paginate($perPage);
    }

    /**
     * Récupère une commande spécifique pour un client
     */
    public function getOrderForClient(Client $client, int $orderId): ?Order
    {
        return Order::where('id_commande', $orderId)
            ->where('id_client', $client->id_client)
            ->with([
                'items.reference.bikeReference.article',
                'items.reference.accessory',
                'items.size',
                'billingAddress.ville',
                'deliveryAddress.ville',
                'deliveryMode',
                'states',
                'discountCode',
                'paymentType',
            ])
            ->first();
    }

    /**
     * Vérifie si une commande appartient à un client
     */
    public function orderBelongsToClient(int $orderId, int $clientId): bool
    {
        return Order::where('id_commande', $orderId)
            ->where('id_client', $clientId)
            ->exists();
    }

    /**
     * Récupère les commandes récentes pour un client (pour le dashboard)
     */
    public function getRecentOrdersForClient(Client $client, int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Order::where('id_client', $client->id_client)
            ->with(['items', 'states'])
            ->orderByDesc('date_commande')
            ->limit($limit)
            ->get();
    }

    /**
     * Compte le nombre total de commandes pour un client
     */
    public function countOrdersForClient(Client $client): int
    {
        return Order::where('id_client', $client->id_client)->count();
    }

    /**
     * Calcule le montant total dépensé par un client
     */
    public function getTotalSpentByClient(Client $client): float
    {
        $orders = Order::where('id_client', $client->id_client)
            ->with('items')
            ->get();

        return $orders->sum(function ($order) {
            $subtotal = $order->items->sum(fn($item) => $item->quantite_ligne * ($item->prix_unit_ligne ?? 0));
            $discount = $order->pourcentage_remise ? ($subtotal * $order->pourcentage_remise / 100) : 0;
            return $subtotal - $discount + ($order->frais_livraison ?? 0);
        });
    }
}