<?php

namespace App\Services\Cart;

use App\Models\Shop\Product;
use Illuminate\Support\Collection;
use Illuminate\Session\Store as Session;

class CartService
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Add product to cart
     */
    public function addProduct(Product $product, int $quantity = 1): array
    {
        if (!$product->isActive()) {
            throw new \Exception('Product is not available');
        }

        if ($quantity < 1 || $quantity > 10) {
            throw new \Exception('Invalid quantity. Must be between 1 and 10.');
        }

        $cart = $this->getCart();
        $productId = $product->id;

        // If product already in cart, update quantity
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;

            // Ensure we don't exceed max quantity
            if ($cart[$productId]['quantity'] > 10) {
                $cart[$productId]['quantity'] = 10;
            }
        } else {
            // Add new product to cart
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'currency' => $product->currency,
                'image' => $product->image,
                'quantity' => $quantity
            ];
        }

        $this->updateCart($cart);

        return [
            'success' => true,
            'message' => 'Product added to cart successfully',
            'cart_count' => $this->getItemCount()
        ];
    }

    /**
     * Update product quantity in cart
     */
    public function updateQuantity(int $productId, int $quantity): void
    {
        if ($quantity < 0 || $quantity > 10) {
            throw new \Exception('Invalid quantity. Must be between 0 and 10.');
        }

        $cart = $this->getCart();

        if ($quantity === 0) {
            unset($cart[$productId]);
        } else {
            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] = $quantity;
            }
        }

        $this->updateCart($cart);
    }

    /**
     * Remove product from cart
     */
    public function removeProduct(int $productId): void
    {
        $cart = $this->getCart();
        unset($cart[$productId]);
        $this->updateCart($cart);
    }

    /**
     * Clear entire cart
     */
    public function clear(): void
    {
        $this->session->forget('cart');
        $this->session->forget('cart_count');
    }

    /**
     * Get cart contents
     */
    public function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    /**
     * Get cart as collection
     */
    public function getCartCollection(): Collection
    {
        return collect($this->getCart());
    }

    /**
     * Get cart items with full product details
     */
    public function getCartWithProducts(): Collection
    {
        $cart = $this->getCart();
        $productIds = array_keys($cart);

        if (empty($productIds)) {
            return collect();
        }

        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        return collect($cart)->map(function ($item) use ($products) {
            $product = $products[$item['id']] ?? null;

            if (!$product || !$product->isActive()) {
                return null;
            }

            return array_merge($item, [
                'product' => $product,
                'subtotal' => $item['price'] * $item['quantity'],
                'formatted_price' => number_format($item['price'], 2) . ' ' . strtoupper($item['currency']),
                'formatted_subtotal' => number_format($item['price'] * $item['quantity'], 2) . ' ' . strtoupper($item['currency'])
            ]);
        })->filter(); // Remove null items (inactive products)
    }

    /**
     * Get total number of items in cart
     */
    public function getItemCount(): int
    {
        return collect($this->getCart())->sum('quantity');
    }

    /**
     * Get cart total amount
     */
    public function getTotal(): float
    {
        return collect($this->getCart())->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    /**
     * Get formatted cart total
     */
    public function getFormattedTotal(): string
    {
        $cart = $this->getCart();
        $currency = collect($cart)->first()['currency'] ?? 'EUR';

        return number_format($this->getTotal(), 2) . ' ' . strtoupper($currency);
    }

    /**
     * Get cart summary for API responses
     */
    public function getCartSummary(): array
    {
        $cart = $this->getCart();
        $cartItems = collect($cart);

        return [
            'items' => $cartItems->values()->toArray(),
            'count' => $this->getItemCount(),
            'total' => $this->getTotal(),
            'formatted_total' => $this->getFormattedTotal(),
            'currency' => $cartItems->first()['currency'] ?? 'EUR',
            'is_empty' => $cartItems->isEmpty()
        ];
    }

    /**
     * Check if cart is empty
     */
    public function isEmpty(): bool
    {
        return empty($this->getCart());
    }

    /**
     * Check if cart has mixed currencies
     */
    public function hasMixedCurrencies(): bool
    {
        $currencies = collect($this->getCart())->pluck('currency')->unique();
        return $currencies->count() > 1;
    }

    /**
     * Get primary currency from cart
     */
    public function getPrimaryCurrency(): string
    {
        return collect($this->getCart())->first()['currency'] ?? 'EUR';
    }

    /**
     * Validate cart contents (remove inactive products)
     */
    public function validateCart(): array
    {
        $cart = $this->getCart();
        $productIds = array_keys($cart);

        if (empty($productIds)) {
            return ['removed' => [], 'cart' => []];
        }

        $activeProducts = Product::active()->whereIn('id', $productIds)->pluck('id')->toArray();
        $removedProducts = [];

        foreach ($cart as $productId => $item) {
            if (!in_array($productId, $activeProducts)) {
                $removedProducts[] = $item;
                unset($cart[$productId]);
            }
        }

        if (!empty($removedProducts)) {
            $this->updateCart($cart);
        }

        return [
            'removed' => $removedProducts,
            'cart' => $cart
        ];
    }

    /**
     * Prepare cart data for order creation
     */
    public function prepareForOrder(): array
    {
        $validation = $this->validateCart();

        if (!empty($validation['removed'])) {
            throw new \Exception('Some items were removed from your cart because they are no longer available. Please review your cart.');
        }

        $cart = $this->getCartWithProducts();

        if ($cart->isEmpty()) {
            throw new \Exception('Your cart is empty.');
        }

        if ($this->hasMixedCurrencies()) {
            throw new \Exception('Cart contains products with different currencies. Please checkout items with the same currency separately.');
        }

        return [
            'items' => $cart->toArray(),
            'total' => $this->getTotal(),
            'currency' => $this->getPrimaryCurrency(),
            'item_count' => $this->getItemCount()
        ];
    }

    /**
     * Private helper to update cart in session
     */
    private function updateCart(array $cart): void
    {
        $this->session->put('cart', $cart);
        $this->session->put('cart_count', collect($cart)->sum('quantity'));
    }
}