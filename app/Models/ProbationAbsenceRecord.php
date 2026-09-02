<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProbationAbsenceRecord extends Model
{
    protected $fillable = [
        'probation_evaluation_id', 'round', 'start_date', 'end_date',
        'business_leave', 'sick_leave', 'absent', 'late_count', 'late_mins'
    ];

    public function probationEvaluation()
    {
        return $this->belongsTo(ProbationEvaluation::class);
    }
}
