<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Job; // Import Job model
use App\Models\JobDetail; // Import JobDetail model

class JobDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing job details (optional, useful for re-seeding)
        JobDetail::truncate();

        // Get all existing jobs to link details to them
        $jobs = Job::all();

        // Define some sample responsibilities and skills
        $responsibilities = [
            'Develop and maintain high-quality software',
            'Collaborate with cross-functional teams',
            'Participate in code reviews',
            'Troubleshoot and debug applications',
            'Write technical documentation',
            'Design and implement new features',
            'Ensure software performance and scalability',
        ];

        $skills = [
            'Problem Solving',
            'Communication',
            'Teamwork',
            'Adaptability',
            'Critical Thinking',
            'Attention to Detail',
            'Time Management',
        ];

        foreach ($jobs as $job) {
            // Create a JobDetail for each existing Job
            JobDetail::create([
                'job_id' => $job->id,
                'description' => "This is a detailed description for the {$job->title} position at {$job->company}. We are looking for a highly motivated individual to join our dynamic team and contribute to exciting projects.",
                'key_responsibilities' => array_slice($responsibilities, 0, rand(3, 5)), // Take 3-5 random responsibilities
                'professional_skills' => array_slice($skills, 0, rand(3, 5)), // Take 3-5 random skills
            ]);
        }
    }
}
