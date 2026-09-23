<?php

namespace App\Models\Recruitment;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    protected $table = 'recruitment_applications';

    protected $fillable = [
        'application_no',
        'job_post_id',
        'applicant_id',
        'applied_at',
        'source',
        'cover_letter',
        'status',
        'screening_score',
        'screening_result',
        'screened_by',
        'screened_at',
        'dept_reviewed_by',
        'dept_reviewed_at',
        'final_result',
        'final_result_at',
        'onboarding_date',
        'remarks',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'screened_at' => 'datetime',
        'dept_reviewed_at' => 'datetime',
        'final_result_at' => 'datetime',
        'onboarding_date' => 'date',
    ];

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class, 'job_post_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }

    public function screener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'screened_by');
    }

    public function deptReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dept_reviewed_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicantDocument::class, 'application_id');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class, 'application_id');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(StatusLog::class, 'application_id');
    }

    public function education(): HasMany
    {
        return $this->hasMany(ApplicationEducation::class, 'application_id');
    }

    public function experience(): HasMany
    {
        return $this->hasMany(ApplicationExperience::class, 'application_id');
    }

    /**
     * Get human-readable Thai label for the application status
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'submitted', 'new' => '1. รอคัดกรองเบื้องต้น',
            'screening_failed' => '2. ไม่ผ่านคุณสมบัติ (เก็บข้อมูล)',
            'dept_review' => '3. รอหัวหน้าแผนกพิจารณา',
            'dept_rejected' => '4. หัวหน้าแผนกส่งกลับ (หาคนใหม่)',
            'interview' => '5. รอ HA กำหนดวันนัดสัมภาษณ์',
            'interview_scheduled' => '5. นัดสัมภาษณ์แล้ว',
            'interview_completed' => '6. สัมภาษณ์เสร็จสิ้น / บันทึกผล',
            'interview_failed' => '7. ไม่ผ่านสัมภาษณ์ (หาคนใหม่)',
            'passed_selection', 'selection_approved' => '8. ผ่านการคัดเลือก',
            'offered' => '9. แจ้งผล / ยื่นข้อเสนอ',
            'hired' => '10. รับเข้าทำงาน (บรรจุงาน)',
            'rejected' => 'ไม่ผ่านการพิจารณา',
            default => $this->status,
        };
    }

    /**
     * Get Tailwind CSS badge classes for the status
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'submitted', 'new' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border-amber-200 dark:border-amber-800',
            'screening_failed', 'rejected' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700',
            'dept_review' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 border-blue-200 dark:border-blue-800',
            'dept_rejected', 'interview_failed' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300 border-rose-200 dark:border-rose-800',
            'interview' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border-amber-200 dark:border-amber-800',
            'interview_scheduled' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300 border-purple-200 dark:border-purple-800',
            'interview_completed' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
            'passed_selection', 'selection_approved' => 'bg-teal-100 text-teal-800 dark:bg-teal-900/40 dark:text-teal-300 border-teal-200 dark:border-teal-800',
            'offered' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/40 dark:text-cyan-300 border-cyan-200 dark:border-cyan-800',
            'hired' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-700',
        };
    }

    /**
     * Get the active workflow step (1 to 7)
     */
    public function getWorkflowStepAttribute(): int
    {
        return match ($this->status) {
            'submitted', 'new' => 1,
            'screening_failed' => 1,
            'dept_review', 'dept_rejected' => 2,
            'interview', 'interview_scheduled' => 3,
            'interview_completed', 'interview_failed' => 4,
            'passed_selection', 'selection_approved' => 5,
            'offered' => 6,
            'hired' => 7,
            default => 1,
        };
    }
}
