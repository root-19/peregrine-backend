<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentFolder;
use Illuminate\Http\Request;

class DocumentFolderController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentFolder::query();

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $folders = $query->orderBy('created_at', 'desc')->get();
        return response()->json($folders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'project_name' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'account' => 'required|string|in:user,hr,manager_coo',
            'folder_name' => 'required|string',
            'category' => 'required|string|in:Procurement,Community',
        ]);

        $folder = DocumentFolder::create($validated);
        $folder->load(['project', 'user']);

        return response()->json($folder, 201);
    }

    public function show(string $id)
    {
        $folder = DocumentFolder::with(['project', 'user'])->findOrFail($id);
        return response()->json($folder);
    }

    public function update(Request $request, string $id)
    {
        $folder = DocumentFolder::findOrFail($id);

        $validated = $request->validate([
            'folder_name' => 'sometimes|required|string',
            'category' => 'sometimes|required|string|in:Procurement,Community',
        ]);

        $folder->update($validated);

        return response()->json($folder);
    }

    public function destroy(string $id)
    {
        $folder = DocumentFolder::findOrFail($id);
        $folder->delete();

        return response()->json(['message' => 'Document folder deleted successfully']);
    }
}

