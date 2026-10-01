<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;
use App\Traits\HasSequentialUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InterviewEvaluation extends Model
{
    use SoftDeletes, Auditable, HasSequentialUuid;

    public string $auditModule = 'interview';
    public string $auditModuleName = 'แบบประเมินสัมภาษณ์';

    public function getAuditTitle(): string
    {
        $job = $this->job_title ? " ({$this->job_title})" : '';
        return "แบบประเมินสัมภาษณ์: " . ($this->applicant_name ?? '#' . $this->getKey()) . $job;
    }

    /**
     * Boot soft deletes conditionally only if the deleted_at column exists in database
     */
    public static function bootSoftDeletes()
    {
        static $hasDeletedAt = null;
        if ($hasDeletedAt === null) {
            try {
                $hasDeletedAt = \Illuminate\Support\Facades\Schema::hasColumn('interview_evaluations', 'deleted_at');
            } catch (\Throwable $e) {
                $hasDeletedAt = false;
            }
        }
        if ($hasDeletedAt) {
            static::addGlobalScope(new \Illuminate\Database\Eloquent\SoftDeletingScope);
        }
    }

    protected $table = 'interview_evaluations';

    protected $fillable = [
        'uuid',
        'user_id',
        'interview_id',
        'application_id',
        'evaluation_date',
        'candidate_prefix',
        'candidate_name',
        'position_applied',
        'department',
        'division',
        'interview_times',
        'total_hr_score',
        'total_dept_score',
        'grand_total_score',
        'average_score',
        'remarks',
        'summary_result',
        'hr_evaluator_name',
        'hr_position',
        'hr_signed_date',
        'dept_evaluator_name',
        'dept_position',
        'dept_signed_date',
        'status',
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'hr_signed_date' => 'date',
        'dept_signed_date' => 'date',
        'average_score' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function interview(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Recruitment\Interview::class, 'interview_id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Recruitment\Application::class, 'application_id');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(InterviewEvaluationScore::class, 'interview_evaluation_id');
    }

    public function getFullCandidateNameAttribute(): string
    {
        return trim(($this->candidate_prefix ? $this->candidate_prefix . ' ' : '') . $this->candidate_name);
    }

    public function getSummaryResultLabelAttribute(): string
    {
        return match ($this->summary_result) {
            'hire' => 'ควรว่าจ้างในตำแหน่งที่สมัคร (30 - 40 คะแนน)',
            'reserve' => 'ควรสำรองไว้กรณีมีการร้องขอพนักงาน (20 - 29 คะแนน)',
            'reject' => 'ปฏิเสธการว่าจ้างเป็นพนักงาน (ต่ำกว่า 20 คะแนน)',
            default => '-',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'ประเมินเสร็จสมบูรณ์',
            'pending_dept' => 'รอต้นสังกัดประเมิน',
            'pending_hr' => 'รอฝ่ายบุคคลประเมิน',
            'draft' => 'ฉบับร่าง',
            default => $this->status ?? 'รอดำเนินการ',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800',
            'pending_dept' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-300 dark:border-amber-800',
            'pending_hr' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-400 border border-blue-300 dark:border-blue-800',
            default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-300 dark:border-slate-700',
        };
    }

    public function isHrEvaluated(): bool
    {
        return !empty($this->hr_signed_date) || ($this->scores()->whereNotNull('hr_score')->count() >= 10);
    }

    public function isDeptEvaluated(): bool
    {
        return !empty($this->dept_signed_date) || ($this->scores()->whereNotNull('dept_score')->count() >= 10);
    }
}
