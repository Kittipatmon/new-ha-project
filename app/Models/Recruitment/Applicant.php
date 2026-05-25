<?php

namespace App\Models\Recruitment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Applicant extends Model
{
    protected $table = 'recruitment_applicants';

    protected $fillable = [
        'prefix',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'national_id',
        'phone',
        'email',
        'line_id',
        'address',
        'province',
        'education_level',
        'university_name',
        'faculty',
        'major',
        'gpa',
        'current_company',
        'current_position',
        'years_of_experience',
        'expected_salary',
        'resume_file',
        'photo_file',
        'portfolio_file',
        'pdpa_consent',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'pdpa_consent' => 'boolean',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'applicant_id');
    }

    public function education(): HasMany
    {
        return $this->hasMany(ApplicationEducation::class, 'applicant_id');
    }

    public function experience(): HasMany
    {
        return $this->hasMany(ApplicationExperience::class, 'applicant_id');
    }

    public function educationEntries(): HasMany
    {
        return $this->education();
    }

    public function experienceEntries(): HasMany
    {
        return $this->experience();
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->prefix} {$this->first_name} {$this->last_name}";
    }
}
