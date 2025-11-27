<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FolderAssignment;
use App\Models\ProjectAssignment;
use App\Models\Project;
use App\Models\ProjectFolder;
use App\Models\User;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    // Project Assignments
    public function assignUserToProject(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $assignment = ProjectAssignment::firstOrCreate([
            'project_id' => $validated['project_id'],
            'user_id' => $validated['user_id'],
        ], [
            'assigned_at' => now(),
        ]);

        return response()->json($assignment, 201);
    }

    public function unassignUserFromProject(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'user_id' => 'required|exists:users,id',
        ]);

        ProjectAssignment::where('project_id', $validated['project_id'])
            ->where('user_id', $validated['user_id'])
            ->delete();

        return response()->json(['message' => 'User unassigned from project successfully']);
    }

    public function getAssignedUsersForProject($projectId)
    {
        $users = User::whereHas('projectAssignments', function ($query) use ($projectId) {
            $query->where('project_id', $projectId);
        })->orderBy('name', 'asc')->get();

        return response()->json($users);
    }

    public function getProjectsForUser($userId)
    {
        $projects = Project::whereHas('assignments', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->orderBy('name', 'asc')->get();

        return response()->json($projects);
    }

    // Folder Assignments
    public function assignUserToFolder(Request $request)
    {
        $validated = $request->validate([
            'folder_id' => 'required|exists:project_folders,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $assignment = FolderAssignment::firstOrCreate([
            'folder_id' => $validated['folder_id'],
            'user_id' => $validated['user_id'],
        ], [
            'assigned_at' => now(),
        ]);

        return response()->json($assignment, 201);
    }

    public function unassignUserFromFolder(Request $request)
    {
        $validated = $request->validate([
            'folder_id' => 'required|exists:project_folders,id',
            'user_id' => 'required|exists:users,id',
        ]);

        FolderAssignment::where('folder_id', $validated['folder_id'])
            ->where('user_id', $validated['user_id'])
            ->delete();

        return response()->json(['message' => 'User unassigned from folder successfully']);
    }

    public function getAssignedUsersForFolder($folderId)
    {
        $users = User::whereHas('folderAssignments', function ($query) use ($folderId) {
            $query->where('folder_id', $folderId);
        })->orderBy('name', 'asc')->get();

        return response()->json($users);
    }

    public function getFoldersForUser($userId)
    {
        $folders = ProjectFolder::whereHas('assignments', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->orderBy('name', 'asc')->get();

        return response()->json($folders);
    }

    public function getProjectFoldersForUser(Request $request, $userId)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        $folders = ProjectFolder::where('project_id', $request->project_id)
            ->whereHas('assignments', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($folders);
    }
}
