<?php

namespace App\Models\datacenter;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class HeroBackground extends Model
{
    use Auditable;

    public string $auditModule = 'hero_background';
    public string $auditModuleName = 'ภาพพื้นหลัง Hero Banner';

    public function getAuditTitle(): string
    {
        return "ภาพพื้นหลัง: " . ($this->title ?? '#' . $this->getKey());
    }

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
