<?php

namespace App\Models\datacenter;

use Illuminate\Database\Eloquent\Model;

class HeroBackground extends Model
{
    protected $table = 'hero_backgrounds';

    protected $fillable = [
        'title',
        'image_path',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope query to only active backgrounds.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
