<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManpowerRequest extends Model
{

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
}
