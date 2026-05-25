<?php

namespace App\Models\Recruitment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationExperience extends Model
{
    protected $table = 'recruitment_application_experiences';

    protected $fillable = [
        'applicant_id',
        'application_id',
        'company_name',
        'position',
        'start_date',
        'end_date',
        'job_detail',
        'salary',
        'reason_for_leaving',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }
}
