<?php

namespace App\Models\Recruitment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewScore extends Model
{
    protected $table = 'recruitment_interview_scores';

    protected $fillable = [
        'interview_id',
        'criteria_name',
        'score',
        'max_score',
        'comment',
    ];

    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class, 'interview_id');
    }
}
