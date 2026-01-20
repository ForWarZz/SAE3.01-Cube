<?php

namespace App\Services;

use App\DTOs\Shop\SelectedShopDTO;
use App\Models\Shop;
use App\Services\Cart\CartSessionManager;
use Illuminate\Support\Collection;

class ShopService
{
    public function __construct(
        protected readonly CartSessionManager $sessionManager,
    ) {}

    /**
     * @return Collection<int, array{shop: array, status: null}>
     */
    public function getAllShops(): Collection
    {
        return Shop::with('city')
            ->withCoordinates()
            ->get()
            ->map(fn ($shop) => [
                'shop' => $shop->toApiFormat(),
                'status' => null,
            ]);
    }

    /**
     * @return Collection<int, array{shop: array, status: null}>
     */
    public function searchShops(string $query): Collection
    {
        return Shop::with('city')
            ->withCoordinates()
            ->where(function ($q) use ($query) {
                $q->where('nom_magasin', 'ILIKE', "%{$query}%")
                    ->orWhere('rue_magasin', 'ILIKE', "%{$query}%")
                    ->orWhereHas('city', function ($subQuery) use ($query) {
                        $subQuery->where('nom_ville', 'ILIKE', "%{$query}%")
                            ->orWhere('cp_ville', 'LIKE', "%{$query}%");
                    });
            })
            ->limit(20)
            ->get()
            ->map(fn ($shop) => [
                'shop' => $shop->toApiFormat(),
                'status' => null,
            ]);
    }

    public function selectShop(int $shopId): SelectedShopDTO
    {
        $shop = Shop::with('city')->findOrFail($shopId);
        $this->sessionManager->setSelectedShop($shop);

        return SelectedShopDTO::fromShop($shop);
    }

    public function getSelectedShop(): ?SelectedShopDTO
    {
        $shopId = $this->sessionManager->getSelectedShopId();

        if (! $shopId) {
            return null;
        }

        $shop = Shop::with('city')->find($shopId);

        if (! $shop) {
            return null;
        }

        return SelectedShopDTO::fromShop($shop);
    }
}
