<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('job_details', function (Blueprint $table) {
            // No incrementing primary key here, we'll use job_id as primary/foreign key
            $table->string('job_id')->primary(); // Make job_id the primary key

            $table->text('description');
            $table->json('key_responsibilities')->default('[]'); // Store as JSON array
            $table->json('professional_skills')->default('[]'); // Store as JSON array

            $table->timestamps();

            // Foreign key constraint
            // This ensures job_details.job_id matches an id in jobsv.id
            $table->foreign('job_id')
                  ->references('id')
                  ->on('jobsv') // Reference the 'jobsv' table
                  ->onDelete('cascade'); // If a job is deleted, its details are also deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_details');
    }
};
