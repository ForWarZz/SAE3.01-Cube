<?php

namespace App\Services\Order;

use App\DTOs\Order\AvailableReturnLineDTO;
use App\DTOs\Order\ReturnRequestItemDTO;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\OrderReturnRequest;
use App\Models\OrderState;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class OrderReturnService
{
    private const RETURN_PERIOD_DAYS = 14;

    public function canReturn(Order $order): bool
    {
        return $this->isDelivered($order)
            && $this->isWithinReturnPeriod($order)
            && $this->hasReturnableItems($order);
    }

    public function isDelivered(Order $order): bool
    {
        return $order->currentState()->id_etat == OrderState::DELIVERED;
    }

    public function isWithinReturnPeriod(Order $order): bool
    {
        $returnDeadline = $this->getReturnDeadline($order);

        return now()->lessThan($returnDeadline);
    }

    public function hasReturnableItems(Order $order): bool
    {
        foreach ($order->items as $item) {
            $totalReturned = $item->returnLines->sum('quantite_retournee');
            if ($totalReturned < $item->quantite_ligne) {
                return true;
            }
        }

        return false;
    }

    public function getReturnDeadline(Order $order): Carbon
    {
        $deliveryDate = $order->getDeliveryDate();
        $startDate = $deliveryDate ?? $order->date_commande;

        return Carbon::parse($startDate)->addDays(self::RETURN_PERIOD_DAYS);
    }

    public function getDaysRemaining(Order $order): int
    {
        $returnDeadline = $this->getReturnDeadline($order);

        return max(0, now()->diffInDays($returnDeadline, false));
    }

    public function getReturnEligibility(Order $order): array
    {
        return [
            'canReturn' => $this->canReturn($order),
            'isDelivered' => $this->isDelivered($order),
            'isWithinReturnPeriod' => $this->isWithinReturnPeriod($order),
            'hasReturnableItems' => $this->hasReturnableItems($order),
            'returnDeadline' => $this->getReturnDeadline($order),
            'daysRemaining' => $this->getDaysRemaining($order),
        ];
    }

    /**
     * @return Collection<int, AvailableReturnLineDTO>
     */
    public function getAvailableLinesToReturn(Order $order): Collection
    {
        $availableLines = collect();

        foreach ($order->items as $line) {
            $returnedQuantity = $line->returnLines->sum('quantite_retournee');
            $availableQuantity = $line->quantite_ligne - $returnedQuantity;

            if ($availableQuantity > 0) {
                $availableLines->push($this->buildReturnLineDTO($line, $returnedQuantity, $availableQuantity));
            }
        }

        return $availableLines;
    }

    private function buildReturnLineDTO(OrderLine $line, int $returnedQuantity, int $availableQuantity): AvailableReturnLineDTO
    {
        $baseReference = $line->reference;
        $ref = $baseReference->variant();
        $article = $baseReference->article;

        $isBike = $baseReference->isBike();

        $subtitle = $isBike
            ? $article->bike?->bikeModel?->nom_modele_velo
            : $article->category?->nom_categorie;

        $image = $ref->getCoverUrl();

        return new AvailableReturnLineDTO(
            lineId: $line->id_ligne,
            name: $article->nom_article,
            subtitle: $subtitle,
            image: $image,
            colorHex: $isBike ? $ref->color?->hex : null,
            colorName: $isBike ? $ref->color?->label_couleur : null,
            size: $line->size?->label,
            orderedQuantity: $line->quantite_ligne,
            returnedQuantity: $returnedQuantity,
            availableQuantity: $availableQuantity,
            unitPrice: $line->prix_unit_ligne,
            totalPrice: $line->prix_unit_ligne * $availableQuantity,
            articleId: $article->id_article,
        );
    }

    /**
     * @param  ReturnRequestItemDTO[]  $items
     */
    public function createReturnRequest(Order $order, array $items, ?string $message): OrderReturnRequest
    {
        $returnRequest = $order->returnRequests()->create([
            'description_demande' => $message,
        ]);

        foreach ($items as $item) {
            $line = $order->items->where('id_ligne', $item->lineId)->first();

            if ($line) {
                $returnedQuantity = $line->returnLines->sum('quantite_retournee');
                $availableQuantity = $line->quantite_ligne - $returnedQuantity;

                if ($item->quantity > 0 && $item->quantity <= $availableQuantity) {
                    $returnRequest->lines()->create([
                        'id_ligne_commande' => $line->id_ligne,
                        'quantite_retournee' => $item->quantity,
                    ]);
                }
            }
        }

        return $returnRequest;
    }
}
