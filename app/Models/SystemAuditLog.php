<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemAuditLog extends Model
{
    use HasFactory;

    protected $table = 'system_audit_logs';

    protected $fillable = [
        'user_id',
        'user_code',
        'user_name',
        'user_email',
        'user_role',
        'action',
        'module',
        'module_name',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'diff',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'diff' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship to the user who performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get action label in Thai
     */
    public function getActionLabel(): string
    {
        return match ($this->action) {
            'created' => 'เพิ่มข้อมูล (Create)',
            'updated' => 'แก้ไขข้อมูล (Update)',
            'renewed' => 'ต่ออายุ (Renew)',
            'deleted' => 'ลบข้อมูล (Delete)',
            'login' => 'เข้าสู่ระบบ (Login)',
            'logout' => 'ออกจากระบบ (Logout)',
            'archived' => 'บีบอัดไฟล์ (Archive)',
            'restored' => 'กู้คืนข้อมูล (Restore)',
            'exported' => 'ส่งออกข้อมูล (Export)',
            default => ucfirst($this->action),
        };
    }

    /**
     * Get badge styling class for action
     */
    public function getActionBadgeClass(): string
    {
        return match ($this->action) {
            'created' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800',
            'updated' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800',
            'renewed' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-400 dark:border-indigo-800',
            'deleted' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800',
            'login', 'logout' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-800',
            'archived' => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-400 dark:border-purple-800',
            default => 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        };
    }

    /**
     * Get icon for action
     */
    public function getActionIcon(): string
    {
        return match ($this->action) {
            'created' => 'fa-solid fa-plus-circle',
            'updated' => 'fa-solid fa-pen-to-square',
            'renewed' => 'fa-solid fa-arrows-rotate',
            'deleted' => 'fa-solid fa-trash-can',
            'login' => 'fa-solid fa-right-to-bracket',
            'logout' => 'fa-solid fa-right-from-bracket',
            'archived' => 'fa-solid fa-file-zipper',
            'restored' => 'fa-solid fa-rotate-left',
            'exported' => 'fa-solid fa-file-export',
            default => 'fa-solid fa-circle-dot',
        };
    }

    /**
     * Check if this log has comparison diff
     */
    public function hasDiff(): bool
    {
        return !empty($this->diff) || !empty($this->old_values) || !empty($this->new_values);
    }

    /**
     * Thai month abbreviations
     */
    public static array $thaiShortMonths = [
        1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
        5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
        9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
    ];

    /**
     * Thai full month names
     */
    public static array $thaiFullMonths = [
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
        5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
        9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
    ];

    /**
     * Format date in Thai short format (e.g. 29 ก.ย. 2569)
     */
    public function getThaiDateAttribute(): string
    {
        if (!$this->created_at) return '-';
        $day = $this->created_at->format('j');
        $month = self::$thaiShortMonths[(int)$this->created_at->format('n')] ?? '';
        $year = (int)$this->created_at->format('Y') + 543;
        return "{$day} {$month} {$year}";
    }

    /**
     * Format time in Thai format (e.g. 11:42:09 น.)
     */
    public function getThaiTimeAttribute(): string
    {
        if (!$this->created_at) return '-';
        return $this->created_at->format('H:i:s') . ' น.';
    }

    /**
     * Format date and time in Thai format (e.g. 29 ก.ย. 2569 11:42:09 น.)
     */
    public function getThaiDatetimeAttribute(): string
    {
        return $this->thai_date . ' ' . $this->thai_time;
    }

    /**
     * Format full Thai date time (e.g. 29 กันยายน 2569 เวลา 11:42:09 น.)
     */
    public function getThaiFullDatetimeAttribute(): string
    {
        if (!$this->created_at) return '-';
        $day = $this->created_at->format('j');
        $month = self::$thaiFullMonths[(int)$this->created_at->format('n')] ?? '';
        $year = (int)$this->created_at->format('Y') + 543;
        $time = $this->created_at->format('H:i:s');
        return "{$day} {$month} {$year} เวลา {$time} น.";
    }
}
