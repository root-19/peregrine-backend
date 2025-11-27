<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProjectFolder;
use Illuminate\Http\Request;

class ProjectFolderController extends Controller
{
    public function index(Request $request)
    {
        $query = ProjectFolder::query();

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('parent_folder_id')) {
            $query->where('parent_folder_id', $request->parent_folder_id);
        } else {
            $query->whereNull('parent_folder_id');
        }

        $folders = $query->orderBy('name', 'asc')->get();
        return response()->json($folders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string',
            'parent_folder_id' => 'nullable|exists:project_folders,id',
        ]);

        $folder = ProjectFolder::create($validated);
        $folder->load('project');

        return response()->json($folder, 201);
    }

    public function show(string $id)
    {
        $folder = ProjectFolder::with(['project', 'parentFolder', 'subfolders', 'assignedUsers'])->findOrFail($id);
        return response()->json($folder);
    }

    public function update(Request $request, string $id)
    {
        $folder = ProjectFolder::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string',
        ]);

        $folder->update($validated);

        return response()->json($folder);
    }

    public function destroy(string $id)
    {
        $folder = ProjectFolder::findOrFail($id);
        $folder->delete();

        return response()->json(['message' => 'Project folder deleted successfully']);
    }
}
