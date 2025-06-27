<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\JobDetailController; // Import the JobDetailController
use App\Http\Controllers\AuthController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (authentication required via our custom ApiTokenMiddleware,
// which is now automatically part of the 'api' middleware group due to bootstrap/app.php setup)
Route::middleware('api')->group(function () { // <-- Use 'api' middleware group here
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});
// Job API routes
Route::apiResource('jobs', JobController::class);

// Job Detail API routes
// This route will handle GET /api/jobs/{job_id}/detail
Route::get('jobs/{id}/detail', [JobDetailController::class, 'show']);

// If you need full CRUD for job details directly (less common than show)
// Route::apiResource('job-details', JobDetailController::class);