<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Job; // Import your Job model
use Illuminate\Support\Str; // Import Str for UUID generation
use Illuminate\Support\Facades\DB; // <--- ADD THIS LINE: Import the DB facade

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- ADD THESE LINES TO TEMPORARILY DISABLE FOREIGN KEY CHECKS ---
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // ------------------------------------------------------------------

        // Clear existing jobs (optional, useful for re-seeding)
        Job::truncate();

        // --- ADD THIS LINE TO RE-ENABLE FOREIGN KEY CHECKS ---
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        // ---------------------------------------------------

        $jobsv = [
            [
                'title' => 'Forward Security Director',
                'company' => 'Block N. Scrapper and Schuler Co',
                'location' => 'New York, USA',
                'salary' => '$40000-$42000',
                'posted_time' => '10 minutes ago',
                'job_type' => 'Fulltime',
                'category' => 'Finance & Operations',
                'experience_level' => 'Director',
                'tags' => ['Security', 'Finance'],
                'company_initial' => 'B',
            ],
            [
                'title' => 'Regional Creative Facilitator',
                'company' => 'Klood - Rekrut ID Co',
                'location' => 'Los Angeles, USA',
                'salary' => '$20000-$32000',
                'posted_time' => '1 day ago',
                'job_type' => 'Part Time',
                'category' => 'Commerce',
                'experience_level' => 'Mid-Senior',
                'tags' => ['Creative', 'Design'],
                'company_initial' => 'K',
            ],
            [
                'title' => 'Internal Integration Planner',
                'company' => 'Wag. Otacny Aircraft Inc',
                'location' => 'Ohio, USA',
                'salary' => '$45000-$50000',
                'posted_time' => '2 days ago',
                'job_type' => 'Fulltime',
                'category' => 'Commerce',
                'experience_level' => 'Executive',
                'tags' => ['Integration', 'Management'],
                'company_initial' => 'W',
            ],
            [
                'title' => 'District Intranet Director',
                'company' => 'Asama. RekrutID Co',
                'location' => 'Florida, USA',
                'salary' => '$45000-$48000',
                'posted_time' => '2 days ago',
                'job_type' => 'Fulltime',
                'category' => 'Hotels & Tourism',
                'experience_level' => 'Director',
                'tags' => ['IT', 'Network'],
                'company_initial' => 'A',
            ],
            [
                'title' => 'Corporate Tactics Facilitator',
                'company' => 'Global Software and Technologies',
                'location' => 'Boston, USA',
                'salary' => '$35000-$40000',
                'posted_time' => '5 days ago',
                'job_type' => 'Fulltime',
                'category' => 'Financial Services',
                'experience_level' => 'Mid-Senior',
                'tags' => ['Tactics', 'Software'],
                'company_initial' => 'G',
            ],
            [
                'title' => 'Forward Accounts Consultant',
                'company' => 'Riva Group',
                'location' => 'Oregon, USA',
                'salary' => '$30000-$35000',
                'posted_time' => '5 days ago',
                'job_type' => 'Fulltime',
                'category' => 'Financial Services',
                'experience_level' => 'Associate',
                'tags' => ['Accounts', 'Consultant'],
                'company_initial' => 'R',
            ],
            [
                'title' => 'Senior UX Designer',
                'company' => 'DesignWorks',
                'location' => 'New York, USA',
                'salary' => '$70000-$90000',
                'posted_time' => '1 hour ago',
                'job_type' => 'Fulltime',
                'category' => 'Commerce',
                'experience_level' => 'Mid-Senior',
                'tags' => ['Design', 'UX'],
                'company_initial' => 'D',
            ],
            [
                'title' => 'Junior Software Engineer',
                'company' => 'Tech Solutions',
                'location' => 'Los Angeles, USA',
                'salary' => '$50000-$60000',
                'posted_time' => '3 hours ago',
                'job_type' => 'Fulltime',
                'category' => 'Financial Services',
                'experience_level' => 'Entry Level',
                'tags' => ['Software', 'Development'],
                'company_initial' => 'T',
            ],
        ];

        foreach ($jobsv as $jobData) {
            Job::create([
                'title' => $jobData['title'],
                'company' => $jobData['company'],
                'location' => $jobData['location'],
                'salary' => $jobData['salary'],
                'posted_time' => $jobData['posted_time'],
                'job_type' => $jobData['job_type'],
                'category' => $jobData['category'],
                'experience_level' => $jobData['experience_level'],
                'tags' => $jobData['tags'],
                'company_initial' => $jobData['company_initial'],
            ]);
        }
    }
}