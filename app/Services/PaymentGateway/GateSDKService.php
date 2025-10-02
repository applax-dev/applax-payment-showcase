<?php

namespace App\Services\PaymentGateway;

use ApplaxDev\GateSDK\GateSDK;
use ApplaxDev\GateSDK\Exceptions\GateException;
use ApplaxDev\GateSDK\Exceptions\ValidationException;
use ApplaxDev\GateSDK\Exceptions\AuthenticationException;
use ApplaxDev\GateSDK\Exceptions\NotFoundException;
use ApplaxDev\GateSDK\Exceptions\RateLimitException;
use ApplaxDev\GateSDK\Exceptions\ServerException;
use ApplaxDev\GateSDK\Exceptions\NetworkException;
use App\Models\Shop\Product;
use App\Models\Shop\Customer;
use App\Models\Shop\Order;
use App\Models\Payment\Payment;
use App\Models\Payment\WebhookLog;
use Illuminate\Support\Facades\Log;
use Exception;

class GateSDKService
{
    private GateSDK $sdk;

    public function __construct()
    {
        $this->sdk = new GateSDK(
            apiKey: config('services.gate.api_key'),
            sandbox: config('services.gate.sandbox', true),
            config: [
                'timeout' => 30,
                'connect_timeout' => 10,
                'debug' => config('app.debug', false),
                'max_retries' => 3
            ]
        );
    }

    /**
     * Get direct access to the SDK for advanced operations
     */
    public function getSdk(): GateSDK
    {
        return $this->sdk;
    }

    // ===== PRODUCTS MANAGEMENT =====

