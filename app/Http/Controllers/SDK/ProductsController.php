<?php

namespace App\Http\Controllers\SDK;

use App\Http\Controllers\Controller;
use App\Services\PaymentGateway\GateSDKService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductsController extends Controller
{
    protected $gateSDKService;

    public function __construct(GateSDKService $gateSDKService)
    {
        $this->gateSDKService = $gateSDKService;
    }

    public function index()
    {
        return view('sdk.products.index');
    }

    public function test()
    {
        return response()->json([
            'success' => true,
            'message' => 'Controller is working',
            'service' => get_class($this->gateSDKService)
        ]);
    }

    public function testDelete(Request $request)
    {
        if (!$request->has('product_id')) {
            return response()->json([
                'error' => 'Please provide product_id parameter'
            ]);
        }

        try {
            Log::info('Test delete called with product_id: ' . $request->product_id);
            $result = $this->gateSDKService->deleteProductRaw($request->product_id);

            return response()->json([
                'success' => true,
                'message' => 'Delete test completed',
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    public function createProduct(Request $request)
    {
        Log::info('SDK createProduct called', ['request_data' => $request->all()]);

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0.01',
                'currency' => 'required|string|size:3'
            ]);

            $productData = [
                'name' => $request->name,
                'amount' => $request->price * 100, // Convert to cents
                'currency' => strtoupper($request->currency),
                'type' => 'product'
            ];

            Log::info('About to call createProductRaw', ['product_data' => $productData]);

            $result = $this->gateSDKService->createProductRaw($productData);

            Log::info('SDK Product Created', ['request' => $productData, 'response' => $result]);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully in Gateway',
                'data' => $result,
                'gateway_product_id' => $result['id'] ?? null
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('SDK Product Creation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create product: ' . $e->getMessage(),
                'error_type' => get_class($e),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
    }

    public function getProducts(Request $request)
    {
        try {
            $filters = [];

            // Note: Products API uses cursor-based pagination, not limit/offset
            if ($request->has('cursor')) {
                $filters['cursor'] = $request->cursor;
            }

            if ($request->has('q')) {
                $filters['search_query'] = $request->q;
            }

            if ($request->has('filter_title')) {
                $filters['filter_title'] = $request->filter_title;
            }

            if ($request->has('filter_price')) {
                $filters['filter_price'] = $request->filter_price;
            }

            $result = $this->gateSDKService->getProducts($filters);

            Log::info('SDK Products Retrieved', ['filters' => $filters, 'count' => count($result['data'] ?? [])]);

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully from Gateway',
                'data' => $result,
                'total_count' => count($result['data'] ?? [])
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Products Retrieval Failed', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve products: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    public function getProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string'
        ]);

        try {
            $result = $this->gateSDKService->getProductRaw($request->product_id);

            Log::info('SDK Product Retrieved', ['product_id' => $request->product_id, 'response' => $result]);

            return response()->json([
                'success' => true,
                'message' => 'Product retrieved successfully from Gateway',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Product Retrieval Failed', [
                'error' => $e->getMessage(),
                'product_id' => $request->product_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve product: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    public function updateProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|string',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3'
        ]);

        try {
            $productData = [
                'name' => $request->name,
                'amount' => $request->price * 100, // Convert to cents
                'currency' => strtoupper($request->currency),
                'type' => 'product'
            ];

            $result = $this->gateSDKService->updateProductRaw($request->product_id, $productData);

            Log::info('SDK Product Updated', [
                'product_id' => $request->product_id,
                'request' => $productData,
                'response' => $result
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully in Gateway',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Product Update Failed', [
                'error' => $e->getMessage(),
                'product_id' => $request->product_id,
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update product: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    public function deleteProduct(Request $request)
    {
        Log::info('SDK deleteProduct called', ['request_data' => $request->all()]);

        try {
            $request->validate([
                'product_id' => 'required|string'
            ]);

            Log::info('About to call deleteProductRaw', ['product_id' => $request->product_id]);

            $result = $this->gateSDKService->deleteProductRaw($request->product_id);

            Log::info('SDK Product Deleted successfully', [
                'product_id' => $request->product_id,
                'response' => $result
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully from Gateway',
                'data' => $result
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed for delete', ['errors' => $e->errors()]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('SDK Product Deletion Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'product_id' => $request->product_id,
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product: ' . $e->getMessage(),
                'error_type' => get_class($e),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
    }
}