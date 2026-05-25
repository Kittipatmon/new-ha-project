<?php

namespace App\Models\Recruitment;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Interview extends Model
{
    protected $table = 'recruitment_interviews';

    protected $fillable = [
        'application_id',
        'interview_round',
        'interview_type',
        'interview_date',
        'interview_time',
        'location',
        'meeting_link',
        'interviewer_id',
        'status',
        'note',
    ];

    protected $casts = [
        'interview_date' => 'date',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function interviewers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        $database = config('database.connections.mysql.database');
        return $this->belongsToMany(User::class, "{$database}.recruitment_interview_interviewer", 'interview_id', 'user_id')->withTimestamps();
    }

    public function scores(): HasMany
    {
        return $this->hasMany(InterviewScore::class, 'interview_id');
    }
}
