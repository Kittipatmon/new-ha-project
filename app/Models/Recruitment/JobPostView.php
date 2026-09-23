<?php

namespace App\Models\Recruitment;

use Illuminate\Database\Eloquent\Model;

class JobPostView extends Model
{
    protected $table = 'recruitment_job_post_views';
    public $timestamps = true;

    protected $fillable = [
        'job_post_id',
        'event_type', // 'view' or 'click'
        'user_id',
        'ip_address',
        'user_agent',
        'view_date',
    ];

    protected $casts = [
        'view_date' => 'date:Y-m-d',
    ];

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class, 'job_post_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
