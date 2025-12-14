<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderLine;
use App\Models\OrderState;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class OrderService
{
    public function formatForIndex(Order $order): object
    {
        $financials = $this->calculateFinancials($order);
        $currentState = $order->currentState();
        $statusStyle = $this->getStatusStyle($currentState->id_etat);

        return (object) [
            'id' => $order->id_commande,
            'number' => trim($order->num_commande),
            'date' => Carbon::parse($order->date_commande)->format('d/m/Y'),
            'tracking' => $order->num_suivi_commande ? trim($order->num_suivi_commande) : null,
            'statusLabel' => $currentState->label_etat,
            'statusColors' => $statusStyle,
            'countArticles' => $order->items->sum('quantite_ligne'),
            'financials' => $financials,
            'address' => $this->formatAddress($order->deliveryAddress),
        ];
    }

    public function calculateFinancials(Order $order): object
    {
        $subtotal = $order->items->sum(fn ($item) => $item->quantite_ligne * $item->prix_unit_ligne);
        $discount = $order->pourcentage_remise ? ($subtotal * $order->pourcentage_remise) / 100 : 0;
        $shipping = $order->frais_livraison ?? 0;

        return (object) [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping' => $shipping,
            'total' => $subtotal - $discount + $shipping,
            'discountPercent' => $order->pourcentage_remise,
            'count' => $order->items->sum('quantite_ligne'),
        ];
    }

    public function formatLineItems(Collection $items): Collection
    {
        return $items->map(function (OrderLine $item) {
            $baseReference = $item->reference;
            $ref = $baseReference->bikeReference ?? $baseReference->accessory;
            $article = $ref->article;

            $isBike = $baseReference->bikeReference;
            $subtitle = $isBike
                ? ($articleParent->bike?->bikeModel->nom_modele_velo ?? 'Vélo')
                : ($articleParent->category->nom_categorie ?? 'Accessoire');

            $image = $ref->getCoverUrl();

            return (object) [
                'name' => $article->nom_article,
                'subtitle' => $subtitle,
                'image' => $image,
                'colorHex' => $isBike ? $ref->color?->code_hex : null,
                'colorName' => $isBike ? $ref->color?->nom_couleur : null,
                'size' => $item->size?->nom_taille,
                'quantity' => $item->quantite_ligne,
                'unitPrice' => $item->prix_unit_ligne,
                'totalPrice' => $item->prix_unit_ligne * $item->quantite_ligne,
                'articleId' => $ref->id_article,
            ];
        });
    }

    public function getStatusStyle(int $stateId): array
    {
        return match ($stateId) {
            OrderState::PENDING_PAYMENT => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
            OrderState::PAYMENT_ACCEPTED => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
            OrderState::SHIPPED => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
            OrderState::DELIVERED => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800'],
            OrderState::CANCELLED => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
            OrderState::RETURNED => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800'],
            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'],
        };
    }

    private function formatAddress($address): ?object
    {
        if (! $address) {
            return null;
        }

        return (object) [
            'name' => $address->prenom_adresse.' '.$address->nom_adresse,
            'street' => $address->num_voie_adresse.' '.$address->rue_adresse,
            'city' => ($address->city->cp_ville ?? '').' '.($address->city->nom_ville ?? ''),
        ];
    }
}
