<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GroupResourceController;
use App\Http\Controllers\API\StudyGroupController;
use App\Http\Controllers\API\StudyMaterialController;
use App\Http\Controllers\API\StudySessionController;
use App\Http\Controllers\API\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    // Tasks
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::post('/', [TaskController::class, 'store']);
        Route::get('{task}', [TaskController::class, 'show']);
        Route::put('{task}', [TaskController::class, 'update']);
        Route::delete('{task}', [TaskController::class, 'destroy']);
    });

    // Study Sessions
    Route::prefix('study-sessions')->group(function () {
        Route::get('/', [StudySessionController::class, 'index']);
        Route::post('/', [StudySessionController::class, 'store']);
        Route::get('{studySession}', [StudySessionController::class, 'show']);
        Route::put('{studySession}', [StudySessionController::class, 'update']);
        Route::delete('{studySession}', [StudySessionController::class, 'destroy']);
    });

    // Study Materials
    Route::prefix('study-materials')->group(function () {
        Route::get('/', [StudyMaterialController::class, 'index']);
        Route::post('/', [StudyMaterialController::class, 'store']);
        Route::get('{studyMaterial}', [StudyMaterialController::class, 'show']);
        Route::put('{studyMaterial}', [StudyMaterialController::class, 'update']);
        Route::delete('{studyMaterial}', [StudyMaterialController::class, 'destroy']);
    });

    // Study Groups
    Route::prefix('study-groups')->group(function () {
        Route::get('/', [StudyGroupController::class, 'index']);
        Route::post('/', [StudyGroupController::class, 'store']);
        Route::get('{studyGroup}', [StudyGroupController::class, 'show']);
        Route::put('{studyGroup}', [StudyGroupController::class, 'update']);
        Route::delete('{studyGroup}', [StudyGroupController::class, 'destroy']);

        // Memberships
        Route::post('{studyGroup}/members', [StudyGroupController::class, 'addMember']);
        Route::delete('{studyGroup}/members/{userId}', [StudyGroupController::class, 'removeMember']);



        // Group resources for a specific group
        Route::get('{studyGroup}/resources', [GroupResourceController::class, 'indexByGroup']);
        Route::post('{studyGroup}/resources', [GroupResourceController::class, 'store']);
        Route::get('{studyGroup}/resources/{groupResource}', [GroupResourceController::class, 'show']);
        Route::put('{studyGroup}/resources/{groupResource}', [GroupResourceController::class, 'update']);
        Route::delete('{studyGroup}/resources/{groupResource}', [GroupResourceController::class, 'destroy']);
    });

    // Shared resources (across groups where current user is a member, visibility=shared)
    Route::get('shared-resources', [GroupResourceController::class, 'indexShared']);
});