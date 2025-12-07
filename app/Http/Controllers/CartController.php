<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartAddRequest;
use App\Http\Requests\CartApplyDiscountRequest;
use App\Http\Requests\CartDeleteRequest;
use App\Http\Requests\CartUpdateQuantityRequest;
use App\Models\ArticleReference;
use App\Models\Size;
use App\Services\CartService;
use App\Services\OrderService;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected OrderService $orderService
    ) {}

    public function addToCart(CartAddRequest $request)
    {
        $validated = $request->validated();
        $this->cartService->addItem(
            reference_id: $validated['reference_id'],
            size_id: $validated['size_id']
        );

        $reference = ArticleReference::findOrFail($validated['reference_id']);
        $size = Size::findOrFail($validated['size_id']);

        return redirect()->back()->with('success', [
            'reference' => $reference,
            'size_id' => $size,
        ]);
    }

    public function index()
    {
        $this->orderService->clearOrderSessionData();

        return view('cart.index', $this->cartService->getCartData());
    }

    public function updateQuantity(CartUpdateQuantityRequest $request)
    {
        $validated = $request->validated();
        $this->cartService->updateItemQuantity(
            reference_id: $validated['reference_id'],
            size_id: $validated['size_id'],
            quantity: $validated['quantity']
        );

        return redirect()->back()->with('success', 'La quantité de l\'article a été mise à jour.');
    }

    public function delete(CartDeleteRequest $request)
    {
        $validated = $request->validated();
        $this->cartService->removeItem(
            reference_id: $validated['reference_id'],
            size_id: $validated['size_id']
        );

        return redirect()->back()->with('success', 'L\'article a été supprimé du panier.');
    }

    public function clearDiscount()
    {
        $this->cartService->removeDiscountCode();

        return redirect()->back()->with('success', 'Le code de réduction a été supprimé.');
    }

    public function applyDiscount(CartApplyDiscountRequest $request)
    {
        $validated = $request->validated();
        $this->cartService->applyDiscountCode($validated['discount_code']);

        return redirect()->back();
    }
}
