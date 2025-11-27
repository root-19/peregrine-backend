<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Procurement;
use Illuminate\Http\Request;

class ProcurementController extends Controller
{
    public function index(Request $request)
    {
        $query = Procurement::query();

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('folder_id')) {
            $query->where('folder_id', $request->folder_id);
        }

        $procurements = $query->orderBy('name', 'asc')->get();
        return response()->json($procurements);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'folder_id' => 'required|exists:project_folders,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $procurement = Procurement::create($validated);
        $procurement->load(['project', 'folder']);

        return response()->json($procurement, 201);
    }

    public function show(string $id)
    {
        $procurement = Procurement::with(['project', 'folder'])->findOrFail($id);
        return response()->json($procurement);
    }

    public function update(Request $request, string $id)
    {
        $procurement = Procurement::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'description' => 'nullable|string',
        ]);

        $procurement->update($validated);

        return response()->json($procurement);
    }

    public function destroy(string $id)
    {
        $procurement = Procurement::findOrFail($id);
        $procurement->delete();

        return response()->json(['message' => 'Procurement deleted successfully']);
    }
}
