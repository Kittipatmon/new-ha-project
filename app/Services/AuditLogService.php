<?php

namespace App\Services;

use App\Models\SystemAuditArchive;
use App\Models\SystemAuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class AuditLogService
{
    /**
     * Hidden fields that should never be recorded in audit logs
     */
    protected static array $sensitiveFields = [
        'password',
        'password_hash',
        'remember_token',
        'access_token',
        'refresh_token',
        'client_secret',
        'token',
        'api_key',
        'secret',
    ];

    /**
     * Record an audit log entry
     */
    public static function log(
        string $action,
        string $description,
        ?Model $model = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $module = null,
        ?string $moduleName = null,
        ?User $user = null
    ): SystemAuditLog {
        $actor = $user ?: Auth::user();

        // Infer module if not provided
        if (!$module && $model) {
            $class = get_class($model);
            if (str_contains($class, 'Recruitment')) {
                $module = 'recruitment';
                $moduleName = $moduleName ?: 'ระบบสรรหาบุคลากร';
            } elseif (str_contains($class, 'Training')) {
                $module = 'training';
                $moduleName = $moduleName ?: 'ระบบฝึกอบรม';
            } elseif (str_contains($class, 'User') || str_contains($class, 'Employee')) {
                $module = 'users';
                $moduleName = $moduleName ?: 'จัดการผู้ใช้งานและพนักงาน';
            } else {
                $module = 'general';
                $moduleName = $moduleName ?: 'ระบบทั่วไป';
            }
        }

        // Clean values
        $cleanedOld = $oldValues ? self::filterSensitiveFields($oldValues) : null;
        $cleanedNew = $newValues ? self::filterSensitiveFields($newValues) : null;

        // Compute diff for updates
        $diff = null;
        if ($cleanedOld && $cleanedNew) {
            $diff = self::computeDiff($cleanedOld, $cleanedNew);
        }

        return SystemAuditLog::create([
            'user_id' => $actor?->id,
            'user_code' => $actor?->emp_code ?? $actor?->user_code,
            'user_name' => $actor?->fullname ?? $actor?->name ?? 'ระบบอัตโนมัติ (System)',
            'user_email' => $actor?->email,
            'user_role' => $actor?->role ?? ($actor?->is_admin ? 'Admin' : 'User'),
            'action' => $action,
            'module' => $module ?: 'system',
            'module_name' => $moduleName ?: 'ระบบจัดการ',
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? (string)$model->getKey() : null,
            'description' => $description,
            'old_values' => $cleanedOld,
            'new_values' => $cleanedNew,
            'diff' => $diff,
            'ip_address' => Request::ip() ?: '127.0.0.1',
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
            'method' => Request::method() ?: 'CLI',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Compute field-level differences between old and new
     */
    public static function computeDiff(array $old, array $new): array
    {
        $diff = [];
        $allKeys = array_unique(array_merge(array_keys($old), array_keys($new)));

        // Ignore common metadata timestamps in diff comparison
        $ignoredKeys = ['updated_at', 'created_at', 'remember_token'];

        foreach ($allKeys as $key) {
            if (in_array($key, $ignoredKeys, true)) {
                continue;
            }

            $oldVal = $old[$key] ?? null;
            $newVal = $new[$key] ?? null;

            if ($oldVal !== $newVal) {
                // If either is array, convert to json for clean display
                $displayOld = is_array($oldVal) ? json_encode($oldVal, JSON_UNESCAPED_UNICODE) : (string)$oldVal;
                $displayNew = is_array($newVal) ? json_encode($newVal, JSON_UNESCAPED_UNICODE) : (string)$newVal;

                $diff[$key] = [
                    'old' => $displayOld,
                    'new' => $displayNew,
                ];
            }
        }

        return $diff;
    }

    /**
     * Filter out passwords or sensitive keys
     */
    protected static function filterSensitiveFields(array $data): array
    {
        foreach ($data as $key => $value) {
            $lowerKey = strtolower($key);
            foreach (self::$sensitiveFields as $sensitive) {
                if (str_contains($lowerKey, $sensitive)) {
                    $data[$key] = '******** (PROTECTED)';
                    break;
                }
            }
        }
        return $data;
    }

    /**
     * Create a Zip Archive for a given year of logs
     */
    public static function createArchive(int $year, ?User $actor = null, string $notes = ''): SystemAuditArchive
    {
        $startDate = "{$year}-01-01 00:00:00";
        $endDate = "{$year}-12-31 23:59:59";

        $logs = SystemAuditLog::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('id', 'asc')
            ->get();

        $count = $logs->count();
        $beYear = $year + 543;
        $periodLabel = "ปี {$beYear} (ค.ศ. {$year})";
        $timestamp = date('Ymd_His');
        $filename = "audit_logs_{$year}_{$timestamp}.zip";
        $relativeDir = 'audit_archives';
        $relativePath = "{$relativeDir}/{$filename}";

        // Ensure directory exists
        Storage::disk('local')->makeDirectory($relativeDir);
        $absolutePath = Storage::disk('local')->path($relativePath);

        // Create Zip file
        $zip = new ZipArchive();
        if ($zip->open($absolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("ไม่สามารถสร้างไฟล์ ZIP ที่: {$absolutePath}");
        }

        // 1. Add complete JSON logs
        $jsonData = json_encode([
            'archive_info' => [
                'year' => $year,
                'year_be' => $beYear,
                'period_label' => $periodLabel,
                'exported_at' => now()->toIso8601String(),
                'records_count' => $count,
                'system' => config('app.name', 'Kumwell HR System'),
            ],
            'logs' => $logs->toArray(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $zip->addFromString("audit_logs_{$year}.json", $jsonData);

        // 2. Add CSV summary for easy Excel viewing
        $csvHandle = fopen('php://memory', 'r+');
        // Add UTF-8 BOM for Excel
        fputs($csvHandle, "\xEF\xBB\xBF");
        fputcsv($csvHandle, [
            'ID',
            'วันเวลา',
            'รหัสพนักงาน',
            'ผู้ดำเนินการ',
            'กิจกรรม (Action)',
            'ระบบ/โมดูล',
            'คำอธิบาย',
            'IP Address',
            'Method',
            'URL',
        ]);

        foreach ($logs as $log) {
            fputcsv($csvHandle, [
                $log->id,
                $log->created_at->format('d/m/Y H:i:s'),
                $log->user_code ?: '-',
                $log->user_name ?: '-',
                $log->getActionLabel(),
                $log->module_name ?: $log->module,
                $log->description,
                $log->ip_address ?: '-',
                $log->method ?: '-',
                $log->url ?: '-',
            ]);
        }
        rewind($csvHandle);
        $csvData = stream_get_contents($csvHandle);
        fclose($csvHandle);
        $zip->addFromString("audit_logs_{$year}_summary.csv", $csvData);

        // 3. Add Integrity manifest
        $manifest = [
            'period' => $periodLabel,
            'records_count' => $count,
            'created_at' => now()->toIso8601String(),
            'created_by' => $actor?->fullname ?? 'Admin',
            'notes' => $notes ?: 'บันทึก Audit Log เก็บรักษาตามนโยบาย 5 ปีสำหรับตรวจสอบ',
            'policy' => 'ISO/IEC 27001 & Data Retention Policy 5 Years',
        ];
        $zip->addFromString('manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $zip->close();

        // Calculate file stats
        $fileSize = filesize($absolutePath);
        $fileSizeHuman = self::formatBytes($fileSize);
        $checksum = hash_file('sha256', $absolutePath);

        return SystemAuditArchive::create([
            'filename' => $filename,
            'file_path' => $relativePath,
            'file_size' => $fileSize,
            'file_size_human' => $fileSizeHuman,
            'records_count' => $count,
            'period_start' => $startDate,
            'period_end' => $endDate,
            'period_label' => $periodLabel,
            'checksum_sha256' => $checksum,
            'archived_by' => $actor?->id,
            'archived_by_name' => $actor?->fullname ?? 'ผู้ดูแลระบบ',
            'notes' => $notes,
        ]);
    }

    /**
     * Read and parse logs from inside an archived ZIP file for auditor inspection
     */
    public static function readArchiveLogs(SystemAuditArchive $archive): array
    {
        $path = $archive->getAbsolutePath();
        if (!file_exists($path)) {
            return [];
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return [];
        }

        $content = null;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (str_ends_with($name, '.json') && str_starts_with($name, 'audit_logs_')) {
                $content = $zip->getFromIndex($i);
                break;
            }
        }
        $zip->close();

        if (!$content) {
            return [];
        }

        $decoded = json_decode($content, true);
        return $decoded['logs'] ?? [];
    }

    /**
     * Format bytes to human readable string
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
