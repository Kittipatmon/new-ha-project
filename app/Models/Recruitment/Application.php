<?php

namespace App\Models\Recruitment;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    protected $table = 'recruitment_applications';

    protected $fillable = [
        'application_no',
        'job_post_id',
        'applicant_id',
        'applied_at',
        'source',
        'cover_letter',
        'status',
        'screening_score',
        'screening_result',
        'screened_by',
        'screened_at',
        'final_result',
        'final_result_at',
        'remarks',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
        'screened_at' => 'datetime',
        'final_result_at' => 'datetime',
    ];

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class, 'job_post_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }

    public function screener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'screened_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicantDocument::class, 'application_id');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class, 'application_id');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(StatusLog::class, 'application_id');
    }

    public function education(): HasMany
    {
        return $this->hasMany(ApplicationEducation::class, 'application_id');
    }

    public function experience(): HasMany
    {
        return $this->hasMany(ApplicationExperience::class, 'application_id');
    }
}
