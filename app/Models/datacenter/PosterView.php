<?php

namespace App\Models\datacenter;

use Illuminate\Database\Eloquent\Model;

class PosterView extends Model
{
    protected $table = 'poster_views';
    public $timestamps = true;

    protected $fillable = [
        'poster_id',
        'event_type', // 'view' or 'click'
        'user_id',
        'ip_address',
        'user_agent',
        'view_date',
    ];

    protected $casts = [
        'view_date' => 'date:Y-m-d',
    ];

    public function poster()
    {
        return $this->belongsTo(Poster::class, 'poster_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
