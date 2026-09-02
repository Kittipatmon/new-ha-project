<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProbationSignature extends Model
{
    protected $fillable = [
        'probation_evaluation_id', 'role', 'name', 'signed_at'
    ];

    public function probationEvaluation()
    {
        return $this->belongsTo(ProbationEvaluation::class);
    }
}
