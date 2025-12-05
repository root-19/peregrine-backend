<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialRequestController extends Controller
{
    /**
     * Get all material requests (for HR, Manager, COO to view)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $status = $request->query('status');
        $priority = $request->query('priority');
        
        $query = MaterialRequest::orderBy('created_at', 'desc');
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($priority) {
            $query->where('priority', $priority);
        }

        $requests = $query->get();

        return response()->json($requests);
    }

    /**
     * Get material requests submitted by current user
     */
    public function myRequests(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $requests = MaterialRequest::where('requested_by_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($requests);
    }

    /**
     * Store a new material request (submitted by user)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'requested_by_name' => 'required|string',
            'requested_by_position' => 'required|string',
            'department' => 'nullable|string',
            'date_of_request' => 'required|date',
            'date_needed' => 'required|date',
            'project_name' => 'nullable|string',
            'project_location' => 'nullable|string',
            'purpose' => 'required|string',
            'materials' => 'required|array|min:1',
            'materials.*.item_name' => 'required|string',
            'materials.*.quantity' => 'required|integer|min:1',
            'materials.*.unit' => 'required|string',
            'materials.*.specifications' => 'nullable|string',
            'priority' => 'required|string|in:low,medium,high,urgent',
        ]);

        $materialRequest = MaterialRequest::create([
            'requested_by_id' => $user->id,
            'requested_by_name' => $validated['requested_by_name'],
            'requested_by_position' => $validated['requested_by_position'],
            'department' => $validated['department'] ?? null,
            'date_of_request' => $validated['date_of_request'],
            'date_needed' => $validated['date_needed'],
            'project_name' => $validated['project_name'] ?? null,
            'project_location' => $validated['project_location'] ?? null,
            'purpose' => $validated['purpose'],
            'materials' => $validated['materials'],
            'priority' => $validated['priority'],
            'status' => 'pending',
        ]);

        return response()->json($materialRequest, 201);
    }

    /**
     * Update material request status (for HR, Manager, COO)
     */
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:pending,approved,rejected,processing,completed',
            'remarks' => 'nullable|string',
            'rejection_reason' => 'nullable|string',
            'approver_type' => 'required|string|in:hr,manager_coo',
        ]);

        $materialRequest = MaterialRequest::findOrFail($id);
        
        $materialRequest->status = $validated['status'];
        
        if (isset($validated['remarks'])) {
            $materialRequest->remarks = $validated['remarks'];
        }
        
        if ($validated['status'] === 'rejected' && isset($validated['rejection_reason'])) {
            $materialRequest->rejection_reason = $validated['rejection_reason'];
        }
        
        if (in_array($validated['status'], ['approved', 'rejected'])) {
            $materialRequest->approved_by_id = $user->id;
            $materialRequest->approved_by_type = $validated['approver_type'];
            $materialRequest->approved_at = now();
        }
        
        if ($validated['status'] === 'completed') {
            $materialRequest->completed_at = now();
        }
        
        $materialRequest->save();

        return response()->json($materialRequest);
    }

    /**
     * Get material request by ID
     */
    public function show($id)
    {
        $materialRequest = MaterialRequest::findOrFail($id);
        return response()->json($materialRequest);
    }

    /**
     * Delete material request
     */
    public function destroy($id)
    {
        $materialRequest = MaterialRequest::findOrFail($id);
        $materialRequest->delete();
        return response()->json(['message' => 'Material request deleted successfully']);
    }
}

