<?php

namespace App\Services\Order;

use App\DTOs\Order\AvailableReturnLineDTO;
use App\DTOs\Order\ReturnRequestItemDTO;
use App\Models\Order;
use App\Models\OrderLine;
use App\Models\OrderReturnRequest;
use App\Services\OrderService;
use Illuminate\Support\Collection;

class OrderReturnService
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

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
    public function createReturnRequest(Order $order, array $items, string $message): OrderReturnRequest
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
