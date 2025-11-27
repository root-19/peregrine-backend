<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subfolder;
use Illuminate\Http\Request;

class SubfolderController extends Controller
{
    public function index(Request $request)
    {
        $query = Subfolder::query();

        if ($request->has('project_folder_id')) {
            $query->where('project_folder_id', $request->project_folder_id);
        }

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('button_name')) {
            $query->where('button_name', $request->button_name);
        }

        $subfolders = $query->orderBy('name', 'asc')->get();
        return response()->json($subfolders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_folder_id' => 'required|exists:project_folders,id',
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string',
            'button_name' => 'required|string',
        ]);

        $subfolder = Subfolder::create($validated);
        $subfolder->load(['projectFolder', 'project']);

        return response()->json($subfolder, 201);
    }

    public function show(string $id)
    {
        $subfolder = Subfolder::with(['projectFolder', 'project'])->findOrFail($id);
        return response()->json($subfolder);
    }

    public function update(Request $request, string $id)
    {
        $subfolder = Subfolder::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string',
        ]);

        $subfolder->update($validated);

        return response()->json($subfolder);
    }

    public function destroy(string $id)
    {
        $subfolder = Subfolder::findOrFail($id);
        $subfolder->delete();

        return response()->json(['message' => 'Subfolder deleted successfully']);
    }
}
