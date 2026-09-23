<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InterviewEvaluation extends Model
{
    use SoftDeletes;
    protected $table = 'interview_evaluations';

    protected $fillable = [
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
}
