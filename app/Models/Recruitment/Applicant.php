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
        'nickname',
        'gender',
        'date_of_birth',
        'age',
        'place_of_birth',
        'race',
        'nationality',
        'religion',
        'national_id',
        'id_card_issued_by',
        'id_card_issued_province',
        'id_card_issued_date',
        'id_card_expiry_date',
        'height_cm',
        'weight_kg',
        'military_status',
        'marital_status',
        'phone',
        'tel',
        'email',
        'line_id',
        'facebook',
        'housing_type',
        'address',
        'house_no',
        'moo',
        'road',
        'subdistrict',
        'district',
        'province',
        'postcode',
        'education_level',
        'university_name',
        'faculty',
        'major',
        'gpa',
        'current_company',
        'current_position',
        'years_of_experience',
        'expected_salary',
        'family_info',
        'emergency_contact',
        'language_skills',
        'experience_summary',
        'computer_skills',
        'special_abilities',
        'application_questions',
        'references_info',
        'health_criminal_questionnaire',
        'attached_documents_check',
        'resume_file',
        'photo_file',
        'portfolio_file',
        'pdpa_consent',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'id_card_issued_date' => 'date',
        'id_card_expiry_date' => 'date',
        'height_cm' => 'decimal:2',
        'weight_kg' => 'decimal:2',
        'family_info' => 'array',
        'emergency_contact' => 'array',
        'language_skills' => 'array',
        'special_abilities' => 'array',
        'application_questions' => 'array',
        'references_info' => 'array',
        'health_criminal_questionnaire' => 'array',
        'attached_documents_check' => 'array',
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
