<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Query\Expression; // <-- ADD THIS LINE

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jobsv', function (Blueprint $table) {
            $table->string('id')->primary(); // Use string for ID as per your model
            $table->string('title');
            $table->string('company');
            $table->string('location');
            $table->string('salary');
            $table->string('posted_time'); // Store as string (e.g., "10 minutes ago")
            $table->string('job_type');
            $table->string('category')->default('Other');
            $table->string('experience_level')->default('Entry Level');
            // FIX: Use an SQL expression for the default JSON array
            $table->json('tags')->default(new Expression('(JSON_ARRAY())')); // Store tags as JSON
            $table->string('company_initial')->nullable();
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobsv');
    }
};