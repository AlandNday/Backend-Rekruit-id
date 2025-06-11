<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\JobDetailController; // Import the JobDetailController

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Job API routes
Route::apiResource('jobs', JobController::class);

// Job Detail API routes
// This route will handle GET /api/jobs/{job_id}/detail
Route::get('jobs/{id}/detail', [JobDetailController::class, 'show']);

// If you need full CRUD for job details directly (less common than show)
// Route::apiResource('job-details', JobDetailController::class);