    /**
     * Create a product in the gateway and sync with local database
     */
    public function createProduct(Product $product): array
    {
        try {
            $gatewayData = $this->sdk->createProduct([
                'brand' => config('services.gate.brand_id'),
                'title' => $product->name,
                'description' => $product->description,
                'price' => (float) $product->price,
                'currency' => $product->currency
            ]);

            // Update local product with gateway ID
            $product->update([
                'gateway_product_id' => $gatewayData['id'],
                'gateway_data' => $gatewayData
            ]);

            Log::info('Product created in gateway', [
                'product_id' => $product->id,
                'gateway_product_id' => $gatewayData['id']
            ]);

            return $gatewayData;

        } catch (\Exception $e) {
            Log::error('Failed to create product in gateway', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create a product in the gateway using raw data (for SDK showcase)
     */
    public function createProductRaw(array $productData): array
    {
        try {
            $gatewayData = $this->sdk->createProduct([
                'brand' => config('services.gate.brand_id'),
                'title' => $productData['name'],
                'price' => (float) ($productData['amount'] / 100), // Convert from cents to decimal
                'currency' => $productData['currency']
            ]);

            Log::info('Raw product created in gateway', [
                'gateway_product_id' => $gatewayData['id'],
                'request_data' => $productData
            ]);

            return $gatewayData;

        } catch (GateException $e) {
            Log::error('Failed to create raw product in gateway', [
                'request_data' => $productData,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to create raw product in gateway', [
                'request_data' => $productData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get all products from gateway
     */
    public function getProducts(array $filters = []): array
    {
        try {
            return $this->sdk->getProducts($filters);
        } catch (GateException $e) {
            Log::error('Failed to retrieve products from gateway', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update product in gateway
     */
    public function updateProduct(Product $product): array
    {
        try {
            if (!$product->gateway_product_id) {
                throw new Exception('Product not synced with gateway');
            }

            $gatewayData = $this->sdk->updateProduct($product->gateway_product_id, [
                'brand' => config('services.gate.brand_id'),
                'title' => $product->name,
                'description' => $product->description,
                'price' => (float) $product->price,
                'currency' => $product->currency
            ]);

            $product->update(['gateway_data' => $gatewayData]);

            return $gatewayData;

        } catch (GateException $e) {
            Log::error('Failed to update product in gateway', [
                'product_id' => $product->id,
                'gateway_product_id' => $product->gateway_product_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete product from gateway
     */
    public function deleteProduct(Product $product): array
    {
        try {
            if (!$product->gateway_product_id) {
                throw new Exception('Product not synced with gateway');
            }

            $result = $this->sdk->deleteProduct($product->gateway_product_id);

            $product->update([
                'gateway_product_id' => null,
                'gateway_data' => null
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to delete product from gateway', [
                'product_id' => $product->id,
                'gateway_product_id' => $product->gateway_product_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get a specific product from gateway (for SDK showcase)
     */
    public function getProductRaw(string $productId): array
    {
        try {
            $gatewayData = $this->sdk->getProduct($productId);

            Log::info('Raw product retrieved from gateway', [
                'gateway_product_id' => $productId
            ]);

            return $gatewayData;

        } catch (GateException $e) {
            Log::error('Failed to get raw product from gateway', [
                'gateway_product_id' => $productId,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to get raw product from gateway', [
                'gateway_product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update a product in the gateway using raw data (for SDK showcase)
     */
    public function updateProductRaw(string $productId, array $productData): array
    {
        try {
            $gatewayData = $this->sdk->updateProduct($productId, [
                'brand' => config('services.gate.brand_id'),
                'title' => $productData['name'],
                'price' => (float) ($productData['amount'] / 100), // Convert from cents
                'currency' => $productData['currency']
            ]);

            Log::info('Raw product updated in gateway', [
                'gateway_product_id' => $productId,
                'request_data' => $productData
            ]);

            return $gatewayData;

        } catch (GateException $e) {
            Log::error('Failed to update raw product in gateway', [
                'gateway_product_id' => $productId,
                'request_data' => $productData,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to update raw product in gateway', [
                'gateway_product_id' => $productId,
                'request_data' => $productData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete a product from gateway using raw ID (for SDK showcase)
     */
    public function deleteProductRaw(string $productId): array
    {
        try {
            Log::info('About to call SDK deleteProduct', ['product_id' => $productId]);

            $result = $this->sdk->deleteProduct($productId);

            Log::info('SDK deleteProduct returned', [
                'gateway_product_id' => $productId,
                'result' => $result,
                'result_type' => gettype($result)
            ]);

            // Delete endpoint returns 204 No Content, so we create our own response
            $response = [
                'success' => true,
                'message' => 'Product deleted successfully',
                'product_id' => $productId,
                'deleted_at' => now()->toISOString()
            ];

            Log::info('Returning custom delete response', ['response' => $response]);

            return $response;

        } catch (GateException $e) {
            // Check if this is the expected "Invalid JSON response" error from 204 No Content
            if (strpos($e->getMessage(), 'Invalid JSON response') !== false) {
                Log::info('Delete successful - caught expected JSON parsing error from 204 response', [
                    'gateway_product_id' => $productId,
                    'error_message' => $e->getMessage()
                ]);

                // This is actually success - the delete worked but SDK couldn't parse empty response
                return [
                    'success' => true,
                    'message' => 'Product deleted successfully',
                    'product_id' => $productId,
                    'deleted_at' => now()->toISOString()
                ];
            }

            // If it's a different error, log and rethrow
            Log::error('Failed to delete raw product from gateway', [
                'gateway_product_id' => $productId,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;

        } catch (\Exception $e) {
            Log::error('Failed to delete raw product from gateway', [
                'gateway_product_id' => $productId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // ===== ORDERS MANAGEMENT (RAW METHODS) =====

    /**
     * Create an order in the gateway using raw data (for SDK showcase)
     */
    public function createOrderRaw(array $orderData): array
    {
        try {
            $gatewayData = $this->sdk->createOrder($orderData);

            Log::info('Raw order created in gateway', [
                'gateway_order_id' => $gatewayData['id'] ?? null,
                'request_data' => $orderData
            ]);

            return $gatewayData;

        } catch (GateException $e) {
            Log::error('Failed to create raw order in gateway', [
                'request_data' => $orderData,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to create raw order in gateway', [
                'request_data' => $orderData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get orders from gateway with filters (for SDK showcase)
     */
    public function getOrdersRaw(array $filters = []): array
    {
        try {
            $result = $this->sdk->getOrders($filters);

            Log::info('Raw orders retrieved from gateway', [
                'filters' => $filters,
                'count' => count($result['data'] ?? [])
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to get raw orders from gateway', [
                'filters' => $filters,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to get raw orders from gateway', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get a specific order from gateway (for SDK showcase)
     */
    public function getOrderRaw(string $orderId): array
    {
        try {
            $gatewayData = $this->sdk->getOrder($orderId);

            Log::info('Raw order retrieved from gateway', [
                'gateway_order_id' => $orderId
            ]);

            return $gatewayData;

        } catch (GateException $e) {
            Log::error('Failed to get raw order from gateway', [
                'gateway_order_id' => $orderId,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to get raw order from gateway', [
                'gateway_order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Capture payment for an order (for SDK showcase)
     */
    public function capturePaymentRaw(string $orderId, ?float $amount = null): array
    {
        try {
            $data = [];
            if ($amount !== null) {
                $data['amount'] = $amount; // Keep original amount for order capture
            }

            $result = $this->sdk->capturePayment($orderId, $data);

            Log::info('Raw payment captured in gateway', [
                'gateway_order_id' => $orderId,
                'amount' => $amount
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to capture raw payment in gateway', [
                'gateway_order_id' => $orderId,
                'amount' => $amount,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to capture raw payment in gateway', [
                'gateway_order_id' => $orderId,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Refund payment for an order (for SDK showcase)
     */
    public function refundPaymentRaw(string $orderId, float $amount, string $reason = ''): array
    {
        try {
            $data = [
                'amount' => $amount, // Keep original amount for order refund
                'reason' => $reason
            ];

            $result = $this->sdk->refundPayment($orderId, $data);

            Log::info('Raw payment refunded in gateway', [
                'gateway_order_id' => $orderId,
                'amount' => $amount,
                'reason' => $reason
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to refund raw payment in gateway', [
                'gateway_order_id' => $orderId,
                'amount' => $amount,
                'reason' => $reason,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to refund raw payment in gateway', [
                'gateway_order_id' => $orderId,
                'amount' => $amount,
                'reason' => $reason,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Cancel an order in the gateway (for SDK showcase)
     */
    public function cancelOrderRaw(string $orderId): array
    {
        try {
            $result = $this->sdk->cancelOrder($orderId);

            Log::info('Raw order cancelled in gateway', [
                'gateway_order_id' => $orderId
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to cancel raw order in gateway', [
                'gateway_order_id' => $orderId,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to cancel raw order in gateway', [
                'gateway_order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // ===== CLIENTS MANAGEMENT =====

    /**
     * Create client in gateway and sync with local database
     */
    public function createClient(Customer $customer): array
    {
        try {
            $customerData = $customer->toGatewayFormat();
            Log::info('Creating client in gateway', [
                'customer_id' => $customer->id,
                'data_sent' => $customerData
            ]);

            $gatewayData = $this->sdk->createClient($customerData);

            $customer->update([
                'gateway_client_id' => $gatewayData['id'],
                'gateway_data' => $gatewayData
            ]);

            Log::info('Client created in gateway', [
                'customer_id' => $customer->id,
                'gateway_client_id' => $gatewayData['id']
            ]);

            return $gatewayData;

        } catch (AuthenticationException $e) {
            $errorCode = method_exists($e, 'getCode') ? $e->getCode() : 'UNKNOWN';
            $httpStatus = method_exists($e, 'getHttpStatus') ? $e->getHttpStatus() : 'N/A';
            $additionalData = method_exists($e, 'getData') ? $e->getData() : [];
            $responseBody = method_exists($e, 'getResponse') ? $e->getResponse() : 'N/A';

            Log::error('Gate SDK Authentication Failed - Client Creation', [
                'customer_id' => $customer->id,
                'error_code' => $errorCode,
                'http_status' => $httpStatus,
                'error_message' => $e->getMessage(),
                'additional_data' => $additionalData,
                'response_body' => $responseBody
            ]);

            throw new \Exception("Gate SDK Authentication Error [Code: {$errorCode}] [HTTP: {$httpStatus}]: Unable to create client in payment gateway.\n\n" .
                "Configuration Steps:\n" .
                "1. Verify GATE_API_KEY is valid and active\n" .
                "2. Ensure GATE_SANDBOX_MODE is set correctly\n" .
                "3. Check GATE_BASE_URL is correct\n" .
                "4. Contact Applax support for credential verification\n\n" .
                "Error Details:\n" .
                "Message: " . $e->getMessage() . "\n" .
                "Code: " . $errorCode . "\n" .
                "HTTP Status: " . $httpStatus . "\n" .
                "Response: " . (is_string($responseBody) ? $responseBody : json_encode($responseBody)));

        } catch (ValidationException $e) {
            $errorCode = method_exists($e, 'getCode') ? $e->getCode() : 'VALIDATION_ERROR';
            $httpStatus = method_exists($e, 'getHttpStatus') ? $e->getHttpStatus() : 400;
            $validationErrors = method_exists($e, 'getErrors') ? $e->getErrors() : [];
            $responseBody = method_exists($e, 'getResponse') ? $e->getResponse() : 'N/A';

            Log::error('Validation failed for client creation', [
                'customer_id' => $customer->id,
                'data_sent' => $customerData,
                'error_code' => $errorCode,
                'http_status' => $httpStatus,
                'validation_errors' => $validationErrors,
                'response_body' => $responseBody
            ]);

            $validationDetails = empty($validationErrors) ? 'No specific validation errors provided' : json_encode($validationErrors, JSON_PRETTY_PRINT);

            throw new \Exception("Gate SDK Validation Error [Code: {$errorCode}]: Invalid data sent to payment gateway.\n\n" .
                "Data Sent:\n" . json_encode($customerData, JSON_PRETTY_PRINT) . "\n\n" .
                "Validation Errors:\n" . $validationDetails . "\n\n" .
                "Response: " . (is_string($responseBody) ? $responseBody : json_encode($responseBody, JSON_PRETTY_PRINT)));

        } catch (GateException $e) {
            $errorCode = method_exists($e, 'getCode') ? $e->getCode() : 'UNKNOWN';
            $httpStatus = method_exists($e, 'getHttpStatus') ? $e->getHttpStatus() : 'N/A';
            $additionalData = method_exists($e, 'getData') ? $e->getData() : [];
            $responseBody = method_exists($e, 'getResponse') ? $e->getResponse() : 'N/A';

            Log::error('Failed to create client in gateway', [
                'customer_id' => $customer->id,
                'data_sent' => $customerData,
                'error_code' => $errorCode,
                'http_status' => $httpStatus,
                'error_message' => $e->getMessage(),
                'additional_data' => $additionalData,
                'response_body' => $responseBody
            ]);

            throw new \Exception("Gate SDK Error [Code: {$errorCode}] [HTTP: {$httpStatus}]: Unable to create client in payment gateway.\n\n" .
                "Data Sent:\n" . json_encode($customerData, JSON_PRETTY_PRINT) . "\n\n" .
                "Troubleshooting Steps:\n" .
                "1. Check your network connection\n" .
                "2. Verify API configuration in .env file\n" .
                "3. Ensure the API endpoint is accessible\n" .
                "4. Contact Applax support if issue persists\n\n" .
                "Error Details:\n" .
                "Message: " . $e->getMessage() . "\n" .
                "Code: " . $errorCode . "\n" .
                "HTTP Status: " . $httpStatus . "\n" .
                "Response: " . (is_string($responseBody) ? $responseBody : json_encode($responseBody)));
        }
    }

    /**
     * Update client in gateway
     */
    public function updateClient(Customer $customer): array
    {
        try {
            if (!$customer->gateway_client_id) {
                throw new Exception('Customer not synced with gateway');
            }

            $gatewayData = $this->sdk->updateClient(
                $customer->gateway_client_id,
                $customer->toGatewayFormat()
            );

            $customer->update(['gateway_data' => $gatewayData]);

            return $gatewayData;

        } catch (GateException $e) {
            Log::error('Failed to update client in gateway', [
                'customer_id' => $customer->id,
                'gateway_client_id' => $customer->gateway_client_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // ===== ORDERS MANAGEMENT =====

    /**
     * Create order in gateway and sync with local database
     */
    public function createOrder(Order $order): array
    {
        try {
            // Validate order before sending to gateway
            $order->validateForGateway();

            // Ensure customer is synced with gateway
            if (!$order->customer->gateway_client_id) {
                $this->createClient($order->customer);
            }

            $orderData = $order->toGatewayFormat();
            Log::info('Creating order in gateway', [
                'order_id' => $order->id,
                'order_total' => $order->total,
                'order_total_type' => gettype($order->total),
                'items_count' => $order->items->count(),
                'items_sum' => $order->items->sum('total_price'),
                'data_sent' => $orderData,
                'amount_in_data' => $orderData['amount'],
                'amount_type' => gettype($orderData['amount']),
                'endpoint' => 'SDK->createOrderModel()',
                'base_url' => config('services.gate.base_url', 'Not configured'),
                'api_key_present' => !empty(config('services.gate.api_key')),
                'sandbox_mode' => config('services.gate.sandbox', false)
            ]);

            // Create order using SDK model (now that SDK is fixed)
            $orderModel = $this->sdk->createOrderModel($orderData);
            $gatewayData = $orderModel->toArray();

            Log::info('Order successfully created in gateway', [
                'order_id' => $order->id,
                'gateway_order_id' => $gatewayData['id'] ?? 'NOT_SET',
                'status' => $gatewayData['status'] ?? 'NOT_SET',
                'gateway_response' => $gatewayData
            ]);

            $order->update([
                'gateway_order_id' => $gatewayData['id'],
                'status' => $gatewayData['status'],
                'gateway_data' => $gatewayData,
                'payment_urls' => [
                    'full_page_checkout' => $gatewayData['full_page_checkout'] ?? null,
                    'iframe_checkout' => $gatewayData['iframe_checkout'] ?? null,
                    'direct_post' => $gatewayData['direct_post'] ?? null,
                    'api_do_url' => $gatewayData['api_do_url'] ?? null,
                    'api_do_applepay' => $gatewayData['api_do_applepay'] ?? null,
                    'api_do_googlepay' => $gatewayData['api_do_googlepay'] ?? null,
                    'api_init_paypal' => $gatewayData['api_init_paypal'] ?? null,
                    'api_init_klarna' => $gatewayData['api_init_klarna'] ?? null,
                    'link' => $gatewayData['link'] ?? null,
                ]
            ]);

            return $gatewayData;

        } catch (AuthenticationException $e) {
            $errorCode = method_exists($e, 'getCode') ? $e->getCode() : 'UNKNOWN';
            $httpStatus = method_exists($e, 'getHttpStatus') ? $e->getHttpStatus() : 'N/A';
            $additionalData = method_exists($e, 'getData') ? $e->getData() : [];
            $responseBody = method_exists($e, 'getResponse') ? $e->getResponse() : 'N/A';

            Log::error('Gate SDK Authentication Failed - Order Creation', [
                'order_id' => $order->id,
                'data_sent' => $orderData,
                'error_code' => $errorCode,
                'http_status' => $httpStatus,
                'error_message' => $e->getMessage(),
                'additional_data' => $additionalData,
                'response_body' => $responseBody
            ]);

            throw new \Exception("Gate SDK Authentication Error [Code: {$errorCode}] [HTTP: {$httpStatus}]: Unable to create order in payment gateway.\n\n" .
                "Configuration Steps:\n" .
                "1. Verify GATE_API_KEY is valid and active\n" .
                "2. Ensure GATE_SANDBOX_MODE is set correctly\n" .
                "3. Check GATE_BASE_URL is correct\n" .
                "4. Contact Applax support for credential verification\n\n" .
                "Error Details:\n" .
                "Message: " . $e->getMessage() . "\n" .
                "Code: " . $errorCode . "\n" .
                "HTTP Status: " . $httpStatus . "\n" .
                "Response: " . (is_string($responseBody) ? $responseBody : json_encode($responseBody)));

        } catch (ValidationException $e) {
            $errorCode = method_exists($e, 'getCode') ? $e->getCode() : 'VALIDATION_ERROR';
            $httpStatus = method_exists($e, 'getHttpStatus') ? $e->getHttpStatus() : 400;
            $validationErrors = method_exists($e, 'getErrors') ? $e->getErrors() : [];
            $responseBody = method_exists($e, 'getResponse') ? $e->getResponse() : 'N/A';

            // Try to get more detailed error info
            $exceptionMessage = $e->getMessage();
            $exceptionTrace = $e->getTraceAsString();

            Log::error('Validation failed for order creation', [
                'order_id' => $order->id,
                'data_sent' => $orderData,
                'error_code' => $errorCode,
                'http_status' => $httpStatus,
                'validation_errors' => $validationErrors,
                'response_body' => $responseBody,
                'exception_message' => $exceptionMessage,
                'exception_trace' => $exceptionTrace
            ]);

            $validationDetails = empty($validationErrors) ? 'No specific validation errors provided' : json_encode($validationErrors, JSON_PRETTY_PRINT);

            throw new \Exception("Gate SDK Order Validation Error [Code: {$errorCode}]: " . $exceptionMessage . "\n\n" .
                "Data Sent:\n" . json_encode($orderData, JSON_PRETTY_PRINT) . "\n\n" .
                "Validation Errors:\n" . $validationDetails . "\n\n" .
                "Response: " . (is_string($responseBody) ? $responseBody : json_encode($responseBody, JSON_PRETTY_PRINT)));

        } catch (GateException $e) {
            $errorCode = method_exists($e, 'getCode') ? $e->getCode() : 'UNKNOWN';
            $httpStatus = method_exists($e, 'getHttpStatus') ? $e->getHttpStatus() : 'N/A';
            $additionalData = method_exists($e, 'getData') ? $e->getData() : [];
            $responseBody = method_exists($e, 'getResponse') ? $e->getResponse() : 'N/A';

            Log::error('Failed to create order in gateway - GateException', [
                'order_id' => $order->id,
                'error_code' => $errorCode,
                'http_status' => $httpStatus,
                'error_message' => $e->getMessage(),
                'additional_data' => $additionalData,
                'response_body' => $responseBody,
                'exception_class' => get_class($e)
            ]);

            throw new \Exception("Gate SDK Error [Code: {$errorCode}] [HTTP: {$httpStatus}]: " . $e->getMessage());

        } catch (\Exception $e) {
            // Catch any other exception type
            Log::error('Failed to create order in gateway - General Exception', [
                'order_id' => $order->id,
                'error_message' => $e->getMessage(),
                'exception_class' => get_class($e),
                'exception_trace' => $e->getTraceAsString(),
                'data_sent' => $orderData
            ]);

            throw new \Exception("Order creation failed: " . $e->getMessage());
        }
    }

    /**
     * Get order from gateway
     */
    public function getOrder(Order $order): array
    {
        try {
            if (!$order->gateway_order_id) {
                throw new Exception('Order not synced with gateway');
            }

            return $this->sdk->getOrder($order->gateway_order_id);

        } catch (GateException $e) {
            Log::error('Failed to retrieve order from gateway', [
                'order_id' => $order->id,
                'gateway_order_id' => $order->gateway_order_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Capture payment for order
     */
    public function capturePayment(Order $order, ?float $amount = null): array
    {
        try {
            if (!$order->gateway_order_id) {
                throw new Exception('Order not synced with gateway');
            }

            $data = $amount ? ['amount' => $amount] : [];
            $result = $this->sdk->capturePayment($order->gateway_order_id, $data);

            Log::info('Payment captured for order', [
                'order_id' => $order->id,
                'gateway_order_id' => $order->gateway_order_id,
                'amount' => $amount
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to capture payment for order', [
                'order_id' => $order->id,
                'gateway_order_id' => $order->gateway_order_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Refund payment for order
     */
    public function refundPayment(Order $order, ?float $amount = null, ?string $reason = null): array
    {
        try {
            if (!$order->gateway_order_id) {
                throw new Exception('Order not synced with gateway');
            }

            $data = [];
            if ($amount) $data['amount'] = $amount;
            if ($reason) $data['reason'] = $reason;

            $result = $this->sdk->refundPayment($order->gateway_order_id, $data);

            Log::info('Payment refunded for order', [
                'order_id' => $order->id,
                'gateway_order_id' => $order->gateway_order_id,
                'amount' => $amount,
                'reason' => $reason
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to refund payment for order', [
                'order_id' => $order->id,
                'gateway_order_id' => $order->gateway_order_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Cancel order
     */
    public function cancelOrder(Order $order): array
    {
        try {
            if (!$order->gateway_order_id) {
                throw new Exception('Order not synced with gateway');
            }

            $result = $this->sdk->cancelOrder($order->gateway_order_id);

            $order->update(['status' => 'cancelled']);

            Log::info('Order cancelled', [
                'order_id' => $order->id,
                'gateway_order_id' => $order->gateway_order_id
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to cancel order', [
                'order_id' => $order->id,
                'gateway_order_id' => $order->gateway_order_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // ===== PAYMENT PROCESSING =====

    /**
     * Execute card payment
     */
    public function executeCardPayment(Order $order, array $cardData): array
    {
        try {
            $apiDoUrl = $order->payment_urls['api_do_url'] ?? null;
            if (!$apiDoUrl) {
                throw new Exception('Payment URL not available for order');
            }

            $result = $this->sdk->executeCardPayment($apiDoUrl, $cardData);

            $this->logPaymentAttempt($order, 'card', $cardData, $result);

            return $result;

        } catch (GateException $e) {
            $this->logPaymentFailure($order, 'card', $cardData, $e);
            throw $e;
        }
    }

    /**
     * Execute Apple Pay payment
     */
    public function executeApplePayPayment(Order $order, array $applePayData): array
    {
        try {
            $apiDoUrl = $order->payment_urls['api_do_applepay'] ?? null;
            if (!$apiDoUrl) {
                throw new Exception('Apple Pay URL not available for order');
            }

            $result = $this->sdk->executeApplePayPayment($apiDoUrl, $applePayData);

            $this->logPaymentAttempt($order, 'apple_pay', $applePayData, $result);

            return $result;

        } catch (GateException $e) {
            $this->logPaymentFailure($order, 'apple_pay', $applePayData, $e);
            throw $e;
        }
    }

    /**
     * Execute Google Pay payment
     */
    public function executeGooglePayPayment(Order $order, array $googlePayData): array
    {
        try {
            $apiDoUrl = $order->payment_urls['api_do_googlepay'] ?? null;
            if (!$apiDoUrl) {
                throw new Exception('Google Pay URL not available for order');
            }

            $result = $this->sdk->executeGooglePayPayment($apiDoUrl, $googlePayData);

            $this->logPaymentAttempt($order, 'google_pay', $googlePayData, $result);

            return $result;

        } catch (GateException $e) {
            $this->logPaymentFailure($order, 'google_pay', $googlePayData, $e);
            throw $e;
        }
    }

    /**
     * Initialize PayPal payment
     */
    public function initPayPalPayment(Order $order): array
    {
        try {
            $apiInitUrl = $order->payment_urls['api_init_paypal'] ?? null;
            if (!$apiInitUrl) {
                throw new Exception('PayPal init URL not available for order');
            }

            $result = $this->sdk->initPayPalPayment($apiInitUrl);

            $this->logPaymentAttempt($order, 'paypal', [], $result);

            return $result;

        } catch (GateException $e) {
            $this->logPaymentFailure($order, 'paypal', [], $e);
            throw $e;
        }
    }

    /**
     * Initialize Klarna payment
     */
    public function initKlarnaPayment(Order $order, array $klarnaData = []): array
    {
        try {
            $apiInitUrl = $order->payment_urls['api_init_klarna'] ?? null;
            if (!$apiInitUrl) {
                throw new Exception('Klarna init URL not available for order');
            }

            $result = $this->sdk->initKlarnaPayment($apiInitUrl, $klarnaData);

            $this->logPaymentAttempt($order, 'klarna', $klarnaData, $result);

            return $result;

        } catch (GateException $e) {
            $this->logPaymentFailure($order, 'klarna', $klarnaData, $e);
            throw $e;
        }
    }

    // ===== WEBHOOK MANAGEMENT =====

    /**
     * Validate webhook signature
     */
    public function validateWebhookSignature(string $payload, string $signature, string $secret): bool
    {
        return $this->sdk->validateWebhookSignature($payload, $signature, $secret);
    }

    /**
     * Process webhook payload
     */
    public function processWebhook(WebhookLog $webhook): void
    {
        try {
            $payload = $webhook->payload;

            // Handle different webhook event types
            switch ($webhook->event_type) {
                case 'order.paid':
                    $this->handleOrderPaidWebhook($payload);
                    break;

                case 'order.failed':
                    $this->handleOrderFailedWebhook($payload);
                    break;

                case 'order.cancelled':
                    $this->handleOrderCancelledWebhook($payload);
                    break;

                case 'payment.completed':
                    $this->handlePaymentCompletedWebhook($payload);
                    break;

                case 'payment.failed':
                    $this->handlePaymentFailedWebhook($payload);
                    break;

                default:
                    Log::warning('Unknown webhook event type', [
                        'event_type' => $webhook->event_type,
                        'payload' => $payload
                    ]);
            }

            $webhook->markAsProcessed();

        } catch (Exception $e) {
            $webhook->markAsFailed($e->getMessage());
            Log::error('Failed to process webhook', [
                'webhook_id' => $webhook->id,
                'event_type' => $webhook->event_type,
                'error' => $e->getMessage()
            ]);
        }
    }

    // ===== UTILITY METHODS =====

    /**
     * Format currency amount
     */
    public function formatCurrency(float $amount, string $currency): string
    {
        return $this->sdk->formatCurrency($amount, $currency);
    }

    /**
     * Validate currency code
     */
    public function validateCurrency(string $currency): bool
    {
        return $this->sdk->validateCurrency($currency);
    }

    // ===== PRIVATE HELPER METHODS =====

    private function logPaymentAttempt(Order $order, string $method, array $data, array $result): void
    {
        Log::info('Payment attempt logged', [
            'order_id' => $order->id,
            'gateway_order_id' => $order->gateway_order_id,
            'method' => $method,
            'result_status' => $result['status'] ?? 'unknown'
        ]);

        // Create payment record
        Payment::create([
            'order_id' => $order->id,
            'method' => $method,
            'amount' => $order->total,
            'currency' => $order->currency,
            'status' => $this->mapGatewayStatusToPaymentStatus($result['status'] ?? 'pending'),
            'gateway_response' => $result,
            'payment_details' => array_merge($data, ['api_response' => $result])
        ]);
    }

    private function logPaymentFailure(Order $order, string $method, array $data, GateException $exception): void
    {
        Log::error('Payment failure logged', [
            'order_id' => $order->id,
            'gateway_order_id' => $order->gateway_order_id,
            'method' => $method,
            'error' => $exception->getMessage()
        ]);

        Payment::create([
            'order_id' => $order->id,
            'method' => $method,
            'amount' => $order->total,
            'currency' => $order->currency,
            'status' => 'failed',
            'failure_reason' => $exception->getMessage(),
            'gateway_response' => method_exists($exception, 'getData') ? $exception->getData() : [],
            'payment_details' => $data
        ]);
    }

    private function mapGatewayStatusToPaymentStatus(string $gatewayStatus): string
    {
        return match($gatewayStatus) {
            'paid' => 'completed',
            'failed', 'rejected' => 'failed',
            'cancelled' => 'cancelled',
            'in_progress' => 'processing',
            default => 'pending'
        };
    }

    private function handleOrderPaidWebhook(array $payload): void
    {
        $gatewayOrderId = $payload['id'] ?? null;
        if (!$gatewayOrderId) return;

        $order = Order::where('gateway_order_id', $gatewayOrderId)->first();
        if ($order) {
            $order->markAsPaid();
        }
    }

    private function handleOrderFailedWebhook(array $payload): void
    {
        $gatewayOrderId = $payload['id'] ?? null;
        if (!$gatewayOrderId) return;

        $order = Order::where('gateway_order_id', $gatewayOrderId)->first();
        if ($order) {
            $order->update(['status' => 'failed']);
        }
    }

    private function handleOrderCancelledWebhook(array $payload): void
    {
        $gatewayOrderId = $payload['id'] ?? null;
        if (!$gatewayOrderId) return;

        $order = Order::where('gateway_order_id', $gatewayOrderId)->first();
        if ($order) {
            $order->update(['status' => 'cancelled']);
        }
    }

    private function handlePaymentCompletedWebhook(array $payload): void
    {
        $paymentId = $payload['payment_id'] ?? null;
        if (!$paymentId) return;

        $payment = Payment::where('gateway_payment_id', $paymentId)->first();
        if ($payment) {
            $payment->markAsCompleted($payload);
        }
    }

    private function handlePaymentFailedWebhook(array $payload): void
    {
        $paymentId = $payload['payment_id'] ?? null;
        if (!$paymentId) return;

        $payment = Payment::where('gateway_payment_id', $paymentId)->first();
        if ($payment) {
            $payment->markAsFailed(
                $payload['failure_reason'] ?? 'Payment failed',
                $payload
            );
        }
    }

    /**
     * Get order information from Gateway
     */
    public function getOrderInfo(string $gatewayOrderId): ?array
    {
        try {
            Log::info("Fetching order info from Gateway", ['gateway_order_id' => $gatewayOrderId]);

            $orderInfo = $this->sdk->getOrder($gatewayOrderId);

            Log::info("Successfully fetched order info", [
                'gateway_order_id' => $gatewayOrderId,
                'status' => $orderInfo['status'] ?? 'unknown'
            ]);

            return $orderInfo;

        } catch (GateException $e) {
            Log::warning("Failed to fetch order info from Gateway", [
                'gateway_order_id' => $gatewayOrderId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }


    // Note: Payments API methods removed - use Orders API instead
    // All payment operations (capture, refund, void) are handled through orders
    public function getPaymentsRaw(array $filters = []): array
    {
        try {
            // Convert limit to page_size for proper SDK pagination
            $originalFilters = $filters;
            if (isset($filters['limit'])) {
                $filters['page_size'] = $filters['limit'];
                unset($filters['limit']);
            }

            // Since GateSDK doesn't have getPayments, we'll get ALL orders first
            // Don't filter by status in the orders call - we need to find orders with transaction_details
            $orderFilters = $filters;
            unset($orderFilters['status']); // Remove status filter to get all orders

            // First try to get ALL orders with no filters to see if there are ANY orders
            try {
                $allOrdersResult = $this->sdk->getOrders([]);
                Log::info('SDK Connection Test - Basic getOrders call', [
                    'raw_response' => $allOrdersResult,
                    'is_array' => is_array($allOrdersResult),
                    'has_data_key' => isset($allOrdersResult['data']),
                    'data_type' => isset($allOrdersResult['data']) ? gettype($allOrdersResult['data']) : 'not_set'
                ]);
            } catch (\Exception $e) {
                Log::error('SDK Connection Test - getOrders failed', [
                    'error' => $e->getMessage(),
                    'exception_class' => get_class($e),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

            // Then get filtered orders if needed
            $ordersResult = empty($orderFilters) ? $allOrdersResult : $this->sdk->getOrders($orderFilters);

            // Debug logging to see what we're getting
            Log::info('Debug - Orders result structure', [
                'original_filters' => $originalFilters,
                'order_filters_used' => $orderFilters,
                'all_orders_count' => isset($allOrdersResult['data']) ? count($allOrdersResult['data']) : 0,
                'filtered_orders_count' => isset($ordersResult['data']) ? count($ordersResult['data']) : 0,
                'all_orders_sample' => isset($allOrdersResult['data'][0]) ? $allOrdersResult['data'][0] : null,
                'filtered_orders_sample' => isset($ordersResult['data'][0]) ? $ordersResult['data'][0] : null,
            ]);

            $payments = [];

            if (isset($ordersResult['data']) && is_array($ordersResult['data'])) {
                foreach ($ordersResult['data'] as $order) {
                    // Only process orders that have been paid (have transaction details)
                    if (isset($order['transaction_details']) && !empty($order['transaction_details'])) {
                        $transactionDetails = $order['transaction_details'];

                        // Handle both single transaction and array of transactions
                        $transactionsArray = is_array($transactionDetails) && isset($transactionDetails[0]) ? $transactionDetails : [$transactionDetails];

                        foreach ($transactionsArray as $transaction) {
                            // Only include successful payments/refunds
                            if (isset($transaction['status']) && in_array($transaction['status'], ['approved', 'refunded', 'partially_refunded'])) {
                                $payment = [
                                    'id' => $transaction['id'] ?? null,
                                    'status' => $transaction['status'] ?? null,
                                    'amount' => $transaction['amount'] ?? null,
                                    'currency' => $transaction['currency'] ?? null,
                                    'card_number' => $transaction['card_number'] ?? null,
                                    'card_type' => $transaction['card_type'] ?? null,
                                    'gateway_response' => $transaction['gateway_response'] ?? null,
                                    'created_at' => $transaction['created_at'] ?? null,
                                    'updated_at' => $transaction['updated_at'] ?? null,
                                    // Include order context
                                    'order_id' => $order['id'] ?? null,
                                    'order_number' => $order['number'] ?? null,
                                    'order_status' => $order['status'] ?? null,
                                ];
                                $payments[] = $payment;
                            }
                        }
                    }
                }
            }

            $result = [
                'success' => true,
                'data' => $payments,
                'count' => count($payments),
                'message' => 'Payments retrieved from orders successfully',
                'debug_info' => [
                    'total_orders_found' => isset($ordersResult['data']) ? count($ordersResult['data']) : 0,
                    'orders_with_payment_data' => count($payments) > 0 ? 'yes' : 'no'
                ]
            ];

            Log::info('Raw payments retrieved from gateway via orders', [
                'filters' => $filters,
                'count' => count($payments)
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to retrieve raw payments from gateway', [
                'filters' => $filters,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to retrieve raw payments from gateway', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get single payment from the gateway (for SDK showcase)
     * Note: GateSDK doesn't have direct getPayment method, so we'll try to find it via orders
     */
    public function getPaymentRaw(string $paymentId): array
    {
        try {
            // Since GateSDK doesn't have getPayment, we'll search through orders to find the payment
            $ordersResult = $this->sdk->getOrders([]);

            $foundPayment = null;
            if (isset($ordersResult['data']) && is_array($ordersResult['data'])) {
                foreach ($ordersResult['data'] as $order) {
                    if (isset($order['payment']) &&
                        (($order['payment']['id'] ?? '') === $paymentId ||
                         ($order['payment']['transaction_id'] ?? '') === $paymentId ||
                         ($order['id'] ?? '') === $paymentId)) {
                        $foundPayment = $order['payment'];
                        $foundPayment['order_id'] = $order['id'] ?? null;
                        $foundPayment['order_number'] = $order['number'] ?? null;
                        break;
                    }
                }
            }

            if ($foundPayment) {
                $result = [
                    'success' => true,
                    'data' => $foundPayment,
                    'message' => 'Payment retrieved successfully'
                ];

                Log::info('Raw payment retrieved from gateway via orders', [
                    'gateway_payment_id' => $paymentId
                ]);

                return $result;
            } else {
                throw new NotFoundException("Payment with ID '{$paymentId}' not found");
            }

        } catch (GateException $e) {
            Log::error('Failed to retrieve raw payment from gateway', [
                'gateway_payment_id' => $paymentId,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to retrieve raw payment from gateway', [
                'gateway_payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Process refund for a payment (for SDK showcase)
     */
    public function processRefundRaw(string $paymentId, float $amount, string $reason = ''): array
    {
        try {
            // First find the order that contains this payment
            $ordersResult = $this->sdk->getOrders([]);
            $orderId = null;

            if (isset($ordersResult['data']) && is_array($ordersResult['data'])) {
                foreach ($ordersResult['data'] as $order) {
                    if (isset($order['payment']) &&
                        (($order['payment']['id'] ?? '') === $paymentId ||
                         ($order['payment']['transaction_id'] ?? '') === $paymentId ||
                         ($order['id'] ?? '') === $paymentId)) {
                        $orderId = $order['id'];
                        break;
                    }
                }
            }

            if (!$orderId) {
                throw new NotFoundException("Order for payment ID '{$paymentId}' not found");
            }

            $data = [
                'amount' => $amount * 100, // Convert to cents for Gateway API
                'reason' => $reason
            ];

            $result = $this->sdk->refundPayment($orderId, $data);

            Log::info('Raw payment refund processed in gateway', [
                'gateway_payment_id' => $paymentId,
                'amount' => $amount,
                'reason' => $reason
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to process raw payment refund in gateway', [
                'gateway_payment_id' => $paymentId,
                'amount' => $amount,
                'reason' => $reason,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to process raw payment refund in gateway', [
                'gateway_payment_id' => $paymentId,
                'amount' => $amount,
                'reason' => $reason,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Capture payment directly (for SDK showcase)
     */
    public function capturePaymentDirectRaw(string $paymentId, ?float $amount = null): array
    {
        try {
            // First find the order that contains this payment
            $ordersResult = $this->sdk->getOrders([]);
            $orderId = null;

            if (isset($ordersResult['data']) && is_array($ordersResult['data'])) {
                foreach ($ordersResult['data'] as $order) {
                    if (isset($order['payment']) &&
                        (($order['payment']['id'] ?? '') === $paymentId ||
                         ($order['payment']['transaction_id'] ?? '') === $paymentId ||
                         ($order['id'] ?? '') === $paymentId)) {
                        $orderId = $order['id'];
                        break;
                    }
                }
            }

            if (!$orderId) {
                throw new NotFoundException("Order for payment ID '{$paymentId}' not found");
            }

            $data = $amount ? ['amount' => $amount * 100] : []; // Convert to cents for Gateway API
            $result = $this->sdk->capturePayment($orderId, $data);

            Log::info('Raw payment captured directly in gateway', [
                'gateway_payment_id' => $paymentId,
                'amount' => $amount
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to capture raw payment directly in gateway', [
                'gateway_payment_id' => $paymentId,
                'amount' => $amount,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to capture raw payment directly in gateway', [
                'gateway_payment_id' => $paymentId,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Void payment in the gateway (for SDK showcase)
     */
    public function voidPaymentRaw(string $paymentId): array
    {
        try {
            // First find the order that contains this payment
            $ordersResult = $this->sdk->getOrders([]);
            $orderId = null;

            if (isset($ordersResult['data']) && is_array($ordersResult['data'])) {
                foreach ($ordersResult['data'] as $order) {
                    if (isset($order['payment']) &&
                        (($order['payment']['id'] ?? '') === $paymentId ||
                         ($order['payment']['transaction_id'] ?? '') === $paymentId ||
                         ($order['id'] ?? '') === $paymentId)) {
                        $orderId = $order['id'];
                        break;
                    }
                }
            }

            if (!$orderId) {
                throw new NotFoundException("Order for payment ID '{$paymentId}' not found");
            }

            $result = $this->sdk->reversePayment($orderId);

            Log::info('Raw payment voided in gateway', [
                'gateway_payment_id' => $paymentId
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to void raw payment in gateway', [
                'gateway_payment_id' => $paymentId,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to void raw payment in gateway', [
                'gateway_payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // ===== CLIENTS MANAGEMENT (RAW METHODS) =====

    /**
     * Get clients from the gateway with filters (for SDK showcase)
     */
    public function getClientsRaw(array $filters = []): array
    {
        try {
            $result = $this->sdk->getClients($filters);

            Log::info('Raw clients retrieved from gateway', [
                'filters' => $filters,
                'count' => count($result['data'] ?? [])
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to retrieve raw clients from gateway', [
                'filters' => $filters,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to retrieve raw clients from gateway', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get single client from the gateway (for SDK showcase)
     */
    public function getClientRaw(string $clientId): array
    {
        try {
            $result = $this->sdk->getClient($clientId);

            Log::info('Raw client retrieved from gateway', [
                'gateway_client_id' => $clientId
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to retrieve raw client from gateway', [
                'gateway_client_id' => $clientId,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to retrieve raw client from gateway', [
                'gateway_client_id' => $clientId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create client in the gateway (for SDK showcase)
     */
    public function createClientRaw(array $clientData): array
    {
        try {
            $result = $this->sdk->createClient($clientData);

            Log::info('Raw client created in gateway', [
                'gateway_client_id' => $result['id'] ?? null,
                'request_data' => $clientData
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to create raw client in gateway', [
                'request_data' => $clientData,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to create raw client in gateway', [
                'request_data' => $clientData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update client in the gateway (for SDK showcase)
     */
    public function updateClientRaw(string $clientId, array $clientData): array
    {
        try {
            $result = $this->sdk->updateClient($clientId, $clientData);

            Log::info('Raw client updated in gateway', [
                'gateway_client_id' => $clientId,
                'request_data' => $clientData
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to update raw client in gateway', [
                'gateway_client_id' => $clientId,
                'request_data' => $clientData,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to update raw client in gateway', [
                'gateway_client_id' => $clientId,
                'request_data' => $clientData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Partially update client in the gateway (for SDK showcase)
     */
    public function partialUpdateClientRaw(string $clientId, array $clientData): array
    {
        try {
            $result = $this->sdk->partialUpdateClient($clientId, $clientData);

            Log::info('Raw client partially updated in gateway', [
                'gateway_client_id' => $clientId,
                'request_data' => $clientData
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to partially update raw client in gateway', [
                'gateway_client_id' => $clientId,
                'request_data' => $clientData,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to partially update raw client in gateway', [
                'gateway_client_id' => $clientId,
                'request_data' => $clientData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete client from the gateway (for SDK showcase)
     */
    public function deleteClientRaw(string $clientId): array
    {
        try {
            $result = $this->sdk->deleteClient($clientId);

            Log::info('Raw client deleted from gateway', [
                'gateway_client_id' => $clientId
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to delete raw client from gateway', [
                'gateway_client_id' => $clientId,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to delete raw client from gateway', [
                'gateway_client_id' => $clientId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    // ===== WEBHOOKS MANAGEMENT (RAW METHODS) =====

    /**
     * Get webhooks from the gateway with filters (for SDK showcase)
     */
    public function getWebhooksRaw(array $filters = []): array
    {
        try {
            $result = $this->sdk->getWebhooks($filters);

            Log::info('Raw webhooks retrieved from gateway', [
                'filters' => $filters,
                'count' => count($result['data'] ?? [])
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to retrieve raw webhooks from gateway', [
                'filters' => $filters,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to retrieve raw webhooks from gateway', [
                'filters' => $filters,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get single webhook from the gateway (for SDK showcase)
     */
    public function getWebhookRaw(string $webhookId): array
    {
        try {
            $result = $this->sdk->getWebhook($webhookId);

            Log::info('Raw webhook retrieved from gateway', [
                'gateway_webhook_id' => $webhookId
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to retrieve raw webhook from gateway', [
                'gateway_webhook_id' => $webhookId,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to retrieve raw webhook from gateway', [
                'gateway_webhook_id' => $webhookId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Create webhook in the gateway (for SDK showcase)
     */
    public function createWebhookRaw(array $webhookData): array
    {
        try {
            $result = $this->sdk->createWebhook($webhookData);

            Log::info('Raw webhook created in gateway', [
                'gateway_webhook_id' => $result['id'] ?? null,
                'request_data' => $webhookData
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to create raw webhook in gateway', [
                'request_data' => $webhookData,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to create raw webhook in gateway', [
                'request_data' => $webhookData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update webhook in the gateway (for SDK showcase)
     */
    public function updateWebhookRaw(string $webhookId, array $webhookData): array
    {
        try {
            $result = $this->sdk->updateWebhook($webhookId, $webhookData);

            Log::info('Raw webhook updated in gateway', [
                'gateway_webhook_id' => $webhookId,
                'request_data' => $webhookData
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to update raw webhook in gateway', [
                'gateway_webhook_id' => $webhookId,
                'request_data' => $webhookData,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to update raw webhook in gateway', [
                'gateway_webhook_id' => $webhookId,
                'request_data' => $webhookData,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete webhook from the gateway (for SDK showcase)
     */
    public function deleteWebhookRaw(string $webhookId): array
    {
        try {
            $result = $this->sdk->deleteWebhook($webhookId);

            // Handle case where delete returns empty response (204 No Content)
            if (empty($result) || is_null($result)) {
                return [
                    'success' => true,
                    'message' => 'Webhook deleted successfully',
                    'webhook_id' => $webhookId,
                    'deleted_at' => now()->toISOString()
                ];
            }

            Log::info('Raw webhook deleted from gateway', [
                'gateway_webhook_id' => $webhookId
            ]);

            return $result;

        } catch (GateException $e) {
            // Handle JSON parsing error for empty 204 response
            if (strpos($e->getMessage(), 'Invalid JSON response') !== false) {
                Log::info('Raw webhook deleted successfully (empty response)', [
                    'gateway_webhook_id' => $webhookId
                ]);

                return [
                    'success' => true,
                    'message' => 'Webhook deleted successfully',
                    'webhook_id' => $webhookId,
                    'deleted_at' => now()->toISOString()
                ];
            }

            Log::error('Failed to delete raw webhook from gateway', [
                'gateway_webhook_id' => $webhookId,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to delete raw webhook from gateway', [
                'gateway_webhook_id' => $webhookId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Test webhook by sending a test event (for SDK showcase)
     */
    public function testWebhookRaw(string $webhookId, string $eventType): array
    {
        try {
            $result = $this->sdk->testWebhook($webhookId, ['event_type' => $eventType]);

            Log::info('Raw webhook test sent from gateway', [
                'gateway_webhook_id' => $webhookId,
                'event_type' => $eventType
            ]);

            return $result;

        } catch (GateException $e) {
            Log::error('Failed to send raw webhook test from gateway', [
                'gateway_webhook_id' => $webhookId,
                'event_type' => $eventType,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to send raw webhook test from gateway', [
                'gateway_webhook_id' => $webhookId,
                'event_type' => $eventType,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}