<?php

namespace App\Services;

use App\DTOs\Shop\SelectedShopDTO;
use App\Models\Shop;
use Illuminate\Support\Collection;

class ShopService
{
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

        $selectedShop = SelectedShopDTO::fromShop($shop);

        session(['selected_shop' => $selectedShop->toArray()]);

        return $selectedShop;
    }

    public function getSelectedShop(): ?SelectedShopDTO
    {
        $sessionData = session('selected_shop');

        if (! $sessionData) {
            return null;
        }

        return new SelectedShopDTO(
            id: $sessionData['id'],
            name: $sessionData['name'],
            city: $sessionData['city']
        );
    }
}
