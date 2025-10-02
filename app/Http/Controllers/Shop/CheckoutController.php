<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Customer;
use App\Models\Shop\Order;
use App\Models\Shop\OrderItem;
use App\Models\Payment\Payment;
use App\Services\Cart\CartService;
use App\Services\PaymentGateway\GateSDKService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    protected $cartService;
    protected $gateSDKService;

    public function __construct(CartService $cartService, GateSDKService $gateSDKService)
    {
        $this->cartService = $cartService;
        $this->gateSDKService = $gateSDKService;
    }

    /**
     * Show the checkout flow - redirects to appropriate step
     */
    public function index()
    {
        // Check if cart is empty
        if ($this->cartService->isEmpty()) {
            return redirect()->route('shop.cart.index')
                ->with('error', 'Your cart is empty. Please add items before checkout.');
        }

        // Determine current step
        $step = $this->getCurrentStep();
        return redirect()->route('shop.checkout.step', ['step' => $step]);
    }

    /**
     * Show specific checkout step
     */
    public function showStep($step)
    {
        // Validate step
        if (!in_array($step, ['customer', 'payment', 'review', 'complete'])) {
            return redirect()->route('shop.checkout.index');
        }

        // Allow payment step with error parameter OR if there's a failed order to retry
        $hasPaymentError = ($step === 'payment' && request()->has('error'));
        $hasFailedOrder = ($step === 'payment' && request()->has('retry_order'));

        // Check if cart is empty - but allow certain exceptions
        if ($this->cartService->isEmpty() && $step !== 'complete' && !$hasPaymentError && !$hasFailedOrder) {
            return redirect()->route('shop.cart.index')
                ->with('error', 'Your cart is empty. Please add items before checkout.');
        }

        // Get cart data
        $cartItems = $this->cartService->getCartWithProducts()->toArray();
        $cartTotal = $this->cartService->getTotal();
        $cartCount = $this->cartService->getItemCount();

        switch ($step) {
            case 'customer':
                return $this->showCustomerStep($cartItems, $cartTotal);
            case 'payment':
                return $this->showPaymentStep($cartItems, $cartTotal);
            case 'review':
                return $this->showReviewStep($cartItems, $cartTotal);
            case 'complete':
                return $this->showCompleteStep();
            default:
                return redirect()->route('shop.checkout.index');
        }
    }

    /**
     * Process customer information step
     */
    public function processCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Store customer data in session
        Session::put('checkout.customer', $request->only(['email', 'phone', 'first_name', 'last_name']));

        $redirectUrl = route('shop.checkout.step', ['step' => 'payment']);

        \Log::info('Customer processing completed', [
            'redirect_url' => $redirectUrl,
            'customer_data' => $request->only(['email', 'phone', 'first_name', 'last_name'])
        ]);

        return response()->json([
            'success' => true,
            'redirect' => $redirectUrl
        ]);
    }

    /**
     * Process payment method selection
     */
    public function processPayment(Request $request)
    {
        $enabledPaymentMethods = collect(config('showcase.payment_methods'))
            ->filter(fn($method) => $method['enabled'])
            ->keys()
            ->implode(',');

        $validator = Validator::make($request->all(), [
            'payment_method' => "required|in:{$enabledPaymentMethods}"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Store payment method in session
        Session::put('checkout.payment_method', $request->payment_method);

        return response()->json([
            'success' => true,
            'redirect' => route('shop.checkout.step', ['step' => 'review'])
        ]);
    }

    /**
     * Process final order creation and payment
     */
    public function processOrder(Request $request)
    {
        try {
            DB::beginTransaction();

            // Get checkout data from session
            $customerData = Session::get('checkout.customer');
            $paymentMethod = Session::get('checkout.payment_method');

            if (!$customerData || !$paymentMethod) {
                return response()->json([
                    'success' => false,
                    'message' => 'Checkout session expired. Please start over.'
                ], 400);
            }

            // Get cart data
            $cartItems = $this->cartService->getCartWithProducts()->toArray();
            $cartTotal = $this->cartService->getTotal();

            if (empty($cartItems)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty.'
                ], 400);
            }

            // Create or get customer
            $customer = Customer::firstOrCreate(
                ['email' => $customerData['email']],
                $customerData
            );

            // Create client in Gate SDK if not exists
            if (!$customer->gateway_client_id) {
                try {
                    $this->gateSDKService->createClient($customer);
                } catch (\Exception $e) {
                    // SDK errors should halt the checkout process
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment Gateway Configuration Error',
                        'details' => $e->getMessage()
                    ], 500);
                }
            }

            // Create order
            $order = Order::create([
                'customer_id' => $customer->id,
                'status' => 'pending',
                'total' => $cartTotal,
                'subtotal' => $cartTotal, // For now, subtotal equals total (no additional fees)
                'tax_amount' => 0,
                'shipping_amount' => 0,
                'currency' => 'EUR', // Gateway expects EUR currency
                'payment_method' => $paymentMethod
            ]);

            // Create order items
            foreach ($cartItems as $item) {
                $unitPrice = $item['product']->price;
                $totalPrice = $unitPrice * $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'product_name' => $item['product']->name,
                    'product_description' => $item['product']->description,
                    'product_snapshot' => [
                        'id' => $item['product']->id,
                        'name' => $item['product']->name,
                        'price' => $item['product']->price,
                        'currency' => $item['product']->currency,
                        'description' => $item['product']->description,
                        'image' => $item['product']->image,
                        'snapshot_at' => now()->toISOString()
                    ]
                ]);
            }

            // Recalculate totals based on order items
            $order->calculateTotals();

            // Create order in Gate SDK
            try {
                $gatewayOrderData = $this->gateSDKService->createOrder($order);
                $order->update(['gateway_order_id' => $gatewayOrderData['id']]);
            } catch (\Exception $e) {
                // SDK errors should halt the checkout process
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Payment Gateway Configuration Error',
                    'details' => $e->getMessage()
                ], 500);
            }

            // Process payment based on method
            $paymentResult = $this->processPaymentMethod($order, $paymentMethod, $request);

            if (!$paymentResult['success']) {
                DB::rollBack();
                return response()->json($paymentResult);
            }

            DB::commit();

            // Handle different payment result types
            if (isset($paymentResult['redirect_to_gateway']) && $paymentResult['redirect_to_gateway']) {
                // Redirect to Gateway for payment processing
                // Clear cart and checkout session since order is created
                $this->cartService->clear();
                Session::forget('checkout');

                // Store order ID for potential return and retry functionality
                Session::put('processing_order_id', $order->id);
                Session::put('checkout.last_order_id', $order->id);

                return response()->json([
                    'success' => true,
                    'redirect_to_gateway' => true,
                    'payment_url' => $paymentResult['payment_url'],
                    'order_id' => $order->id
                ]);
            } else {
                // Payment completed internally (shouldn't happen with new flow)
                $this->cartService->clear();
                Session::forget('checkout');
                Session::put('completed_order_id', $order->id);

                return response()->json([
                    'success' => true,
                    'redirect' => route('shop.checkout.step', ['step' => 'complete'])
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout process failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your order. Please try again.'
            ], 500);
        }
    }

    /**
     * Show customer information step
     */
    private function showCustomerStep($cartItems, $cartTotal)
    {
        $customerData = Session::get('checkout.customer', []);

        return view('shop.checkout.customer', compact('cartItems', 'cartTotal', 'customerData'));
    }

    /**
     * Show payment method selection step
     */
    private function showPaymentStep($cartItems, $cartTotal)
    {
        // Handle retry from failed order
        if (request()->has('retry_order')) {
            $orderId = request()->get('retry_order');
            $order = Order::find($orderId);

            if ($order && $order->canBePaid()) {
                // If order has existing payment URL, redirect directly to Gateway
                $paymentUrl = $order->getPaymentUrl();
                if ($paymentUrl) {
                    return redirect($paymentUrl);
                }

                // If no payment URL exists, restore session data to allow reprocessing
                Session::put('checkout.customer', [
                    'email' => $order->customer->email,
                    'phone' => $order->customer->phone,
                    'first_name' => $order->customer->first_name,
                    'last_name' => $order->customer->last_name,
                ]);

                Session::put('checkout.payment_method', $order->payment_method);

                // Show notice that we're retrying the order
                session()->flash('info', 'Retrying order #' . $order->id . '. Please proceed to review and payment.');

                return redirect()->route('shop.checkout.step', ['step' => 'review']);
            }
        }

        // Handle payment error returns from Gateway
        $paymentError = request()->get('error');
        if ($paymentError === 'payment_failed') {
            // Get the failed order from URL parameter, fallback to session
            $lastOrder = null;
            if (request()->has('order')) {
                $lastOrder = Order::find(request()->get('order'));
            } elseif (Session::has('checkout.last_order_id')) {
                $lastOrder = Order::find(Session::get('checkout.last_order_id'));
            }

            return view('shop.checkout.payment-failed')
                ->with('error', 'Your payment was declined. You can try again with a different payment method or contact support.')
                ->with('lastOrder', $lastOrder);
        }

        if (!Session::has('checkout.customer')) {
            return redirect()->route('shop.checkout.step', ['step' => 'customer']);
        }

        $customerData = Session::get('checkout.customer');
        $selectedPaymentMethod = Session::get('checkout.payment_method');

        return view('shop.checkout.payment', compact('cartItems', 'cartTotal', 'customerData', 'selectedPaymentMethod'));
    }

    /**
     * Show order review step
     */
    private function showReviewStep($cartItems, $cartTotal)
    {
        if (!Session::has('checkout.customer') || !Session::has('checkout.payment_method')) {
            return redirect()->route('shop.checkout.step', ['step' => 'customer']);
        }

        $customerData = Session::get('checkout.customer');
        $paymentMethod = Session::get('checkout.payment_method');

        return view('shop.checkout.review', compact('cartItems', 'cartTotal', 'customerData', 'paymentMethod'));
    }

    /**
     * Show order completion step
     */
    private function showCompleteStep()
    {
        // Log all incoming parameters for debugging
        \Log::info('Complete step accessed', [
            'url' => request()->fullUrl(),
            'all_params' => request()->all(),
            'referrer' => request()->header('referer'),
            'user_agent' => request()->header('user-agent')
        ]);

        // Check for order ID from URL parameter (Gateway return)
        $orderIdFromUrl = request()->get('order');

        // Check session for completed order ID
        $orderIdFromSession = Session::get('completed_order_id') ?: Session::get('processing_order_id');

        // Prefer URL parameter (Gateway return) over session
        $orderId = $orderIdFromUrl ?: $orderIdFromSession;

        if (!$orderId) {
            return redirect()->route('shop.products.index')
                ->with('error', 'No order found to display.');
        }

        $order = Order::with(['customer', 'items.product', 'payments'])->find($orderId);
        if (!$order) {
            return redirect()->route('shop.products.index')
                ->with('error', 'Order not found.');
        }

        // If returning from Gateway, sync order status
        if ($orderIdFromUrl) {
            try {
                $this->syncOrderStatusFromGateway($order);
            } catch (\Exception $e) {
                \Log::warning('Failed to sync order status from Gateway: ' . $e->getMessage());
                // Continue showing the page even if sync fails
            }
        }

        // Clear processing session if order is found
        Session::forget(['completed_order_id', 'processing_order_id']);

        // Handle AJAX status check requests
        if (request()->ajax() && request()->has('ajax')) {
            $previousStatus = request()->session()->get('last_order_status_' . $order->id);
            $currentStatus = $order->status;

            // Store current status for next comparison
            request()->session()->put('last_order_status_' . $order->id, $currentStatus);

            return response()->json([
                'status_changed' => $previousStatus !== $currentStatus,
                'current_status' => $currentStatus,
                'previous_status' => $previousStatus
            ]);
        }

        return view('shop.checkout.complete', compact('order'));
    }

    /**
     * Sync order status from Gateway when user returns
     */
    private function syncOrderStatusFromGateway(Order $order)
    {
        if (!$order->gateway_order_id) {
            return;
        }

        try {
            // Get order status from Gateway
            $gatewayOrder = $this->gateSDKService->getOrder($order);

            // Update local order status based on Gateway response
            $gatewayStatus = $gatewayOrder['status'] ?? 'unknown';

            $order->update([
                'status' => $gatewayStatus,
                'gateway_data' => array_merge($order->gateway_data ?? [], $gatewayOrder)
            ]);

            // Update payment status if payments exist
            if ($order->payments->isNotEmpty()) {
                $payment = $order->payments->first();

                $paymentStatus = match($gatewayStatus) {
                    'paid' => 'completed',
                    'failed', 'cancelled', 'expired', 'rejected' => 'failed',
                    default => 'pending'
                };

                $existingResponse = is_array($payment->gateway_response) ? $payment->gateway_response : (json_decode($payment->gateway_response, true) ?? []);

                $payment->update([
                    'status' => $paymentStatus,
                    'gateway_response' => array_merge(
                        $existingResponse,
                        ['gateway_sync' => $gatewayOrder, 'synced_at' => now()]
                    )
                ]);
            }

            \Log::info('Order status synced from Gateway', [
                'order_id' => $order->id,
                'gateway_status' => $gatewayStatus,
                'local_status' => $order->status
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to sync order status from Gateway', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get current checkout step based on session data
     */
    private function getCurrentStep()
    {
        if (!Session::has('checkout.customer')) {
            return 'customer';
        }
        if (!Session::has('checkout.payment_method')) {
            return 'payment';
        }
        return 'review';
    }

    /**
     * Process payment based on selected method
     */
    private function processPaymentMethod(Order $order, $paymentMethod, Request $request)
    {
        try {
            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'method' => $paymentMethod,
                'amount' => $order->total
            ]);

            // Demo: Simulate payment processing
            switch ($paymentMethod) {
                case 'card':
                    return $this->processCardPayment($payment, $request);
                case 'apple_pay':
                    return $this->processApplePayment($payment);
                case 'google_pay':
                    return $this->processGooglePayment($payment);
                case 'paypal':
                    return $this->processPayPalPayment($payment);
                case 'klarna':
                    return $this->processKlarnaPayment($payment);
                default:
                    return ['success' => false, 'message' => 'Invalid payment method'];
            }
        } catch (\Exception $e) {
            \Log::error('Payment processing failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Payment processing failed'];
        }
    }

    /**
     * Process card payment (demo)
     */
    private function processCardPayment(Payment $payment, Request $request)
    {
        try {
            $order = $payment->order;

            // For card payments, redirect to the Gateway payment page
            Log::info('Extracting payment URL', [
                'order_id' => $order->id,
                'all_payment_urls' => $order->payment_urls,
                'available_keys' => array_keys($order->payment_urls ?? [])
            ]);

            $paymentUrl = $order->payment_urls['full_page_checkout'] ??
                         $order->payment_urls['api_do_url'] ??
                         $order->payment_urls['iframe_checkout'] ?? null;

            Log::info('Payment URL extraction result', [
                'order_id' => $order->id,
                'extracted_payment_url' => $paymentUrl,
                'full_page_checkout' => $order->payment_urls['full_page_checkout'] ?? 'NOT_FOUND',
                'api_do_url' => $order->payment_urls['api_do_url'] ?? 'NOT_FOUND',
                'iframe_checkout' => $order->payment_urls['iframe_checkout'] ?? 'NOT_FOUND'
            ]);

            if (!$paymentUrl) {
                Log::error('Payment URL not found', [
                    'order_id' => $order->id,
                    'payment_urls' => $order->payment_urls
                ]);
                throw new \Exception('Payment page URL not available. Available URLs: ' . json_encode(array_keys($order->payment_urls ?? [])));
            }

            $payment->update([
                'status' => 'pending',
                'gateway_response' => json_encode([
                    'payment_url' => $paymentUrl,
                    'method' => 'card',
                    'timestamp' => now()
                ])
            ]);

            $order->update(['status' => 'issued']);

            return [
                'success' => true,
                'redirect_to_gateway' => true,
                'payment_url' => $paymentUrl
            ];

        } catch (\Exception $e) {
            \Log::error('Card payment processing failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Card payment setup failed: ' . $e->getMessage()];
        }
    }

    /**
     * Process Apple Pay payment (demo)
     */
    private function processApplePayment(Payment $payment)
    {
        try {
            $order = $payment->order;

            // For Apple Pay, redirect to the Gateway payment page
            $paymentUrl = $order->payment_urls['payment_page'] ?? null;

            if (!$paymentUrl) {
                throw new \Exception('Apple Pay URL not available');
            }

            $payment->update([
                'status' => 'pending',
                'gateway_response' => json_encode([
                    'payment_url' => $paymentUrl,
                    'method' => 'apple_pay',
                    'timestamp' => now()
                ])
            ]);

            $order->update(['status' => 'issued']);

            return [
                'success' => true,
                'redirect_to_gateway' => true,
                'payment_url' => $paymentUrl
            ];

        } catch (\Exception $e) {
            \Log::error('Apple Pay processing failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Apple Pay setup failed: ' . $e->getMessage()];
        }
    }

    /**
     * Process Google Pay payment (demo)
     */
    private function processGooglePayment(Payment $payment)
    {
        try {
            $order = $payment->order;

            // For Google Pay, redirect to the Gateway payment page
            $paymentUrl = $order->payment_urls['payment_page'] ?? null;

            if (!$paymentUrl) {
                throw new \Exception('Google Pay URL not available');
            }

            $payment->update([
                'status' => 'pending',
                'gateway_response' => json_encode([
                    'payment_url' => $paymentUrl,
                    'method' => 'google_pay',
                    'timestamp' => now()
                ])
            ]);

            $order->update(['status' => 'issued']);

            return [
                'success' => true,
                'redirect_to_gateway' => true,
                'payment_url' => $paymentUrl
            ];

        } catch (\Exception $e) {
            \Log::error('Google Pay processing failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Google Pay setup failed: ' . $e->getMessage()];
        }
    }

    /**
     * Process PayPal payment (demo)
     */
    private function processPayPalPayment(Payment $payment)
    {
        try {
            $order = $payment->order;

            // For PayPal, redirect to the Gateway payment page
            $paymentUrl = $order->payment_urls['payment_page'] ?? null;

            if (!$paymentUrl) {
                throw new \Exception('PayPal URL not available');
            }

            $payment->update([
                'status' => 'pending',
                'gateway_response' => json_encode([
                    'payment_url' => $paymentUrl,
                    'method' => 'paypal',
                    'timestamp' => now()
                ])
            ]);

            $order->update(['status' => 'issued']);

            return [
                'success' => true,
                'redirect_to_gateway' => true,
                'payment_url' => $paymentUrl
            ];

        } catch (\Exception $e) {
            \Log::error('PayPal processing failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'PayPal setup failed: ' . $e->getMessage()];
        }
    }

    /**
     * Process Klarna payment (demo)
     */
    private function processKlarnaPayment(Payment $payment)
    {
        try {
            $order = $payment->order;

            // For Klarna, redirect to the Gateway payment page
            $paymentUrl = $order->payment_urls['payment_page'] ?? null;

            if (!$paymentUrl) {
                throw new \Exception('Klarna URL not available');
            }

            $payment->update([
                'status' => 'pending',
                'gateway_response' => json_encode([
                    'payment_url' => $paymentUrl,
                    'method' => 'klarna',
                    'timestamp' => now()
                ])
            ]);

            $order->update(['status' => 'issued']);

            return [
                'success' => true,
                'redirect_to_gateway' => true,
                'payment_url' => $paymentUrl
            ];

        } catch (\Exception $e) {
            \Log::error('Klarna processing failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Klarna setup failed: ' . $e->getMessage()];
        }
    }

    /**
     * Handle payment callback
     */
    public function callback(Order $order, Request $request)
    {
        // Handle payment gateway callback
        return view('shop.checkout.callback', compact('order'));
    }
}