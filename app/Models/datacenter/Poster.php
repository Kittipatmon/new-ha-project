<?php

namespace App\Models\datacenter;

use Illuminate\Database\Eloquent\Model;

class Poster extends Model
{
    protected $table = 'posters';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'title',
        'position', // 'main_carousel', 'side_top', 'side_bottom'
        'image_path',
        'target_type', // 'link', 'file', 'none'
        'link_url',
        'file_path',
        'sort_order',
        'views',
        'clicks',
        'is_active',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'views' => 'integer',
        'clicks' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    /**
     * Scope query to only currently published and scheduled posters.
     */
    public function scopePublished($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')
                  ->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_at')
                  ->orWhere('end_at', '>=', $now);
            });
    }

    /**
     * Check if the poster is currently active and within schedule.
     */
    public function getIsPublishedAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        if ($this->start_at && $this->start_at->isFuture()) {
            return false;
        }

        if ($this->end_at && $this->end_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Relationship with Poster views tracking history
     */
    public function viewLogs()
    {
        return $this->hasMany(PosterView::class, 'poster_id');
    }

    /**
     * Get the resolved destination URL (link_url or asset(file_path))
     */
    public function getActionUrlAttribute()
    {
        if ($this->target_type === 'file' && !empty($this->file_path)) {
            return asset($this->file_path);
        }

        if ($this->target_type === 'link' && !empty($this->link_url)) {
            return $this->link_url;
        }

        return null;
    }
}
