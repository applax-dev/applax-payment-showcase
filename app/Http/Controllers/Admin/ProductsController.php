<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop\Product;
use App\Services\PaymentGateway\GateSDKService;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    protected $gateSDKService;

    public function __construct(GateSDKService $gateSDKService)
    {
        $this->gateSDKService = $gateSDKService;
    }

    public function index(Request $request)
    {
        $query = Product::orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $products = $query->paginate(20)->appends($request->query());

        return view('admin.products.index', compact('products'));
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'image' => 'nullable|url',
            'status' => 'required|in:active,inactive'
        ]);

        $product = Product::create($request->all());

        try {
            $this->gateSDKService->createProduct($product);
        } catch (\Exception $e) {
            // Product created locally but Gateway sync failed
        }

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'image' => 'nullable|url',
            'status' => 'required|in:active,inactive'
        ]);

        $product->update($request->all());

        try {
            if ($product->gateway_product_id) {
                $this->gateSDKService->updateProduct($product);
            }
        } catch (\Exception $e) {
            // Product updated locally but Gateway sync failed
        }

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        try {
            if ($product->gateway_product_id) {
                $this->gateSDKService->deleteProduct($product);
            }
        } catch (\Exception $e) {
            // Continue with local deletion even if Gateway deletion fails
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully');
    }

    public function syncWithGateway(Product $product)
    {
        try {
            if (!$product->gateway_product_id) {
                $this->gateSDKService->createProduct($product);
            } else {
                $this->gateSDKService->updateProduct($product);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product synchronized with Gateway successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        }
    }
}