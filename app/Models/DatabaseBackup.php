<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Model
{
    use HasFactory;

    protected $table = 'database_backups';

    protected $fillable = [
        'filename',
        'file_path',
        'file_size',
        'file_size_human',
        'tables_count',
        'rows_count',
        'checksum_sha256',
        'dumper_engine',
        'created_by',
        'created_by_name',
        'notes',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'tables_count' => 'integer',
        'rows_count' => 'integer',
    ];

    public static array $thaiShortMonths = [
        1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
        5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
        9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if backup file physically exists on private storage
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
     * Format date in Thai short format (e.g. 30 ก.ย. 2569)
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
     * Format time in Thai format (e.g. 00:00 น.)
     */
    public function getThaiTimeAttribute(): string
    {
        if (!$this->created_at) return '-';
        return $this->created_at->format('H:i') . ' น.';
    }

    /**
     * Format date and time in Thai format (e.g. 30 ก.ย. 2569 00:00 น.)
     */
    public function getThaiDatetimeAttribute(): string
    {
        return $this->thai_date . ' ' . $this->thai_time;
    }
}
