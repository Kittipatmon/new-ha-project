<?php

namespace App\Models\Recruitment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $connection = 'userkml2025';
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

    public function jobPositions(): HasMany
    {
        return $this->hasMany(JobPosition::class, 'department_id', 'department_id');
    }

    public function recruitmentRequests(): HasMany
    {
        return $this->hasMany(RecruitmentRequest::class, 'department_id');
    }

    public function jobPosts(): HasMany
    {
        return $this->hasMany(JobPost::class, 'department_id');
    }
}
