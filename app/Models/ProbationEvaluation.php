<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProbationEvaluation extends Model
{

    protected $fillable = [
        'user_id', 'prefix', 'employee_name', 'position', 'emp_code', 'department', 
        'start_date', 'probation_due_date', 'status',
        // Round 1
        'tasks_assigned', 'performance_result', 'performance_level', 
        'evaluation_result', 'evaluation_result_reason', 'evaluator_comment', 'hr_comment',
        // Round 2
        'tasks_assigned_2', 'performance_result_2', 'performance_level_2', 
        'evaluation_result_2', 'evaluation_result_reason_2', 'evaluator_comment_2', 'hr_comment_2',
        // Round 3
        'tasks_assigned_3', 'performance_result_3', 'performance_level_3', 
        'evaluation_result_3', 'evaluation_result_reason_3', 'evaluator_comment_3', 'hr_comment_3'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function absenceRecords()
    {
        return $this->hasMany(ProbationAbsenceRecord::class);
    }

    public function examResults()
    {
        return $this->hasMany(ProbationExamResult::class);
    }

    public function signatures()
    {
        return $this->hasMany(ProbationSignature::class);
    }

    public function getEvaluateeNameAttribute() {
        return $this->signatures()->where('role', 'evaluatee')->value('name');
    }

    public function getEvaluatorNameAttribute() {
        return $this->signatures()->where('role', 'evaluator')->value('name');
    }

    public function getManagerNameAttribute() {
        return $this->signatures()->where('role', 'manager')->value('name');
    }

    public function getHrNameAttribute() {
        return $this->signatures()->where('role', 'hr')->value('name');
    }

    public function getExamOtherTopicAttribute() {
        $exam7 = $this->examResults()->skip(6)->first();
        if ($exam7) {
            return trim(str_replace('7. อื่นๆ', '', $exam7->topic));
        }
        return '';
    }
}
