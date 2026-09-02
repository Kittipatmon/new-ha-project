<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProbationExamResult extends Model
{
    protected $fillable = [
        'probation_evaluation_id', 'topic', 'passed_round', 'exam_date', 'exam_tester'
    ];

    public function probationEvaluation()
    {
        return $this->belongsTo(ProbationEvaluation::class);
    }
}
