<?php

namespace App\Http\Controllers\SDK;

use App\Http\Controllers\Controller;
use App\Services\PaymentGateway\GateSDKService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientsController extends Controller
{
    protected $gateSDKService;

    public function __construct(GateSDKService $gateSDKService)
    {
        $this->gateSDKService = $gateSDKService;
    }

    public function index()
    {
        return view('sdk.clients.index');
    }

    public function getClients(Request $request)
    {
        try {
            $filters = [];

            // Note: Clients API uses cursor-based pagination, not page_size/offset
            if ($request->has('cursor')) {
                $filters['cursor'] = $request->cursor;
            }

            if ($request->has('filter_email')) {
                $filters['filter_email'] = $request->filter_email;
            }

            if ($request->has('filter_phone')) {
                $filters['filter_phone'] = $request->filter_phone;
            }

            if ($request->has('q')) {
                $filters['search_query'] = $request->q;
            }

            $result = $this->gateSDKService->getClientsRaw($filters);

            Log::info('SDK Clients Retrieved', ['filters' => $filters, 'count' => count($result['data'] ?? [])]);

            return response()->json([
                'success' => true,
                'message' => 'Clients retrieved successfully from Gateway',
                'data' => $result,
                'total_count' => count($result['data'] ?? [])
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Clients Retrieval Failed', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve clients: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    public function getClient(Request $request)
    {
        $request->validate([
            'client_id' => 'required|string'
        ]);

        try {
            $result = $this->gateSDKService->getClientRaw($request->client_id);

            Log::info('SDK Client Retrieved', ['client_id' => $request->client_id, 'response' => $result]);

            return response()->json([
                'success' => true,
                'message' => 'Client retrieved successfully from Gateway',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Client Retrieval Failed', [
                'error' => $e->getMessage(),
                'client_id' => $request->client_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve client: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    public function createClient(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:254',
            'phone' => 'required|string|max:32',
            'first_name' => 'nullable|string|max:63',
            'last_name' => 'nullable|string|max:63',
            'birth_date' => 'nullable|date_format:Y-m-d'
        ]);

        try {
            $clientData = [
                'email' => $request->email,
                'phone' => $request->phone
            ];

            // Add optional fields
            if ($request->has('first_name')) {
                $clientData['first_name'] = $request->first_name;
            }

            if ($request->has('last_name')) {
                $clientData['last_name'] = $request->last_name;
            }

            if ($request->has('birth_date')) {
                $clientData['birth_date'] = $request->birth_date;
            }

            $result = $this->gateSDKService->createClientRaw($clientData);

            Log::info('SDK Client Created', [
                'request' => $clientData,
                'response' => $result
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Client created successfully in Gateway',
                'data' => $result,
                'gateway_client_id' => $result['id'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Client Creation Failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create client: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    public function updateClient(Request $request)
    {
        $request->validate([
            'client_id' => 'required|string',
            'email' => 'required|email|max:254',
            'phone' => 'required|string|max:32',
            'first_name' => 'nullable|string|max:63',
            'last_name' => 'nullable|string|max:63',
            'birth_date' => 'nullable|date_format:Y-m-d'
        ]);

        try {
            $clientData = [
                'email' => $request->email,
                'phone' => $request->phone
            ];

            // Add optional fields
            if ($request->has('first_name')) {
                $clientData['first_name'] = $request->first_name;
            }

            if ($request->has('last_name')) {
                $clientData['last_name'] = $request->last_name;
            }

            if ($request->has('birth_date')) {
                $clientData['birth_date'] = $request->birth_date;
            }

            $result = $this->gateSDKService->updateClientRaw($request->client_id, $clientData);

            Log::info('SDK Client Updated', [
                'client_id' => $request->client_id,
                'request' => $clientData,
                'response' => $result
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Client updated successfully in Gateway',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Client Update Failed', [
                'error' => $e->getMessage(),
                'client_id' => $request->client_id,
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update client: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    public function partialUpdateClient(Request $request)
    {
        $request->validate([
            'client_id' => 'required|string',
            'email' => 'nullable|email|max:254',
            'phone' => 'nullable|string|max:32',
            'first_name' => 'nullable|string|max:63',
            'last_name' => 'nullable|string|max:63',
            'birth_date' => 'nullable|date_format:Y-m-d'
        ]);

        try {
            $clientData = [];

            // Only include fields that are present
            if ($request->has('email')) {
                $clientData['email'] = $request->email;
            }

            if ($request->has('phone')) {
                $clientData['phone'] = $request->phone;
            }

            if ($request->has('first_name')) {
                $clientData['first_name'] = $request->first_name;
            }

            if ($request->has('last_name')) {
                $clientData['last_name'] = $request->last_name;
            }

            if ($request->has('birth_date')) {
                $clientData['birth_date'] = $request->birth_date;
            }

            if (empty($clientData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'At least one field must be provided for partial update'
                ], 400);
            }

            $result = $this->gateSDKService->partialUpdateClientRaw($request->client_id, $clientData);

            Log::info('SDK Client Partially Updated', [
                'client_id' => $request->client_id,
                'request' => $clientData,
                'response' => $result
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Client updated successfully in Gateway',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Client Partial Update Failed', [
                'error' => $e->getMessage(),
                'client_id' => $request->client_id,
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update client: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }

    public function deleteClient(Request $request)
    {
        $request->validate([
            'client_id' => 'required|string'
        ]);

        try {
            $result = $this->gateSDKService->deleteClientRaw($request->client_id);

            Log::info('SDK Client Deleted', [
                'client_id' => $request->client_id,
                'response' => $result
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Client deleted successfully from Gateway',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('SDK Client Deletion Failed', [
                'error' => $e->getMessage(),
                'client_id' => $request->client_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete client: ' . $e->getMessage(),
                'error_type' => get_class($e)
            ], 500);
        }
    }
}