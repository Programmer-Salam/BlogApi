<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\PublicPostController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('v1')->group(function () {
    // Authentication
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    // Public posts
    Route::get('posts', [PublicPostController::class, 'index']);
    Route::get('posts/{post}', [PublicPostController::class, 'show']);
});

// Protected routes
Route::prefix('v1')->middleware('jwt')->group(function () {
    // User posts
    Route::apiResource('user/posts', PostController::class);

    // Logout (if using Sanctum)
    Route::post('logout', function () {
        auth()->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    });
});
