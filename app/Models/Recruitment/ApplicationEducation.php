<?php

namespace App\Models\Recruitment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationEducation extends Model
{
    protected $table = 'recruitment_application_education';

    protected $fillable = [
        'applicant_id',
        'application_id',
        'level',
        'institution_name',
        'faculty',
        'major',
        'start_year',
        'end_year',
        'gpa',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }
}
