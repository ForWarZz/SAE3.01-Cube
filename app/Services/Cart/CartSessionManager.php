<?php

namespace App\Services\Cart;

use Illuminate\Support\Facades\Session;

class CartSessionManager
{
    private const CART_KEY = 'cart';

    private const DISCOUNT_KEY = 'discount_code';

    private const CHECKOUT_KEY = 'checkout';

    public function addItem(int $referenceId, int $sizeId, int $quantity = 1): void
    {
        $cart = $this->getItems();
        $key = $this->makeKey($referenceId, $sizeId);

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
        } else {
            $cart[$key] = [
                'reference_id' => $referenceId,
                'size_id' => $sizeId,
                'quantity' => $quantity,
            ];
        }

        Session::put(self::CART_KEY, $cart);
    }

    /**
     * @return array<string, array{reference_id: int, size_id: int, quantity: int}>
     */
    public function getItems(): array
    {
        return Session::get(self::CART_KEY, []);
    }

    private function makeKey(int $referenceId, int $sizeId): string
    {
        return $referenceId.'_'.$sizeId;
    }

    public function updateQuantity(int $referenceId, int $sizeId, int $quantity): void
    {
        $cart = $this->getItems();
        $key = $this->makeKey($referenceId, $sizeId);

        if (isset($cart[$key])) {
            if ($quantity <= 0) {
                unset($cart[$key]);
            } else {
                $cart[$key]['quantity'] = $quantity;
            }
            Session::put(self::CART_KEY, $cart);
        }
    }

    public function removeItem(int $referenceId, int $sizeId): void
    {
        $cart = $this->getItems();
        $key = $this->makeKey($referenceId, $sizeId);

        if (isset($cart[$key])) {
            unset($cart[$key]);
            Session::put(self::CART_KEY, $cart);
        }
    }

    public function isEmpty(): bool
    {
        return empty($this->getItems());
    }

    public function getDiscountCodeId(): ?int
    {
        return Session::get(self::DISCOUNT_KEY);
    }

    public function setDiscountCodeId(int $discountCodeId): void
    {
        Session::put(self::DISCOUNT_KEY, $discountCodeId);
    }

    /**
    * @return array{billing_address_id?: int|null, delivery_address_id?: int|null, shipping_mode_id?: int|null, shop_id?: int|null}
     */
    public function getCheckoutData(): array
    {
        return Session::get(self::CHECKOUT_KEY, []);
    }

    public function setCheckoutData(?int $billingAddressId, ?int $deliveryAddressId, ?int $shippingModeId, ?int $shopId = null): void
    {
        Session::put(self::CHECKOUT_KEY, [
            'billing_address_id' => $billingAddressId,
            'delivery_address_id' => $deliveryAddressId,
            'shipping_mode_id' => $shippingModeId,
            'shop_id' => $shopId,
        ]);
    }

    public function clearAll(): void
    {
        $this->clearCart();
        $this->clearDiscountCode();
        $this->clearCheckout();
    }

    public function clearCart(): void
    {
        Session::forget(self::CART_KEY);
    }

    public function clearDiscountCode(): void
    {
        Session::forget(self::DISCOUNT_KEY);
    }

    public function clearCheckout(): void
    {
        Session::forget(self::CHECKOUT_KEY);
    }
}
