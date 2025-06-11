<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job; // Import the Job model
use App\Models\JobDetail; // Import the JobDetail model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobDetailController extends Controller
{
    /**
     * Display the specified job detail.
     * Accessible via /api/jobs/{id}/detail
     */
    public function show(string $id)
    {
        // Attempt to find the JobDetail along with its associated Job
        $jobDetail = JobDetail::with('job')->find($id);

        if (!$jobDetail) {
            return response()->json([
                'message' => 'Job detail not found for the given job ID.'
            ], 404);
        }

        // Fetch some related jobs (e.g., 3 random jobs, excluding the current one)
        $relatedJobs = Job::where('id', '!=', $id)
                          ->inRandomOrder()
                          ->limit(3)
                          ->get();

        // Return a structured response matching Flutter model expectation
        return response()->json([
            'message' => 'Job detail retrieved successfully',
            'data' => [
                'job' => $jobDetail->job, // The basic job information
                'description' => $jobDetail->description,
                'key_responsibilities' => $jobDetail->key_responsibilities,
                'professional_skills' => $jobDetail->professional_skills,
                'related_jobs' => $relatedJobs, // List of other Job objects
            ]
        ], 200);
    }

    /**
     * Store a newly created job detail in storage.
     * (Optional: You might not need this if details are always created with jobs)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|string|exists:jobsv,id|unique:job_details,job_id',
            'description' => 'required|string',
            'key_responsibilities' => 'nullable|array',
            'professional_skills' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $jobDetail = JobDetail::create([
            'job_id' => $request->job_id,
            'description' => $request->description,
            'key_responsibilities' => $request->key_responsibilities ?? [],
            'professional_skills' => $request->professional_skills ?? [],
        ]);

        return response()->json([
            'message' => 'Job detail created successfully',
            'data' => $jobDetail
        ], 201);
    }

    /**
     * Update the specified job detail in storage.
     */
    public function update(Request $request, string $id)
    {
        $jobDetail = JobDetail::find($id);

        if (!$jobDetail) {
            return response()->json(['message' => 'Job detail not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'description' => 'string',
            'key_responsibilities' => 'nullable|array',
            'professional_skills' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $jobDetail->update([
            'description' => $request->description ?? $jobDetail->description,
            'key_responsibilities' => $request->key_responsibilities ?? $jobDetail->key_responsibilities,
            'professional_skills' => $request->professional_skills ?? $jobDetail->professional_skills,
        ]);

        return response()->json([
            'message' => 'Job detail updated successfully',
            'data' => $jobDetail
        ], 200);
    }

    /**
     * Remove the specified job detail from storage.
     */
    public function destroy(string $id)
    {
        $jobDetail = JobDetail::find($id);

        if (!$jobDetail) {
            return response()->json(['message' => 'Job detail not found.'], 404);
        }

        $jobDetail->delete();

        return response()->json(['message' => 'Job detail deleted successfully.'], 200);
    }
}
