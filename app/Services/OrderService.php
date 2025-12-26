<?php

namespace App\Services;

use App\DTOs\Order\AddressDTO;
use App\DTOs\Order\OrderFinancialsDTO;
use App\DTOs\Order\OrderLineItemDTO;
use App\DTOs\Order\OrderSummaryDTO;
use App\DTOs\Order\StatusStyleDTO;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\OrderState;
use Illuminate\Support\Collection;

class OrderService
{
    public function formatForIndex(Order $order): OrderSummaryDTO
    {
        $financials = $this->calculateFinancials($order);
        $currentState = $order->currentState();
        $statusStyle = $this->getStatusStyle($currentState->id_etat);

        return new OrderSummaryDTO(
            id: $order->id_commande,
            number: trim($order->num_commande),
            date: $order->date_commande,
            tracking: $order->num_suivi_commande ? trim($order->num_suivi_commande) : null,
            statusLabel: $currentState->label_etat,
            statusColors: $statusStyle,
            countArticles: $order->items->sum('quantite_ligne'),
            financials: $financials,
            address: $order->deliveryAddress ? AddressDTO::fromModel($order->deliveryAddress) : null,
        );
    }

    public function calculateFinancials(Order $order): OrderFinancialsDTO
    {
        $subtotal = $order->items->sum(fn ($item) => $item->quantite_ligne * $item->prix_unit_ligne);
        $discount = $order->pourcentage_remise ? ($subtotal * $order->pourcentage_remise) / 100 : 0;
        $shipping = $order->frais_livraison ?? 0;

        return new OrderFinancialsDTO(
            subtotal: $subtotal,
            discount: $discount,
            shipping: $shipping,
            total: $subtotal - $discount + $shipping,
            discountPercent: $order->pourcentage_remise,
            count: $order->items->sum('quantite_ligne'),
        );
    }

    public function getStatusStyle(int $stateId): StatusStyleDTO
    {
        return match ($stateId) {
            OrderState::PENDING_PAYMENT => new StatusStyleDTO(bg: 'bg-yellow-100', text: 'text-yellow-800'),
            OrderState::PAYMENT_ACCEPTED => new StatusStyleDTO(bg: 'bg-green-100', text: 'text-green-800'),
            OrderState::SHIPPED => new StatusStyleDTO(bg: 'bg-blue-100', text: 'text-blue-800'),
            OrderState::DELIVERED => new StatusStyleDTO(bg: 'bg-indigo-100', text: 'text-indigo-800'),
            OrderState::CANCELLED => new StatusStyleDTO(bg: 'bg-red-100', text: 'text-red-800'),
            OrderState::RETURNED => new StatusStyleDTO(bg: 'bg-purple-100', text: 'text-purple-800'),
            default => new StatusStyleDTO(bg: 'bg-gray-100', text: 'text-gray-800'),
        };
    }

    /**
     * @return Collection<int, OrderLineItemDTO>
     */
    public function formatLineItems(Collection $items): Collection
    {
        return $items->map(function (OrderLine $item) {
            $baseReference = $item->reference;
            $ref = $baseReference->variant();
            $article = $baseReference->article;

            $isBike = $baseReference->isBike();
            $subtitle = $isBike
                ? $article->bike?->bikeModel?->nom_modele_velo
                : $article->category?->nom_categorie;

            $image = $ref->getCoverUrl();

            return new OrderLineItemDTO(
                name: $article->nom_article,
                subtitle: $subtitle,
                image: $image,
                colorHex: $isBike ? $ref->color?->hex : null,
                colorName: $isBike ? $ref->color?->label_couleur : null,
                size: $item->size?->nom_taille,
                quantity: $item->quantite_ligne,
                unitPrice: $item->prix_unit_ligne,
                totalPrice: $item->prix_unit_ligne * $item->quantite_ligne,
                articleId: $ref->id_article,
            );
        });
    }
}
