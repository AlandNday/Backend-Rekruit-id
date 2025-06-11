<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobDetail extends Model
{
    use HasFactory;

    protected $table = 'job_details'; // Specify the table name

    // The primary key is 'job_id' and it's a string
    protected $primaryKey = 'job_id';
    protected $keyType = 'string';
    public $incrementing = false; // It's not an auto-incrementing integer

    protected $fillable = [
        'job_id',
        'description',
        'key_responsibilities',
        'professional_skills',
    ];

    protected $casts = [
        'key_responsibilities' => 'array', // Cast to array for easy handling
        'professional_skills' => 'array',  // Cast to array for easy handling
    ];

    /**
     * Get the job that owns the detail.
     */
    public function job()
    {
        // A JobDetail belongs to a Job.
        // job_id is the foreign key on JobDetail, 'id' is the primary key on Job.
        return $this->belongsTo(Job::class, 'job_id', 'id');
    }
}
