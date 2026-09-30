<?php

namespace App\Services;

use App\Models\DatabaseBackup;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDO;
use ZipArchive;

class DatabaseBackupService
{
    /**
     * Directory inside Storage::disk('local') for database backups
     */
    const BACKUP_DIR = 'backups/db';

    /**
     * Create a compressed database backup (.zip) with SHA-256 verification
     *
     * @param User|null $actor
     * @param string $notes
     * @return DatabaseBackup
     */
    public static function createBackup(?User $actor = null, string $notes = ''): DatabaseBackup
    {
        // 1. Ensure private directory exists
        Storage::disk('local')->makeDirectory(self::BACKUP_DIR);

        $dbConnection = config('database.default', 'mysql');
        $dbConfig = config("database.connections.{$dbConnection}", []);
        $dbName = $dbConfig['database'] ?? 'database';
        $timestamp = date('Ymd_His');
        $zipFilename = "backup_db_{$dbName}_{$timestamp}.zip";
        $zipRelativePath = self::BACKUP_DIR . '/' . $zipFilename;
        $zipAbsolutePath = Storage::disk('local')->path($zipRelativePath);

        $tempSqlRelative = self::BACKUP_DIR . "/temp_{$dbName}_{$timestamp}.sql";
        $tempSqlAbsolute = Storage::disk('local')->path($tempSqlRelative);

        $dumperEngine = 'pdo_native';
        $tablesCount = 0;
        $totalRowsCount = 0;

        try {
            // 2. Perform database dump to temporary SQL file
            $dumpResult = self::dumpDatabaseToSql($tempSqlAbsolute, $dbConfig);
            $tablesCount = $dumpResult['tables_count'];
            $totalRowsCount = $dumpResult['rows_count'];
            $dumperEngine = $dumpResult['engine'];

            // 3. Compress SQL file into ZIP
            $zip = new ZipArchive();
            if ($zip->open($zipAbsolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException("ไม่สามารถสร้างไฟล์ ZIP ที่: {$zipAbsolutePath}");
            }

            // Add the main SQL dump file inside ZIP
            $internalSqlName = "database_{$dbName}_{$timestamp}.sql";
            $zip->addFile($tempSqlAbsolute, $internalSqlName);

            // Add Manifest JSON inside ZIP for integrity & audit
            $manifest = [
                'system' => config('app.name', 'Kumwell HR System'),
                'database' => $dbName,
                'backup_at' => now()->toIso8601String(),
                'tables_count' => $tablesCount,
                'rows_count' => $totalRowsCount,
                'dumper_engine' => $dumperEngine,
                'created_by' => $actor?->fullname ?? 'ระบบอัตโนมัติ (Daily Midnight Cron)',
                'notes' => $notes ?: 'สำรองฐานข้อมูลอัตโนมัติประจำวัน',
                'schedule' => 'รันทุกวัน เวลา 00:00 น. (Daily Midnight Backup)',
                'retention_policy' => 'เก็บสำรองย้อนหลัง 30 วัน',
            ];
            $zip->addFromString('manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $zip->close();

            // 4. Clean up temporary uncompressed SQL file
            if (file_exists($tempSqlAbsolute)) {
                @unlink($tempSqlAbsolute);
            }

            // 5. Verify ZIP integrity
            if (!file_exists($zipAbsolutePath) || filesize($zipAbsolutePath) === 0) {
                throw new \RuntimeException("ไฟล์ ZIP ที่สร้างขึ้นไม่สมบูรณ์หรือว่างเปล่า");
            }

            $fileSize = filesize($zipAbsolutePath);
            $fileSizeHuman = self::formatBytes($fileSize);
            $checksum = hash_file('sha256', $zipAbsolutePath);

            // 6. Record to database ledger
            $backup = DatabaseBackup::create([
                'filename' => $zipFilename,
                'file_path' => $zipRelativePath,
                'file_size' => $fileSize,
                'file_size_human' => $fileSizeHuman,
                'tables_count' => $tablesCount,
                'rows_count' => $totalRowsCount,
                'checksum_sha256' => $checksum,
                'dumper_engine' => $dumperEngine,
                'created_by' => $actor?->id,
                'created_by_name' => $actor?->fullname ?? ($actor ? 'ผู้ดูแลระบบ' : 'ระบบอัตโนมัติ (Daily Midnight Cron)'),
                'notes' => $notes ?: ($actor ? 'สั่งสำรองข้อมูลด้วยตนเอง' : 'สำรองฐานข้อมูลอัตโนมัติประจำวัน (เที่ยงคืน)'),
            ]);

            // 7. Log in Audit Trail
            if (class_exists(AuditLogService::class)) {
                AuditLogService::log(
                    action: 'backup',
                    description: "สำรองฐานข้อมูลสำเร็จ: {$zipFilename} ขนาด {$fileSizeHuman} ({$tablesCount} ตาราง, " . number_format($totalRowsCount) . " รายการ)",
                    model: $backup,
                    module: 'system',
                    moduleName: 'ระบบสำรองฐานข้อมูลอัตโนมัติ',
                    user: $actor
                );
            }

            return $backup;

        } catch (\Throwable $e) {
            // Clean up temporary files on error
            if (file_exists($tempSqlAbsolute)) {
                @unlink($tempSqlAbsolute);
            }
            if (file_exists($zipAbsolutePath)) {
                @unlink($zipAbsolutePath);
            }
            throw $e;
        }
    }

    /**
     * Dump database using PDO natively (resilient on all OS & hosting environments)
     */
    protected static function dumpDatabaseToSql(string $outputFile, array $dbConfig): array
    {
        $handle = fopen($outputFile, 'w+');
        if (!$handle) {
            throw new \RuntimeException("ไม่สามารถเปิดไฟล์เพื่อเขียนข้อมูล: {$outputFile}");
        }

        $dbName = $dbConfig['database'] ?? 'laravel';
        $now = now()->toDateTimeString();

        // Write SQL Header
        fwrite($handle, "-- ==========================================================\n");
        fwrite($handle, "-- Kumwell HR System - Database Backup Dump\n");
        fwrite($handle, "-- Generated: {$now}\n");
        fwrite($handle, "-- Database: `{$dbName}`\n");
        fwrite($handle, "-- ==========================================================\n\n");

        fwrite($handle, "SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT;\n");
        fwrite($handle, "SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS;\n");
        fwrite($handle, "SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION;\n");
        fwrite($handle, "SET NAMES utf8mb4;\n");
        fwrite($handle, "SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n");

        $pdo = DB::connection()->getPdo();

        // 1. Fetch all base tables
        $tablesStmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        $tables = [];
        while ($row = $tablesStmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $totalRows = 0;

        foreach ($tables as $table) {
            fwrite($handle, "\n-- --------------------------------------------------------\n");
            fwrite($handle, "-- Table structure for table `{$table}`\n");
            fwrite($handle, "-- --------------------------------------------------------\n\n");

            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");

            // Get Create Table statement
            $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
            $createRow = $createStmt->fetch(PDO::FETCH_NUM);
            if ($createRow && isset($createRow[1])) {
                fwrite($handle, $createRow[1] . ";\n\n");
            }

            // Dump data in chunks to prevent memory spikes
            fwrite($handle, "-- Dumping data for table `{$table}`\n");

            $countStmt = $pdo->query("SELECT COUNT(*) FROM `{$table}`");
            $tableRowCount = (int)$countStmt->fetchColumn();
            $totalRows += $tableRowCount;

            if ($tableRowCount > 0) {
                $offset = 0;
                $chunkSize = 500;

                while ($offset < $tableRowCount) {
                    $dataStmt = $pdo->query("SELECT * FROM `{$table}` LIMIT {$chunkSize} OFFSET {$offset}");
                    $rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

                    if (!empty($rows)) {
                        $columns = array_keys($rows[0]);
                        $escapedColumns = array_map(fn($col) => "`{$col}`", $columns);
                        $colList = implode(', ', $escapedColumns);

                        $valueRows = [];
                        foreach ($rows as $r) {
                            $escapedValues = [];
                            foreach ($columns as $c) {
                                $val = $r[$c];
                                if ($val === null) {
                                    $escapedValues[] = 'NULL';
                                } elseif (is_int($val) || is_float($val)) {
                                    $escapedValues[] = $val;
                                } else {
                                    $escapedValues[] = $pdo->quote((string)$val);
                                }
                            }
                            $valueRows[] = '(' . implode(', ', $escapedValues) . ')';
                        }

                        fwrite($handle, "INSERT INTO `{$table}` ({$colList}) VALUES\n" . implode(",\n", $valueRows) . ";\n");
                    }

                    $offset += $chunkSize;
                }
                fwrite($handle, "\n");
            }
        }

        // 2. Fetch and dump all Views (if any)
        try {
            $viewsStmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
            while ($vRow = $viewsStmt->fetch(PDO::FETCH_NUM)) {
                $viewName = $vRow[0];
                fwrite($handle, "\n-- --------------------------------------------------------\n");
                fwrite($handle, "-- View structure for view `{$viewName}`\n");
                fwrite($handle, "-- --------------------------------------------------------\n\n");
                fwrite($handle, "DROP VIEW IF EXISTS `{$viewName}`;\n");
                $cView = $pdo->query("SHOW CREATE VIEW `{$viewName}`")->fetch(PDO::FETCH_NUM);
                if ($cView && isset($cView[1])) {
                    fwrite($handle, $cView[1] . ";\n\n");
                }
            }
        } catch (\Throwable) {
            // Ignore view error if unsupported
        }

        // Write SQL Footer
        fwrite($handle, "\n-- ==========================================================\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;\n");
        fwrite($handle, "SET SQL_MODE=@OLD_SQL_MODE;\n");
        fwrite($handle, "SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT;\n");
        fwrite($handle, "SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS;\n");
        fwrite($handle, "SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION;\n");
        fwrite($handle, "-- Dump completed at " . now()->toDateTimeString() . "\n");
        fwrite($handle, "-- ==========================================================\n");

        fclose($handle);

        return [
            'tables_count' => count($tables),
            'rows_count' => $totalRows,
            'engine' => 'pdo_native',
        ];
    }

    /**
     * Clean up old backups exceeding retention days (default 30 days)
     *
     * @param int $keepDays Number of days to retain (e.g. 30 days)
     * @return array
     */
    public static function cleanOldBackups(int $keepDays = 30): array
    {
        $threshold = now()->subDays($keepDays);

        $expired = DatabaseBackup::where('created_at', '<', $threshold)->get();

        $deletedCount = 0;
        $freedBytes = 0;
        $deletedFiles = [];

        foreach ($expired as $backup) {
            if (Storage::disk('local')->exists($backup->file_path)) {
                $freedBytes += (int)$backup->file_size;
                Storage::disk('local')->delete($backup->file_path);
            }
            $deletedFiles[] = $backup->filename;
            $backup->delete();
            $deletedCount++;
        }

        // Also check disk for any orphan files older than threshold
        $allFiles = Storage::disk('local')->files(self::BACKUP_DIR);
        foreach ($allFiles as $filePath) {
            if (str_ends_with($filePath, '.zip')) {
                $lastModified = Storage::disk('local')->lastModified($filePath);
                if ($lastModified < $threshold->timestamp) {
                    $size = Storage::disk('local')->size($filePath);
                    Storage::disk('local')->delete($filePath);
                    $freedBytes += $size;
                    $filename = basename($filePath);
                    if (!in_array($filename, $deletedFiles)) {
                        $deletedFiles[] = $filename;
                        $deletedCount++;
                    }
                }
            }
        }

        if ($deletedCount > 0 && class_exists(AuditLogService::class)) {
            AuditLogService::log(
                action: 'deleted',
                description: "ลบไฟล์สำรองฐานข้อมูลเก่าที่เก็บเกิน {$keepDays} วัน จำนวน {$deletedCount} ไฟล์ (คืนพื้นที่ " . self::formatBytes($freedBytes) . ")",
                module: 'system',
                moduleName: 'ระบบสำรองฐานข้อมูลอัตโนมัติ',
                user: null
            );
        }

        return [
            'count' => $deletedCount,
            'freed_bytes' => $freedBytes,
            'freed_human' => self::formatBytes($freedBytes),
            'deleted_files' => $deletedFiles,
            'keep_days' => $keepDays,
            'threshold_date' => $threshold->toDateString(),
        ];
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
