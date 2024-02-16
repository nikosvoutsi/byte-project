<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAppliedFor extends Model
{
    use HasFactory;

    protected $table = 'job_applied_for';

    protected $guarded = [];

    // Define the relationship with the Candidate model
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    // Define the relationship with the Job model
    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
