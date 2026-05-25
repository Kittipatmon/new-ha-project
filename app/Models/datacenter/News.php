<?php

namespace App\Models\datacenter;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $table = 'news';
    protected $primaryKey = 'news_id';
    public $timestamps = true;

    protected $fillable = [
        'newto',
        'title',
        'content',
        'link_news',
        'file_news',
        'published_date',
        'is_active',
        'image_path',
    ];

    protected $casts = [
        'published_date' => 'date',
        'is_active' => 'boolean',
        'image_path' => 'array',
        'file_news' => 'array',
    ];

}
