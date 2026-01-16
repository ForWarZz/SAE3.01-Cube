<?php

namespace App\Http\Controllers;

use App\Http\Requests\Shop\SelectShopRequest;
use App\Services\ShopService;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct(
        private readonly ShopService $shopService
    ) {}

    public function index()
    {
        $shops = $this->shopService->getAllShops();

        return response()->json([
            'shops' => $shops,
        ]);
    }

    public function select(SelectShopRequest $request)
    {
        $selectedShop = $this->shopService->selectShop($request->input('shop_id'));

        return response()->json([
            'success' => true,
            'shop' => $selectedShop->toArray(),
        ]);
    }

    public function selected()
    {
        $selectedShop = $this->shopService->getSelectedShop();

        if (! $selectedShop) {
            return response()->json([
                'selected' => false,
                'shop' => null,
            ]);
        }

        return response()->json([
            'selected' => true,
            'shop' => $selectedShop->toArray(),
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $shops = $this->shopService->searchShops($query);

        return response()->json([
            'shops' => $shops,
        ]);
    }
}
