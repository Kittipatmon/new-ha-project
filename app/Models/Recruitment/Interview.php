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

    public function interviewers(): \App\Models\Recruitment\Relations\CrossConnectionBelongsToMany
    {
        return new \App\Models\Recruitment\Relations\CrossConnectionBelongsToMany(
            User::query(),
            $this,
            'recruitment_interview_interviewer',
            'interview_id',
            'user_id',
            $this->getConnectionName() ?: 'mysql'
        );
    }

    public function scores(): HasMany
    {
        return $this->hasMany(InterviewScore::class, 'interview_id');
    }

    public function evaluation(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\InterviewEvaluation::class, 'interview_id');
    }
}
