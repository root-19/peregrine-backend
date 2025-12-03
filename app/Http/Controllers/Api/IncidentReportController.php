<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IncidentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncidentReportController extends Controller
{
    /**
     * Get all incident reports (for HR, Manager, COO to view)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $status = $request->query('status');
        
        $query = IncidentReport::orderBy('created_at', 'desc');
        
        if ($status) {
            $query->where('status', $status);
        }

        $reports = $query->get();

        return response()->json($reports);
    }

    /**
     * Get incident reports submitted by current user
     */
    public function myReports(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $reports = IncidentReport::where('reported_by_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reports);
    }

    /**
     * Store a new incident report (submitted by user)
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'reported_by_name' => 'required|string',
            'reported_by_position' => 'required|string',
            'date_of_report' => 'required|date',
            'location' => 'required|string',
            'date_of_incident' => 'required|date',
            'time_of_incident' => 'required|string',
            'time_period' => 'required|string|in:AM,PM',
            'description_of_accident' => 'required|string',
            'is_someone_injured' => 'required|boolean',
            'injury_description' => 'nullable|string',
            'people_involved' => 'nullable|array',
        ]);

        $report = IncidentReport::create([
            'reported_by_id' => $user->id,
            'reported_by_name' => $validated['reported_by_name'],
            'reported_by_position' => $validated['reported_by_position'],
            'date_of_report' => $validated['date_of_report'],
            'location' => $validated['location'],
            'date_of_incident' => $validated['date_of_incident'],
            'time_of_incident' => $validated['time_of_incident'],
            'time_period' => $validated['time_period'],
            'description_of_accident' => $validated['description_of_accident'],
            'is_someone_injured' => $validated['is_someone_injured'],
            'injury_description' => $validated['injury_description'],
            'people_involved' => $validated['people_involved'],
            'status' => 'pending',
        ]);

        return response()->json($report, 201);
    }

    /**
     * Update incident report status (for HR, Manager, COO)
     */
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'status' => 'required|string|in:pending,reviewed,resolved',
            'resolution' => 'nullable|string',
            'reviewer_type' => 'required|string|in:hr,manager_coo',
        ]);

        $report = IncidentReport::findOrFail($id);
        
        $report->status = $validated['status'];
        $report->reviewed_by_id = $user->id;
        $report->reviewed_by_type = $validated['reviewer_type'];
        $report->reviewed_at = now();
        
        if (isset($validated['resolution'])) {
            $report->resolution = $validated['resolution'];
        }
        
        if ($validated['status'] === 'resolved') {
            $report->resolved_at = now();
        }
        
        $report->save();

        return response()->json($report);
    }

    /**
     * Get incident report by ID
     */
    public function show($id)
    {
        $report = IncidentReport::findOrFail($id);
        return response()->json($report);
    }

    /**
     * Delete incident report
     */
    public function destroy($id)
    {
        $report = IncidentReport::findOrFail($id);
        $report->delete();
        return response()->json(['message' => 'Incident report deleted successfully']);
    }
}

