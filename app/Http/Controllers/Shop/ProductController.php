<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Product;
use App\Services\Cart\CartService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    /**
     * Display product catalog
     */
    public function index(Request $request)
    {
        $query = Product::active()->orderBy('name');

        // Handle search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Handle price filtering
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->input('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->input('price_max'));
        }

        // Handle currency filtering
        if ($request->filled('currency')) {
            $query->forCurrency($request->input('currency'));
        }

        $products = $query->paginate(12);

        // Get available currencies for filter
        $currencies = Product::active()
            ->distinct()
            ->pluck('currency')
            ->sort()
            ->values();

        // Calculate price range for filters
        $priceStats = Product::active()
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return view('shop.products.index', compact(
            'products',
            'currencies',
            'priceStats'
        ));
    }

    /**
     * Show single product
     */
    public function show(Product $product)
    {
        if (!$product->isActive()) {
            abort(404, 'Product not available');
        }

        // Get related products (same price range)
        $relatedProducts = Product::active()
            ->where('id', '!=', $product->id)
            ->where('price', '>=', $product->price * 0.8)
            ->where('price', '<=', $product->price * 1.2)
            ->where('currency', $product->currency)
            ->limit(4)
            ->get();

        return view('shop.products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Add product to cart
     */
    public function addToCart(Request $request, Product $product)
    {
        try {
            // Force JSON response if AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                $request->validate([
                    'quantity' => 'required|integer|min:1|max:10'
                ]);

                $quantity = (int) $request->input('quantity', 1);
                $result = $this->cartService->addProduct($product, $quantity);

                return response()->json($result);
            }

            // Regular form submission
            $request->validate([
                'quantity' => 'required|integer|min:1|max:10'
            ]);

            $quantity = (int) $request->input('quantity', 1);
            $result = $this->cartService->addProduct($product, $quantity);

            return redirect()->back()->with('success', $result['message']);

        } catch (\Throwable $e) {
            \Log::error('Add to cart error: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'request_data' => $request->all(),
                'expects_json' => $request->expectsJson(),
                'ajax' => $request->ajax(),
                'exception' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }

            return redirect()->back()->withErrors(['cart' => $e->getMessage()]);
        }
    }

    /**
     * Get cart data for AJAX requests
     */
    public function getCart()
    {
        $cart = session()->get('cart', []);
        $cartItems = collect($cart);

        $cartSummary = [
            'items' => $cartItems->values(),
            'count' => $cartItems->sum('quantity'),
            'subtotal' => $cartItems->sum(function($item) {
                return $item['price'] * $item['quantity'];
            }),
            'currency' => $cartItems->first()['currency'] ?? 'EUR'
        ];

        return response()->json($cartSummary);
    }
}