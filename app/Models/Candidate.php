<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Candidate extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    // Define the column name for soft deletes, if it's not 'deleted_at'
    protected $dates = ['deleted_at'];

    // Accessor for applicationDate attribute
    public function getApplicationDateAttribute($value)
    {
        // Convert the attribute to a Carbon instance and format it
        return Carbon::parse($value)->format('d/m/Y');
    }

    public function degree()
    {
        return $this->belongsTo(Degree::class);
    }

    public function jobApplications()
    {
        return $this->hasMany(JobAppliedFor::class);
    }
}
