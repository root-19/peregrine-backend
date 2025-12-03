<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\HRAccountController;
use App\Http\Controllers\Api\ManagerCOOAccountController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\PositionController;
use App\Http\Controllers\Api\ProcurementController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\DocumentFolderController;
use App\Http\Controllers\Api\ProjectFolderController;
use App\Http\Controllers\Api\SubfolderController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // User routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/email', [UserController::class, 'getByEmail']);
        Route::get('/position', [UserController::class, 'getByPosition']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    // HR Account routes
    Route::prefix('hr-accounts')->group(function () {
        Route::get('/', [HRAccountController::class, 'index']);
        Route::post('/', [HRAccountController::class, 'store']);
        Route::get('/email', [HRAccountController::class, 'getByEmail']);
        Route::get('/{id}', [HRAccountController::class, 'show']);
        Route::put('/{id}', [HRAccountController::class, 'update']);
        Route::delete('/{id}', [HRAccountController::class, 'destroy']);
    });

    // Manager/COO Account routes
    Route::prefix('manager-coo-accounts')->group(function () {
        Route::get('/', [ManagerCOOAccountController::class, 'index']);
        Route::post('/', [ManagerCOOAccountController::class, 'store']);
        Route::get('/email', [ManagerCOOAccountController::class, 'getByEmail']);
        Route::get('/{id}', [ManagerCOOAccountController::class, 'show']);
        Route::put('/{id}', [ManagerCOOAccountController::class, 'update']);
        Route::delete('/{id}', [ManagerCOOAccountController::class, 'destroy']);
    });

    // Project routes
    Route::prefix('projects')->group(function () {
        Route::get('/', [ProjectController::class, 'index']);
        Route::post('/', [ProjectController::class, 'store']);
        Route::get('/{id}', [ProjectController::class, 'show']);
        Route::put('/{id}', [ProjectController::class, 'update']);
        Route::delete('/{id}', [ProjectController::class, 'destroy']);
    });

    // Project Folder routes
    Route::prefix('project-folders')->group(function () {
        Route::get('/', [ProjectFolderController::class, 'index']);
        Route::post('/', [ProjectFolderController::class, 'store']);
        Route::get('/{id}', [ProjectFolderController::class, 'show']);
        Route::put('/{id}', [ProjectFolderController::class, 'update']);
        Route::delete('/{id}', [ProjectFolderController::class, 'destroy']);
    });

    // Subfolder routes
    Route::prefix('subfolders')->group(function () {
        Route::get('/', [SubfolderController::class, 'index']);
        Route::post('/', [SubfolderController::class, 'store']);
        Route::get('/{id}', [SubfolderController::class, 'show']);
        Route::put('/{id}', [SubfolderController::class, 'update']);
        Route::delete('/{id}', [SubfolderController::class, 'destroy']);
    });

    // Document Folder routes
    Route::prefix('document-folders')->group(function () {
        Route::get('/', [DocumentFolderController::class, 'index']);
        Route::post('/', [DocumentFolderController::class, 'store']);
        Route::get('/{id}', [DocumentFolderController::class, 'show']);
        Route::put('/{id}', [DocumentFolderController::class, 'update']);
        Route::delete('/{id}', [DocumentFolderController::class, 'destroy']);
    });

    // Position routes
    Route::prefix('positions')->group(function () {
        Route::get('/', [PositionController::class, 'index']);
        Route::post('/', [PositionController::class, 'store']);
        Route::get('/{id}', [PositionController::class, 'show']);
        Route::put('/{id}', [PositionController::class, 'update']);
        Route::delete('/{id}', [PositionController::class, 'destroy']);
    });

    // Procurement routes
    Route::prefix('procurement')->group(function () {
        Route::get('/', [ProcurementController::class, 'index']);
        Route::post('/', [ProcurementController::class, 'store']);
        Route::get('/{id}', [ProcurementController::class, 'show']);
        Route::put('/{id}', [ProcurementController::class, 'update']);
        Route::delete('/{id}', [ProcurementController::class, 'destroy']);
    });

    // Assignment routes
    Route::prefix('assignments')->group(function () {
        // Project assignments
        Route::post('/project', [AssignmentController::class, 'assignUserToProject']);
        Route::delete('/project', [AssignmentController::class, 'unassignUserFromProject']);
        Route::get('/project/{projectId}/users', [AssignmentController::class, 'getAssignedUsersForProject']);
        Route::get('/user/{userId}/projects', [AssignmentController::class, 'getProjectsForUser']);

        // Folder assignments
        Route::post('/folder', [AssignmentController::class, 'assignUserToFolder']);
        Route::delete('/folder', [AssignmentController::class, 'unassignUserFromFolder']);
        Route::get('/folder/{folderId}/users', [AssignmentController::class, 'getAssignedUsersForFolder']);
        Route::get('/user/{userId}/folders', [AssignmentController::class, 'getFoldersForUser']);
        Route::get('/user/{userId}/project-folders', [AssignmentController::class, 'getProjectFoldersForUser']);
    });

    // Message routes
    Route::prefix('messages')->group(function () {
        Route::get('/conversations', [MessageController::class, 'getConversations']);
        Route::get('/unread-count', [MessageController::class, 'getUnreadCount']);
        Route::get('/{userId}', [MessageController::class, 'getMessages']);
        Route::post('/', [MessageController::class, 'sendMessage']);
        Route::put('/{userId}/read', [MessageController::class, 'markAsRead']);
    });
});
