<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment\WebhookLog;
use App\Services\PaymentGateway\GateSDKService;
use Illuminate\Http\Request;

class WebhooksController extends Controller
{
    protected $gateSDKService;

    public function __construct(GateSDKService $gateSDKService)
    {
        $this->gateSDKService = $gateSDKService;
    }

    public function index(Request $request)
    {
        $query = WebhookLog::orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('event_type') && $request->event_type) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('event_type', 'like', "%{$search}%")
                  ->orWhere('payload', 'like', "%{$search}%");
            });
        }

        $webhooks = $query->paginate(20)->appends($request->query());

        // Get filter options
        $statuses = WebhookLog::select('status')->distinct()->pluck('status')->filter()->sort()->values();
        $eventTypes = WebhookLog::select('event_type')->distinct()->pluck('event_type')->filter()->sort()->values();

        return view('admin.webhooks.index', compact('webhooks', 'statuses', 'eventTypes'));
    }

    public function show(WebhookLog $webhookLog)
    {
        return view('admin.webhooks.show', compact('webhookLog'));
    }

    public function reprocess(WebhookLog $webhookLog)
    {
        try {
            $this->gateSDKService->processWebhook($webhookLog);

            return response()->json([
                'success' => true,
                'message' => 'Webhook reprocessed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Reprocessing failed: ' . $e->getMessage()
            ], 500);
        }
    }
}