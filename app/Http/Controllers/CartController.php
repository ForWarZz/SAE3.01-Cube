<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartAddRequest;
use App\Http\Requests\CartDeleteRequest;
use App\Models\ArticleReference;
use App\Models\Size;
use App\Services\CartService;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
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
        $cartItems = $this->cartService->getCartFromSession();

        $cartData = [];

        $summaryData = [
            'subtotal' => 0,
            'tax' => 0,
            'total' => 0,
        ];

        foreach ($cartItems as &$item) {
            $reference = ArticleReference::with(['article', 'bikeReference', 'bikeReference.color'])->find($item['reference_id']);
            $size = Size::find($item['size_id']);
            $article = $reference->bikeReference->article ?? $reference->accessory->article;

            $cartData[] = [
                'reference' => $reference->bikeReference ?? $reference->accessory,
                'img_url' => $article->getCoverUrl($reference->bikeReference?->color->id_couleur ?? null),
                'size' => $size,
                'quantity' => $item['quantity'],
                'article' => $article,
                'color' => $reference->bikeReference?->color->label_couleur,
            ];

            $summaryData['subtotal'] += $article->prix_article * $item['quantity'];
            $summaryData['tax'] += ($article->prix_article * 0.2) * $item['quantity'];
            $summaryData['total'] += ($article->prix_article * 1.2) * $item['quantity'];
        }

        return view('cart.index', [
            'cartItems' => $cartData,
            'summaryData' => $summaryData,
        ]);
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
}
