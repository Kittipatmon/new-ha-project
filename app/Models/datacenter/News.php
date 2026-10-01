<?php

namespace App\Models\datacenter;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use Auditable;

    public string $auditModule = 'news';
    public string $auditModuleName = 'ข่าวสารและกิจกรรม';

    public function getAuditTitle(): string
    {
        return "ข่าวสาร: " . ($this->title ?? '#' . $this->getKey());
    }

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
        'views',
        'clicks',
    ];

    protected $casts = [
        'published_date' => 'date',
        'is_active' => 'boolean',
        'image_path' => 'array',
        'file_news' => 'array',
        'views' => 'integer',
        'clicks' => 'integer',
    ];

    public function viewLogs()
    {
        return $this->hasMany(NewsView::class, 'news_id', 'news_id');
    }
}
