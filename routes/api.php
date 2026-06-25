<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\WorkspaceMemberController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::put('password', [AuthController::class, 'updatePassword'])->middleware('auth:sanctum');
    Route::delete('account', [AuthController::class, 'deleteAccount'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('workspaces', WorkspaceController::class);
    Route::post('workspaces/{workspace}/members', [WorkspaceMemberController::class, 'store']);
    Route::delete('workspaces/{workspace}/members/{user}', [WorkspaceMemberController::class, 'destroy']);
    Route::apiResource('workspaces.projects', ProjectController::class)->shallow();
    Route::apiResource('projects.tasks', TaskController::class)->shallow();
    Route::apiResource('workspaces.webhooks', WebhookController::class)->shallow();
    Route::get('tasks/{task}/comments', [CommentController::class, 'index']);
    Route::post('tasks/{task}/comments', [CommentController::class, 'store']);
    Route::put('comments/{comment}', [CommentController::class, 'update']);
    Route::delete('comments/{comment}', [CommentController::class, 'destroy']);
});
