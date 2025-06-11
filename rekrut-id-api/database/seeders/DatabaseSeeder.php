<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Make sure this is imported if you're seeding users

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear dependent tables first
        $this->call(JobDetailSeeder::class); // Truncates job_details
        $this->call(JobSeeder::class);       // Truncates jobsv

        // Then re-create data, ensuring Job data exists before JobDetail
        $this->call(JobSeeder::class);        // Create jobs
        $this->call(JobDetailSeeder::class);  // Create job details (linking to existing jobs)
    }
}