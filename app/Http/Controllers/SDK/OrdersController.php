<?php

namespace App\Http\Controllers\SDK;

use App\Http\Controllers\Controller;
use App\Services\PaymentGateway\GateSDKService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{
    protected $gateSDKService;

    public function __construct(GateSDKService $gateSDKService)
    {
        $this->gateSDKService = $gateSDKService;
    }

    public function index()
    {
        return view('sdk.orders.index');
    }

    public function createOrder(Request $request)
    {
        Log::info('SDK createOrder called', ['request_data' => $request->all()]);

        try {
            $request->validate([
                'client_email' => 'required|email',
                'client_first_name' => 'required|string|max:255',
                'client_last_name' => 'required|string|max:255',
                'client_phone' => 'nullable|string|max:20',
                'product_title' => 'required|string|max:255',
                'product_price' => 'required|numeric|min:0.01',
                'currency' => 'required|string|size:3',
                'quantity' => 'nullable|integer|min:1'
            ]);

            $orderData = [
                'client' => [
                    'email' => $request->client_email,
                    'first_name' => $request->client_first_name,
                    'last_name' => $request->client_last_name,
                    'phone' => $this->formatPhoneNumber($request->client_phone ?? ''),
                ],
                'products' => [
                    [
                        'title' => $request->product_title,
                        'price' => (float) $request->product_price,
                        'quantity' => (int) ($request->quantity ?? 1),
                    ]
                ],
                'currency' => strtoupper($request->currency),
                'brand' => config('services.gate.brand_id'),
                'skip_capture' => true, // Enable manual capture for SDK showcase
            ];

            Log::info('About to call createOrderRaw', ['order_data' => $orderData]);

            $result = $this->gateSDKService->createOrderRaw($orderData);

            Log::info('SDK Order Created', ['request' => $orderData, 'response' => $result]);

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully in Gateway',
                'data' => $result,
                'gateway_order_id' => $result['id'] ?? null
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('SDK Order Creation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage(),
                'error_type' => get_class($e),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
    }

    public function getOrders(Request $request)
    {
        try {
            $filters = [];

            if ($request->has('limit')) {
                $filters['limit'] = min($request->limit, 50);
            }

            if ($request->has('offset')) {
                $filters['offset'] = $request->offset;
            }

            if ($request->has('status')) {
                $filters['status'] = $request->status;
            }

            $result = $this->gateSDKService->getOrdersRaw($filters);

            Log::info('SDK Orders Retrieved', ['filters' => $filters, 'count' => count($result['data'] ?? [])]);

            return response()->json([
                'success' => true,
                'message' => 'Orders retrieved successfully from Gateway',
                'data' => $result,
                'total_count' => count($result['data'] ?? [])
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Orders Retrieval Failed', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve orders: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    public function getOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string'
        ]);

        try {
            $result = $this->gateSDKService->getOrderRaw($request->order_id);

            Log::info('SDK Order Retrieved', ['order_id' => $request->order_id, 'response' => $result]);

            return response()->json([
                'success' => true,
                'message' => 'Order retrieved successfully from Gateway',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Order Retrieval Failed', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve order: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    public function capturePayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'amount' => 'nullable|numeric|min:0.01'
        ]);

        try {
            $amount = $request->amount ? (float) $request->amount : null;

            $result = $this->gateSDKService->capturePaymentRaw($request->order_id, $amount);

            Log::info('SDK Payment Captured', [
                'order_id' => $request->order_id,
                'amount' => $amount,
                'response' => $result
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment captured successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Payment Capture Failed', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id
            ]);

            $message = 'Failed to capture payment: ' . $e->getMessage();

            // Provide more helpful error message for common scenarios
            if (strpos($e->getMessage(), 'Invalid input') !== false) {
                $message = 'Cannot capture payment: The order must be paid first before it can be captured. Orders in "issued" status need to receive payment before capture is possible.';
            }

            return response()->json([
                'success' => false,
                'message' => $message,
                'error_type' => get_class($e),
                'help' => 'Orders must be paid by customers before payments can be captured. Use the order\'s payment link to complete payment first.'
            ], 500);
        }
    }

    public function refundPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255'
        ]);

        try {
            $result = $this->gateSDKService->refundPaymentRaw(
                $request->order_id,
                (float) $request->amount,
                $request->reason ?? 'SDK showcase refund'
            );

            Log::info('SDK Payment Refunded', [
                'order_id' => $request->order_id,
                'amount' => $request->amount,
                'reason' => $request->reason,
                'response' => $result
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment refunded successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Payment Refund Failed', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to refund payment: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    public function cancelOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string'
        ]);

        try {
            $result = $this->gateSDKService->cancelOrderRaw($request->order_id);

            Log::info('SDK Order Cancelled', [
                'order_id' => $request->order_id,
                'response' => $result
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Order Cancellation Failed', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    /**
     * Format phone number to the expected format (40-771604545)
     */
    private function formatPhoneNumber(string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        // Remove all non-digit characters
        $phone = preg_replace('/\D/', '', $phone);

        // If phone starts with +40, remove the +
        if (strpos($phone, '40') === 0 && strlen($phone) > 10) {
            // Already has country code, format as XX-XXXXXXXXX
            return substr($phone, 0, 2) . '-' . substr($phone, 2);
        } elseif (strlen($phone) >= 9) {
            // Assume Romanian number, add country code and format
            $phone = ltrim($phone, '0'); // Remove leading zero if present
            return '40-' . $phone;
        }

        // Return as-is if format is unclear
        return $phone;
    }
}