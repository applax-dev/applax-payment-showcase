<?php

namespace App\Http\Controllers\SDK;

use App\Http\Controllers\Controller;
use App\Services\PaymentGateway\GateSDKService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhooksController extends Controller
{
    protected $gateSDKService;

    public function __construct(GateSDKService $gateSDKService)
    {
        $this->gateSDKService = $gateSDKService;
    }

    public function index()
    {
        return view('sdk.webhooks.index');
    }

    public function getWebhooks(Request $request)
    {
        try {
            $filters = [];

            if ($request->has('limit')) {
                $filters['limit'] = min($request->limit, 50);
            }

            if ($request->has('offset')) {
                $filters['offset'] = $request->offset;
            }

            if ($request->has('event_type')) {
                $filters['event_type'] = $request->event_type;
            }

            if ($request->has('status')) {
                $filters['status'] = $request->status;
            }

            $result = $this->gateSDKService->getWebhooksRaw($filters);

            Log::info('SDK Webhooks Retrieved', ['filters' => $filters, 'count' => count($result['data'] ?? [])]);

            return response()->json([
                'success' => true,
                'message' => 'Webhooks retrieved successfully from Gateway',
                'data' => $result,
                'total_count' => count($result['data'] ?? [])
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Webhooks Retrieval Failed', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve webhooks: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    public function getWebhook(Request $request)
    {
        $request->validate([
            'webhook_id' => 'required|string'
        ]);

        try {
            $result = $this->gateSDKService->getWebhookRaw($request->webhook_id);

            Log::info('SDK Webhook Retrieved', ['webhook_id' => $request->webhook_id, 'response' => $result]);

            return response()->json([
                'success' => true,
                'message' => 'Webhook retrieved successfully from Gateway',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Webhook Retrieval Failed', [
                'error' => $e->getMessage(),
                'webhook_id' => $request->webhook_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve webhook: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    public function createWebhook(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'events' => 'nullable|array|min:1',
            'events.*' => 'required|string',
            'all_events' => 'nullable|boolean',
            'version' => 'nullable|string|in:v0.6,v0.5',
            'is_test' => 'nullable|boolean'
        ]);

        try {
            // Validate that either events or all_events is provided
            if (!$request->has('events') && !$request->has('all_events')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Either events array or all_events boolean must be provided'
                ], 400);
            }

            $webhookData = [
                'title' => $request->title,
                'url' => $request->url,
                'version' => $request->version ?? 'v0.6'
            ];

            // Add either events or all_events
            if ($request->has('all_events') && $request->boolean('all_events')) {
                $webhookData['all_events'] = true;
            } else {
                $webhookData['events'] = $request->events ?? [];
            }

            // Add optional fields
            if ($request->has('is_test')) {
                $webhookData['is_test'] = $request->boolean('is_test');
            }

            $result = $this->gateSDKService->createWebhookRaw($webhookData);

            Log::info('SDK Webhook Created', [
                'request' => $webhookData,
                'response' => $result
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Webhook created successfully in Gateway',
                'data' => $result,
                'gateway_webhook_id' => $result['id'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Webhook Creation Failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create webhook: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    public function updateWebhook(Request $request)
    {
        $request->validate([
            'webhook_id' => 'required|string',
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'events' => 'nullable|array|min:1',
            'events.*' => 'required|string',
            'all_events' => 'nullable|boolean',
            'version' => 'nullable|string|in:v0.6,v0.5',
            'is_test' => 'nullable|boolean'
        ]);

        try {
            // Validate that either events or all_events is provided
            if (!$request->has('events') && !$request->has('all_events')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Either events array or all_events boolean must be provided'
                ], 400);
            }

            $webhookData = [
                'title' => $request->title,
                'url' => $request->url,
                'version' => $request->version ?? 'v0.6'
            ];

            // Add either events or all_events
            if ($request->has('all_events') && $request->boolean('all_events')) {
                $webhookData['all_events'] = true;
            } else {
                $webhookData['events'] = $request->events ?? [];
            }

            // Add optional fields
            if ($request->has('is_test')) {
                $webhookData['is_test'] = $request->boolean('is_test');
            }

            $result = $this->gateSDKService->updateWebhookRaw($request->webhook_id, $webhookData);

            Log::info('SDK Webhook Updated', [
                'webhook_id' => $request->webhook_id,
                'request' => $webhookData,
                'response' => $result
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Webhook updated successfully in Gateway',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Webhook Update Failed', [
                'error' => $e->getMessage(),
                'webhook_id' => $request->webhook_id,
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update webhook: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    public function deleteWebhook(Request $request)
    {
        $request->validate([
            'webhook_id' => 'required|string'
        ]);

        try {
            $result = $this->gateSDKService->deleteWebhookRaw($request->webhook_id);

            Log::info('SDK Webhook Deleted', [
                'webhook_id' => $request->webhook_id,
                'response' => $result
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Webhook deleted successfully from Gateway',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Webhook Deletion Failed', [
                'error' => $e->getMessage(),
                'webhook_id' => $request->webhook_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete webhook: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

}