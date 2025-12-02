<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class CartService
{
    private string $cart = 'cart';

    public function __construct() {}

    public function getCartFromSession(): array
    {
        return Session::get($this->cart, []);
    }

    public function addItem(int $reference_id, int $size_id): void
    {
        $cart = $this->getCartFromSession();
        $key = $reference_id.'_'.$size_id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += 1;
        } else {
            $cart[$key] = [
                'reference_id' => $reference_id,
                'size_id' => $size_id,
                'quantity' => 1,
            ];
        }

        Session::put($this->cart, $cart);
    }

    public function removeItem(int $reference_id, int $size_id): void
    {
        $cart = $this->getCartFromSession();
        $key = $reference_id.'_'.$size_id;

        if (isset($cart[$key])) {
            unset($cart[$key]);

            Session::put($this->cart, $cart);
        }
    }
}
