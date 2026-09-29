<?php

namespace App\Models\Recruitment;

use App\Models\User;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use Auditable;

    public string $auditModule = 'recruitment';
    public string $auditModuleName = 'ระบบสรรหาบุคลากร (ใบสมัคร)';

    public function getAuditTitle(): string
    {
        return "ใบสมัครงาน: {$this->application_no}";
    }

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
     * Get Tailwind CSS badge classes for the status (vibrant pill style matching Image 2)
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'submitted', 'new' => 'bg-[#fffbeb] text-[#b45309] border-[#fde68a] dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
            'screening_failed', 'interview_failed', 'rejected' => 'bg-[#fff1f2] text-[#be123c] border-[#fecdd3] dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800',
            'dept_review' => 'bg-[#eff6ff] text-[#1d4ed8] border-[#bfdbfe] dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800',
            'dept_rejected' => 'bg-[#fff7ed] text-[#c2410c] border-[#fed7aa] dark:bg-orange-950/60 dark:text-orange-300 dark:border-orange-800',
            'interview', 'interview_scheduled' => 'bg-[#faf5ff] text-[#7e22ce] border-[#e9d5ff] dark:bg-purple-950/60 dark:text-purple-300 dark:border-purple-800',
            'interview_completed' => 'bg-[#eef2ff] text-[#4338ca] border-[#c7d2fe] dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800',
            'passed_selection', 'selection_approved' => 'bg-[#ecfdf5] text-[#047857] border-[#a7f3d0] dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
            'offered' => 'bg-[#f0fdfa] text-[#0f766e] border-[#99f6e4] dark:bg-teal-950/60 dark:text-teal-300 dark:border-teal-800',
            'hired' => 'bg-[#ecfdf5] text-[#047857] border-[#a7f3d0] dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
            default => 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        };
    }

    /**
     * Get FontAwesome icon class for the status
     */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'submitted', 'new' => 'fa-regular fa-clock text-[#d97706]',
            'dept_review' => 'fa-solid fa-user-clock text-[#2563eb]',
            'dept_rejected' => 'fa-solid fa-arrow-rotate-left text-[#ea580c]',
            'interview', 'interview_scheduled' => 'fa-regular fa-calendar-check text-[#9333ea]',
            'interview_completed' => 'fa-solid fa-clipboard-check text-[#4f46e5]',
            'passed_selection', 'selection_approved' => 'fa-solid fa-circle-check text-[#059669]',
            'offered' => 'fa-solid fa-file-signature text-[#0d9488]',
            'hired' => 'fa-solid fa-circle-check text-[#059669]',
            'screening_failed', 'interview_failed', 'rejected' => 'fa-solid fa-circle-xmark text-[#e11d48]',
            default => 'fa-solid fa-circle-info text-slate-500',
        };
    }

    /**
     * Get the active workflow step (1 to 8)
     */
    public function getWorkflowStepAttribute(): int
    {
        return match ($this->status) {
            'submitted', 'new', 'screening_failed' => 1,
            'dept_review', 'dept_rejected' => 2,
            'interview' => 3,
            'interview_scheduled' => 4,
            'interview_completed', 'interview_failed' => 5,
            'passed_selection', 'selection_approved' => 6,
            'offered' => 7,
            'hired' => 8,
            default => 1,
        };
    }
}
