<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // For validation

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jobs = Job::all();
        return response()->json([
            'message' => 'Jobs retrieved successfully',
            'data' => $jobs
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'salary' => 'required|string|max:255',
            'postedTime' => 'required|string|max:255',
            'jobType' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'experienceLevel' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'companyInitial' => 'nullable|string|max:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }

        // Map Flutter's camelCase to Laravel's snake_case for creation
        $job = Job::create([
            'title' => $request->title,
            'company' => $request->company,
            'location' => $request->location,
            'salary' => $request->salary,
            'posted_time' => $request->postedTime,
            'job_type' => $request->jobType,
            'category' => $request->category ?? 'Other',
            'experience_level' => $request->experienceLevel ?? 'Entry Level',
            'tags' => $request->tags ?? [],
            'company_initial' => $request->companyInitial,
        ]);

        return response()->json([
            'message' => 'Job created successfully',
            'data' => $job
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $job = Job::find($id);

        if (!$job) {
            return response()->json([
                'message' => 'Job not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Job retrieved successfully',
            'data' => $job
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $job = Job::find($id);

        if (!$job) {
            return response()->json([
                'message' => 'Job not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'company' => 'string|max:255',
            'location' => 'string|max:255',
            'salary' => 'string|max:255',
            'postedTime' => 'string|max:255',
            'jobType' => 'string|max:255',
            'category' => 'nullable|string|max:255',
            'experienceLevel' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'companyInitial' => 'nullable|string|max:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }

        // Map Flutter's camelCase to Laravel's snake_case for update
        $job->update([
            'title' => $request->title ?? $job->title,
            'company' => $request->company ?? $job->company,
            'location' => $request->location ?? $job->location,
            'salary' => $request->salary ?? $job->salary,
            'posted_time' => $request->postedTime ?? $job->posted_time,
            'job_type' => $request->jobType ?? $job->job_type,
            'category' => $request->category ?? $job->category,
            'experience_level' => $request->experienceLevel ?? $job->experience_level,
            'tags' => $request->tags ?? $job->tags,
            'company_initial' => $request->companyInitial ?? $job->company_initial,
        ]);

        return response()->json([
            'message' => 'Job updated successfully',
            'data' => $job
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $job = Job::find($id);

        if (!$job) {
            return response()->json([
                'message' => 'Job not found'
            ], 404);
        }

        $job->delete();

        return response()->json([
            'message' => 'Job deleted successfully'
        ], 200);
    }
}
