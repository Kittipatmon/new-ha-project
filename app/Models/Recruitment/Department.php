<?php

namespace App\Models\Recruitment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Division;
use App\Models\Section;

class Department extends Model
{
    protected $connection = 'mysql';
    protected $table = 'department';
    protected $primaryKey = 'department_id';

    protected $fillable = [
        'division_id',
        'section_id',
        'department_name',
        'department_fullname',
        'department_status',
        'department_description',
    ];

    public function getNameAttribute(): string
    {
        return (string) ($this->department_name ?: ($this->department_fullname ?: ''));
    }

    public function jobPositions(): HasMany
    {
        return $this->hasMany(JobPosition::class, 'department_id', 'department_id');
    }

    public function recruitmentRequests(): HasMany
    {
        return $this->hasMany(RecruitmentRequest::class, 'department_id');
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id', 'division_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id', 'section_id');
    }

    public function jobPosts(): HasMany
    {
        return $this->hasMany(JobPost::class, 'department_id');
    }
}
