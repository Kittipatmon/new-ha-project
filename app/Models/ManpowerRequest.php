<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManpowerRequest extends Model
{
    use SoftDeletes;

    /**
     * Boot soft deletes conditionally only if the deleted_at column exists in database
     */
    public static function bootSoftDeletes()
    {
        static $hasDeletedAt = null;
        if ($hasDeletedAt === null) {
            try {
                $hasDeletedAt = \Illuminate\Support\Facades\Schema::hasColumn('manpower_requests', 'deleted_at');
            } catch (\Throwable $e) {
                $hasDeletedAt = false;
            }
        }
        if ($hasDeletedAt) {
            static::addGlobalScope(new \Illuminate\Database\Eloquent\SoftDeletingScope);
        }
    }

    protected $fillable = [
        'user_id', 'date', 'department', 'section', 'job_title_th', 'job_title_en',
        'headcount', 'current_headcount', 'expected_start_date', 'job_level',
        'hire_type', 'hire_replacement_name', 'hire_transfer_name', 'hire_temp_start', 'hire_temp_end',
        'attachment_org_chart', 'attachment_jd', 'attachment_manpower_plan',
        'req_gender', 'req_age', 'req_education', 'req_major', 'req_experience', 'req_special', 'req_other',
        'res_1', 'res_2', 'res_3', 'res_4', 'res_5', 'res_6',
        'requester_name', 'requester_date',
        'manager_name', 'manager_date',
        'vp_name', 'vp_date',
        
        'status',
        'manager_approved_by', 'manager_approved_at',
        'vp_approved_by', 'vp_approved_at',
        'hr_approved_by', 'hr_approved_at',
        'ceo_approved_by', 'ceo_approved_at',
        'rejected_by', 'rejected_at', 'rejection_reason',

        'onboard_employee_code', 'onboard_employee_name', 'onboard_date',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function managerApprover()
    {
        return $this->belongsTo(User::class, 'manager_approved_by');
    }

    public function vpApprover()
    {
        return $this->belongsTo(User::class, 'vp_approved_by');
    }

    public function hrApprover()
    {
        return $this->belongsTo(User::class, 'hr_approved_by');
    }

    public function ceoApprover()
    {
        return $this->belongsTo(User::class, 'ceo_approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Linked RecruitmentRequest in the recruitment subsystem
     */
    public function recruitmentRequest()
    {
        $paddedNo = 'REQ-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
        return $this->hasOne(\App\Models\Recruitment\RecruitmentRequest::class, 'request_no', 'request_no')
            ->orWhere('request_no', $paddedNo);
    }

    /**
     * Find existing JobPost created for this ManpowerRequest
     */
    public function getJobPostAttribute()
    {
        $paddedNo = 'REQ-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
        $req = \App\Models\Recruitment\RecruitmentRequest::where('request_no', $paddedNo)->first();

        if ($req) {
            $post = \App\Models\Recruitment\JobPost::where('recruitment_request_id', $req->id)->latest()->first();
            if ($post) {
                return $post;
            }
        }

        return null;
    }

    /**
     * Check if a Job Post has already been created for this Manpower Request
     */
    public function hasJobPost(): bool
    {
        return !is_null($this->job_post);
    }
}
