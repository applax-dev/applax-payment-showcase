<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Product;
use App\Services\Cart\CartService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Display cart contents
     */
    public function index(): View
    {
        // Validate cart and remove any inactive products
        $validation = $this->cartService->validateCart();
        $removedItems = $validation['removed'];

        // Get cart with full product details
        $cartItems = $this->cartService->getCartWithProducts();
        $cartSummary = $this->cartService->getCartSummary();

        return view('shop.cart.index', compact('cartItems', 'cartSummary', 'removedItems'));
    }

    /**
     * Add product to cart (AJAX)
     */
    public function addProduct(Request $request, Product $product): JsonResponse
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1|max:10'
            ]);

            $quantity = (int) $request->input('quantity', 1);
            $result = $this->cartService->addProduct($product, $quantity);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update product quantity in cart (AJAX)
     */
    public function updateQuantity(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'quantity' => 'required|integer|min:0|max:10'
            ]);

            $productId = (int) $request->input('product_id');
            $quantity = (int) $request->input('quantity');

            $this->cartService->updateQuantity($productId, $quantity);

            $cartSummary = $this->cartService->getCartSummary();

            return response()->json([
                'success' => true,
                'message' => $quantity > 0 ? 'Cart updated successfully' : 'Product removed from cart',
                'cart_summary' => $cartSummary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove product from cart (AJAX)
     */
    public function removeProduct(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'product_id' => 'required|integer'
            ]);

            $productId = (int) $request->input('product_id');
            $this->cartService->removeProduct($productId);

            $cartSummary = $this->cartService->getCartSummary();

            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart successfully',
                'cart_summary' => $cartSummary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Clear entire cart (AJAX)
     */
    public function clear(): JsonResponse
    {
        try {
            $this->cartService->clear();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully',
                'cart_summary' => $this->cartService->getCartSummary()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get cart data (AJAX)
     */
    public function getCartData(): JsonResponse
    {
        try {
            $cartSummary = $this->cartService->getCartSummary();

            return response()->json($cartSummary);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get cart widget data for navigation (AJAX)
     */
    public function getCartWidget(): JsonResponse
    {
        try {
            $cartItems = $this->cartService->getCartWithProducts();
            $cartSummary = $this->cartService->getCartSummary();

            // Limit to first 3 items for widget display
            $widgetItems = $cartItems->take(3);

            return response()->json([
                'success' => true,
                'items' => $widgetItems->values()->toArray(),
                'total_items' => $cartItems->count(),
                'cart_count' => $cartSummary['count'],
                'formatted_total' => $cartSummary['formatted_total'],
                'has_more_items' => $cartItems->count() > 3
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Validate cart before checkout
     */
    public function validate(): JsonResponse
    {
        try {
            $validation = $this->cartService->validateCart();
            $orderData = $this->cartService->prepareForOrder();

            return response()->json([
                'success' => true,
                'is_valid' => true,
                'removed_items' => $validation['removed'],
                'order_data' => $orderData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'is_valid' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Apply promotional code (placeholder for future implementation)
     */
    public function applyPromoCode(Request $request): JsonResponse
    {
        $request->validate([
            'promo_code' => 'required|string|max:50'
        ]);

        // Placeholder for promo code logic
        return response()->json([
            'success' => false,
            'message' => 'Promotional codes are not available in the demo version'
        ], 400);
    }

    /**
     * Proceed to checkout
     */
    public function proceedToCheckout(Request $request)
    {
        try {
            // Validate cart
            $orderData = $this->cartService->prepareForOrder();

            // Store cart data in session for checkout process
            session(['checkout_data' => $orderData]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => route('shop.checkout.index')
                ]);
            }

            return redirect()->route('shop.checkout.index');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->back()->withErrors(['cart' => $e->getMessage()]);
        }
    }
}