<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SystemAuditArchive extends Model
{
    use HasFactory;

    protected $table = 'system_audit_archives';

    protected $fillable = [
        'filename',
        'file_path',
        'file_size',
        'file_size_human',
        'records_count',
        'period_start',
        'period_end',
        'period_label',
        'checksum_sha256',
        'archived_by',
        'archived_by_name',
        'notes',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'records_count' => 'integer',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];

    public function archiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    /**
     * Check if archive file physically exists
     */
    public function fileExists(): bool
    {
        return Storage::disk('local')->exists($this->file_path);
    }

    /**
     * Get absolute path on server
     */
    public function getAbsolutePath(): string
    {
        return Storage::disk('local')->path($this->file_path);
    }

    /**
     * Format date in Thai short format (e.g. 29 ก.ย. 2569)
     */
    public function getThaiDateAttribute(): string
    {
        if (!$this->created_at) return '-';
        $day = $this->created_at->format('j');
        $month = SystemAuditLog::$thaiShortMonths[(int)$this->created_at->format('n')] ?? '';
        $year = (int)$this->created_at->format('Y') + 543;
        return "{$day} {$month} {$year}";
    }

    /**
     * Format time in Thai format (e.g. 11:47 น.)
     */
    public function getThaiTimeAttribute(): string
    {
        if (!$this->created_at) return '-';
        return $this->created_at->format('H:i') . ' น.';
    }

    /**
     * Format date and time in Thai format (e.g. 29 ก.ย. 2569 11:47 น.)
     */
    public function getThaiDatetimeAttribute(): string
    {
        return $this->thai_date . ' ' . $this->thai_time;
    }

    /**
     * Retention date (5 years from archive creation)
     */
    public function getRetainUntilAttribute(): ?\Carbon\Carbon
    {
        return $this->created_at ? $this->created_at->copy()->addYears(5) : null;
    }

    /**
     * Format retain until date in Thai format (e.g. 29 ก.ย. 2574)
     */
    public function getThaiRetainUntilAttribute(): string
    {
        if (!$this->retain_until) return '-';
        $day = $this->retain_until->format('j');
        $month = SystemAuditLog::$thaiShortMonths[(int)$this->retain_until->format('n')] ?? '';
        $year = (int)$this->retain_until->format('Y') + 543;
        return "{$day} {$month} {$year}";
    }

    /**
     * Check if this archive has passed the retention period (default 5 years)
     */
    public function isExpired(int $retentionYears = 5): bool
    {
        if (!$this->created_at) return false;
        return now()->greaterThanOrEqualTo($this->created_at->copy()->addYears($retentionYears));
    }

    /**
     * Human-readable remaining retention time
     */
    public function getRetentionStatusAttribute(): array
    {
        if (!$this->created_at) {
            return ['status' => 'unknown', 'label' => 'ไม่ระบุ', 'badge' => 'bg-slate-100 text-slate-600'];
        }

        $now = now();
        $expireAt = $this->created_at->copy()->addYears(5);

        if ($now->greaterThanOrEqualTo($expireAt)) {
            return [
                'status' => 'expired',
                'label' => 'ครบกำหนด 5 ปีแล้ว',
                'badge' => 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300',
            ];
        }

        $diffDays = $now->diffInDays($expireAt);
        $diffYears = floor($diffDays / 365);
        $diffMonths = floor(($diffDays % 365) / 30);

        $text = $diffYears > 0 
            ? "เหลืออีก {$diffYears} ปี" . ($diffMonths > 0 ? " {$diffMonths} เดือน" : '')
            : "เหลืออีก {$diffDays} วัน";

        return [
            'status' => 'active',
            'label' => $text,
            'badge' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
        ];
    }
}
