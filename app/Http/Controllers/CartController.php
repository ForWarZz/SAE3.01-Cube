<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartAddRequest;
use App\Http\Requests\CartApplyDiscountRequest;
use App\Http\Requests\CartDeleteRequest;
use App\Http\Requests\CartUpdateQuantityRequest;
use App\Models\ArticleReference;
use App\Models\DeliveryMode;
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

    public function checkout()
    {
        $cartData = $this->cartService->getCartData();
        $client = auth()->user();

        $addresses = $client->addresses()->with('ville')->get();
        $deliveryPrice = $cartData['summaryData']['subtotal'] >= 50 ? 0 : 6;

        $deliveryModes = DeliveryMode::query()
            ->when($cartData['hasBikes'], fn ($q) => $q->where('id_moyen_livraison', '=', 1)
            )
            ->get()
            ->map(function ($mode) use ($deliveryPrice) {
                $price = $mode->id_moyen_livraison === 1 ? $deliveryPrice : 6;

                return [
                    'id' => $mode->id_moyen_livraison,
                    'name' => $mode->label_moyen_livraison,
                    'price' => $price,
                ];
            });

        return view('order.checkout', array_merge($cartData, [
            'addresses' => $addresses,
            'deliveryModes' => $deliveryModes,
        ]));
    }
}
