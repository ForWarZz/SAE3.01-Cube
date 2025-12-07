<?php

namespace App\Http\Controllers;

use App\Services\CartService;

class OrderController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index()
    {
        $cartData = $this->cartService->getCartData();

        return view('command.index', $cartData);
    }
}
