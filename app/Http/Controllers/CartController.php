<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\CartAddRequest;
use App\Http\Requests\Cart\CartApplyDiscountRequest;
use App\Http\Requests\Cart\CartDeleteRequest;
use App\Http\Requests\Cart\CartUpdateQuantityRequest;
use App\Models\ArticleReference;
use App\Models\Size;
use App\Services\Cart\CartService;
use App\Services\Cart\CheckoutService;

class CartController extends Controller
{
    public function __construct(
        protected readonly CartService $cartService,
        protected readonly CheckoutService $checkoutService,
    ) {}

    public function addToCart(CartAddRequest $request)
    {
        $validated = $request->validated();
        $this->cartService->addItem(
            referenceId: $validated['reference_id'],
            sizeId: $validated['size_id']
        );

        $reference = ArticleReference::with(['bikeReference.article', 'bikeReference.color', 'accessory.article'])->findOrFail($validated['reference_id']);
        $size = Size::findOrFail($validated['size_id']);

        $article = $reference->bikeReference?->article ?? $reference->accessory?->article;
        $color = $reference->bikeReference?->color;

        $itemData = [
            'name' => $article->nom_article ?? 'Article',
            'image' => $article->getCoverUrl($reference->id_reference),
            'color' => $color?->label_couleur ?? null,
            'size' => $size->nom_taille,
            'price' => number_format($article->getDiscountedPrice(), 2, ',', ' ').' €',
        ];

        return redirect()->back()->with('cart_added', $itemData);
    }

    public function index()
    {
        $this->checkoutService->clearCheckout();

        return view('cart.index', $this->cartService->getCartViewData());
    }

    public function updateQuantity(CartUpdateQuantityRequest $request)
    {
        $validated = $request->validated();
        $this->cartService->updateQuantity(
            referenceId: $validated['reference_id'],
            sizeId: $validated['size_id'],
            quantity: $validated['quantity']
        );

        return redirect()->back()->with('success', 'La quantité de l\'article a été mise à jour.');
    }

    public function delete(CartDeleteRequest $request)
    {
        $validated = $request->validated();
        $this->cartService->removeItem(
            referenceId: $validated['reference_id'],
            sizeId: $validated['size_id']
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
