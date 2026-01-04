<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::with('city')
            ->withCoordinates()
            ->get()
            ->map(function ($shop) {
                return [
                    'shop' => $shop->toApiFormat(),
                    'status' => null,
                ];
            });

        return response()->json([
            'shops' => $shops,
        ]);
    }

    /**
     * Sélectionne un magasin et le stocke en session
     */
    public function select(Request $request)
    {
        $validated = $request->validate([
            'shop_id' => 'required|exists:magasin,id_magasin',
        ]);

        $shop = Shop::with('city')
            ->withCoordinates()
            ->find($validated['shop_id']);

        session(['selected_shop' => [
            'id' => $shop->id_magasin,
            'name' => $shop->nom_magasin,
            'address' => $shop->full_address, 
            'postalCode' => $shop->city?->cp_ville,
            'city' => $shop->city ? trim($shop->city->nom_ville) : null,
            'lat' => $shop->latitude ?? null,
            'lng' => $shop->longitude ?? null,
        ]]);

        return response()->json([
            'success' => true,
            'shop' => session('selected_shop'),
        ]);
    }

    public function selected()
    {
        $selectedShop = session('selected_shop');

        if (! $selectedShop) {
            return response()->json([
                'selected' => false,
                'shop' => null,
            ]);
        }

        return response()->json([
            'selected' => true,
            'shop' => $selectedShop,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');

        $shops = Shop::with('city')
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
            ->map(fn ($shop) => ['shop' => $shop->toApiFormat(), 'status' => null]);

        return response()->json([
            'shops' => $shops,
        ]);
    }

    public function clear()
    {
        session()->forget('selected_shop');
        
        return response()->json(['success' => true]);
    }
}
