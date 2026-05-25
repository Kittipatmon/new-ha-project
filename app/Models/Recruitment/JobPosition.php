<?php

namespace App\Models\Recruitment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPosition extends Model
{
    protected $table = 'recruitment_job_positions';

    protected $fillable = [
        'department_id',
        'position_code',
        'position_name',
        'position_level',
        'employment_type',
        'description',
        'qualification',
        'salary_min',
        'salary_max',
        'status',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function recruitmentRequests(): HasMany
    {
        return $this->hasMany(RecruitmentRequest::class, 'job_position_id');
    }

    public function jobPosts(): HasMany
    {
        return $this->hasMany(JobPost::class, 'job_position_id');
    }
}
