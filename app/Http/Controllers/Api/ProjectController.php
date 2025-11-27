<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with(['creator', 'assignedUsers'])->orderBy('created_at', 'desc')->get();
        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'created_by' => 'required|integer',
        ]);

        // Get authenticated user from token
        $authenticatedUser = $request->user();
        
        // Check if created_by exists in users table
        $user = \App\Models\User::find($validated['created_by']);
        
        if (!$user) {
            // If the ID doesn't exist in users table, it might be from HR/Manager/COO account
            // In this case, we need to either:
            // 1. Create a user record for them, or
            // 2. Use a default user, or
            // 3. Allow null (if migration allows it)
            
            // For now, let's check if authenticated user is HR/Manager/COO and create/find a user
            if ($authenticatedUser) {
                // Try to find or create a user based on the authenticated account
                $user = \App\Models\User::firstOrCreate(
                    ['email' => $authenticatedUser->email],
                    [
                        'name' => $authenticatedUser->name,
                        'last_name' => $authenticatedUser->last_name ?? '',
                        'password' => $authenticatedUser->password ?? bcrypt('temp'),
                        'company_name' => $authenticatedUser->company_name ?? null,
                        'position' => $authenticatedUser->position ?? null,
                    ]
                );
                $validated['created_by'] = $user->id;
            } else {
                return response()->json([
                    'message' => 'The selected created by is invalid.',
                    'errors' => ['created_by' => ['The user ID does not exist in the users table.']]
                ], 422);
            }
        }

        $project = Project::create($validated);
        $project->load('creator');

        return response()->json($project, 201);
    }

    public function show(string $id)
    {
        $project = Project::with(['creator', 'folders', 'assignedUsers'])->findOrFail($id);
        return response()->json($project);
    }

    public function update(Request $request, string $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'description' => 'nullable|string',
        ]);

        $project->update($validated);

        return response()->json($project);
    }

    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return response()->json(['message' => 'Project deleted successfully']);
    }
}
