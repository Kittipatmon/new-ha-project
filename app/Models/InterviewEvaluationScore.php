<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterviewEvaluationScore extends Model
{
    protected $table = 'interview_evaluation_scores';

    protected $fillable = [
        'interview_evaluation_id',
        'item_no',
        'topic_title',
        'hr_score',
        'dept_score',
    ];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(InterviewEvaluation::class, 'interview_evaluation_id');
    }
}
