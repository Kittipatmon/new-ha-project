<?php

namespace App\Models\Recruitment;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPost extends Model
{
    protected $table = 'recruitment_job_posts';

    protected $fillable = [
        'recruitment_request_id',
        'job_position_id',
        'position_name',
        'title',
        'slug',
        'department_id',
        'vacancy',
        'employment_type',
        'location',
        'work_schedule',
        'salary_min',
        'salary_max',
        'salary_note',
        'job_description',
        'qualification',
        'benefits',
        'required_documents',
        'start_date',
        'end_date',
        'publish_status',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function recruitmentRequest(): BelongsTo
    {
        return $this->belongsTo(RecruitmentRequest::class, 'recruitment_request_id');
    }

    public function jobPosition(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class, 'job_position_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'job_post_id');
    }
}
