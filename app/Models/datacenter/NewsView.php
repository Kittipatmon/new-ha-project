<?php

namespace App\Models\datacenter;

use Illuminate\Database\Eloquent\Model;

class NewsView extends Model
{
    protected $table = 'news_views';
    public $timestamps = true;

    protected $fillable = [
        'news_id',
        'event_type', // 'view' or 'click'
        'user_id',
        'ip_address',
        'user_agent',
        'view_date',
    ];

    protected $casts = [
        'view_date' => 'date:Y-m-d',
    ];

    public function news()
    {
        return $this->belongsTo(News::class, 'news_id', 'news_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
