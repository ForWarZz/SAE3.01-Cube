<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $shops = Shop::all()->map(function ($shop) {
            return [
                'shop' => [
                    'id' => $shop->id_magasin,
                    'name' => $shop->nom_magasin,
                    'address' => trim($shop->num_voie_magasin . ' ' . $shop->rue_magasin),
                    'complement' => $shop->complement_magasin,
                ],
                'status' => null
            ];
        });

        return response()->json([
            'shops' => $shops
        ]);
    }

    public function select(Request $request)
    {
        $validated = $request->validate([
            'shop_id' => 'required|exists:magasin,id_magasin'
        ]);

        $shop = Shop::find($validated['shop_id']);
        session(['selected_shop' => $shop]);

        return response()->json([
            'success' => true,
            'shop' => [
                'id' => $shop->id_magasin,
                'name' => $shop->nom_magasin,
            ]
        ]);
    }
}