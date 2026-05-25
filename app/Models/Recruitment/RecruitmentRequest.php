<?php

namespace App\Models\Recruitment;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecruitmentRequest extends Model
{
    protected $table = 'recruitment_requests';

    protected $fillable = [
        'request_no',
        'department_id',
        'job_position_id',
        'position_name',
        'requested_by',
        'headcount',
        'reason',
        'job_description',
        'qualification',
        'salary_min',
        'salary_max',
        'required_start_date',
        'status',
        'approved_by_manager',
        'approved_at_manager',
        'approved_by_executive',
        'approved_at_executive',
        'approver_manager_id',
        'approver_executive_id',
        'remarks',
    ];

    protected $casts = [
        'required_start_date' => 'date',
        'approved_at_manager' => 'datetime',
        'approved_at_executive' => 'datetime',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function jobPosition(): BelongsTo
    {
        return $this->belongsTo(JobPosition::class, 'job_position_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function managerApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_manager');
    }

    public function executiveApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_executive');
    }

    public function targetManagerApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_manager_id');
    }

    public function targetExecutiveApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_executive_id');
    }

    public function jobPosts(): HasMany
    {
        return $this->hasMany(JobPost::class, 'recruitment_request_id');
    }
}
