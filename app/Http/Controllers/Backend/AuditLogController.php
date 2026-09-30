<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SystemAuditArchive;
use App\Models\SystemAuditLog;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs and archive records
     */
    public function index(Request $request)
    {
        $query = SystemAuditLog::query();

        // 1. Keyword search
        if ($search = trim($request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('user_code', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%");
            });
        }

        // 2. Action filter
        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        // 3. Module filter
        if ($module = $request->input('module')) {
            $query->where('module', $module);
        }

        // 4. Date range filter
        if ($startDate = $request->input('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        // 5. User filter
        if ($userId = $request->input('user_id')) {
            $query->where('user_id', $userId);
        }

        // Metrics for summary KPI cards
        $metrics = [
            'total' => SystemAuditLog::count(),
            'created' => SystemAuditLog::where('action', 'created')->count(),
            'updated' => SystemAuditLog::where('action', 'updated')->count(),
            'deleted' => SystemAuditLog::where('action', 'deleted')->count(),
            'archives' => SystemAuditArchive::count(),
        ];

        // Available modules for dropdown filter
        $modules = SystemAuditLog::select('module', 'module_name')
            ->distinct()
            ->whereNotNull('module')
            ->get();

        // Archives list for the 5-year retention & Zip archive ledger tab
        $archives = SystemAuditArchive::orderBy('created_at', 'desc')->get();

        // Available years that have logs
        $yearsWithLogs = SystemAuditLog::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // 6. Sort order
        $sortOrder = $request->input('sort', 'desc');
        $logs = $query->orderBy('created_at', $sortOrder === 'asc' ? 'asc' : 'desc')
            ->limit(500)
            ->get();

        return view('backend.audit-logs.index', compact(
            'logs',
            'metrics',
            'modules',
            'archives',
            'yearsWithLogs',
            'sortOrder'
        ));
    }

    /**
     * Real-time AJAX endpoint for DataTable live feed
     */
    public function getLogsData(Request $request)
    {
        $sinceId = (int)$request->input('since_id', 0);

        $query = SystemAuditLog::query();

        // If polling for newly created logs in real-time
        if ($sinceId > 0) {
            $query->where('id', '>', $sinceId);
        }

        // Apply filters if any
        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }
        if ($module = $request->input('module')) {
            $query->where('module', $module);
        }
        if ($search = trim($request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('user_code', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $sinceId > 0
            ? $query->orderBy('id', 'asc')->get()
            : $query->orderBy('id', 'desc')->take(500)->get();

        $formatted = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'timestamp' => $log->created_at->timestamp,
                'thai_date' => $log->thai_date,
                'thai_time' => $log->thai_time,
                'thai_datetime' => $log->thai_datetime,
                'user_name' => $log->user_name ?: 'ระบบอัตโนมัติ',
                'user_code' => $log->user_code ?: ($log->user_role ?: '-'),
                'user_initial' => mb_substr($log->user_name ?: 'S', 0, 1),
                'action' => $log->action,
                'action_label' => $log->getActionLabel(),
                'badge_class' => $log->getActionBadgeClass(),
                'icon' => $log->getActionIcon(),
                'module' => $log->module,
                'module_name' => $log->module_name ?: ucfirst($log->module),
                'description' => $log->description,
                'has_diff' => $log->hasDiff(),
                'diff_count' => is_array($log->diff) ? count($log->diff) : 0,
                'ip_address' => $log->ip_address ?: '-',
                'method' => $log->method ?: 'POST',
                'url' => $log->url,
                'created_at_raw' => $log->created_at->toIso8601String(),
            ];
        });

        $latestLog = SystemAuditLog::orderBy('id', 'desc')->first();

        return response()->json([
            'success' => true,
            'data' => $formatted,
            'latest_id' => $latestLog?->id ?? 0,
            'metrics' => [
                'total' => SystemAuditLog::count(),
                'created' => SystemAuditLog::where('action', 'created')->count(),
                'updated' => SystemAuditLog::where('action', 'updated')->count(),
                'deleted' => SystemAuditLog::where('action', 'deleted')->count(),
                'archives' => SystemAuditArchive::count(),
            ],
            'server_time' => (new SystemAuditLog(['created_at' => now()]))->thai_time,
        ]);
    }

    /**
     * Get single audit log details for Diff Viewer modal
     */
    public function show(int $id)
    {
        $log = SystemAuditLog::findOrFail($id);

        return response()->json([
            'success' => true,
            'log' => $log,
            'action_label' => $log->getActionLabel(),
            'badge_class' => $log->getActionBadgeClass(),
            'icon' => $log->getActionIcon(),
            'formatted_time' => $log->thai_full_datetime,
        ]);
    }

    /**
     * Create an on-demand Zip Archive for a given year
     */
    public function createArchive(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
            'notes' => 'nullable|string|max:500',
            'purge' => 'nullable|boolean',
        ]);

        $year = (int)$request->input('year');
        $notes = $request->input('notes', 'สั่งบีบอัดและจัดเก็บคลังข้อมูลผ่านระบบจัดการ');
        $purge = $request->boolean('purge', false);

        try {
            $archive = AuditLogService::createArchive($year, Auth::user(), $notes, $purge);

            // Log that an archive was created
            AuditLogService::log(
                action: 'archived',
                description: "สร้างไฟล์คลังบีบอัด Audit Log: {$archive->filename} (จำนวน {$archive->records_count} รายการ)" . ($purge ? " และล้างข้อมูลตารางเพื่อเริ่มรอบปีใหม่" : ""),
                model: $archive,
                module: 'system',
                moduleName: 'ระบบจัดเก็บข้อมูลคลัง Log',
                user: Auth::user()
            );

            $successMsg = "สร้างไฟล์คลัง ZIP สำหรับปี {$archive->period_label} สำเร็จเรียบร้อยแล้ว ({$archive->records_count} รายการ) ระบบจะจัดเก็บไฟล์ไว้นาน 5 ปี" . ($purge ? " และล้างข้อมูลปี {$year} ออกจากตารางเพื่อเริ่มบันทึกปีใหม่เรียบร้อยแล้ว" : "");

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMsg,
                    'archive' => $archive,
                ]);
            }

            return redirect()->route('backend.audit-logs.index', ['tab' => 'archives'])
                ->with('success', $successMsg);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "เกิดข้อผิดพลาดในการสร้างไฟล์ ZIP: " . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('backend.audit-logs.index', ['tab' => 'archives'])
                ->with('error', "เกิดข้อผิดพลาดในการสร้างไฟล์ ZIP: " . $e->getMessage());
        }
    }

    /**
     * Clean up expired archives older than 5 years
     */
    public function cleanExpiredArchives(Request $request)
    {
        try {
            $result = AuditLogService::cleanupExpiredArchives(5);

            if ($result['count'] > 0) {
                AuditLogService::log(
                    action: 'deleted',
                    description: "ล้างไฟล์ ZIP คลัง Log ที่หมดอายุตามนโยบาย 5 ปี จำนวน {$result['count']} ไฟล์ (คืนพื้นที่ {$result['freed_human']})",
                    module: 'system',
                    moduleName: 'ระบบจัดเก็บข้อมูลคลัง Log',
                    user: Auth::user()
                );

                $msg = "ลบไฟล์ ZIP ที่จัดเก็บครบกำหนด 5 ปีเรียบร้อยแล้วจำนวน {$result['count']} ไฟล์ (คืนพื้นที่ {$result['freed_human']})";
            } else {
                $msg = "ไม่มีไฟล์ ZIP ที่จัดเก็บเกิน 5 ปี (ไฟล์ทั้งหมดยังอยู่ในระยะเวลาการเก็บรักษา)";
            }

            return redirect()->route('backend.audit-logs.index', ['tab' => 'archives'])
                ->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->route('backend.audit-logs.index', ['tab' => 'archives'])
                ->with('error', "เกิดข้อผิดพลาดในการล้างไฟล์หมดอายุ: " . $e->getMessage());
        }
    }

    /**
     * Download the archived ZIP file
     */
    public function downloadArchive(int $id)
    {
        $archive = SystemAuditArchive::findOrFail($id);

        if (!$archive->fileExists()) {
            return back()->with('error', 'ไม่พบไฟล์บีบอัดบนระบบจัดเก็บ');
        }

        // Record download event for auditor accountability
        AuditLogService::log(
            action: 'exported',
            description: "ดาวน์โหลดไฟล์คลัง Audit Log: {$archive->filename} (ขนาด {$archive->file_size_human})",
            model: $archive,
            module: 'system',
            moduleName: 'ระบบจัดเก็บข้อมูลคลัง Log',
            user: Auth::user()
        );

        $path = $archive->getAbsolutePath();
        return Response::download($path, $archive->filename, [
            'Content-Type' => 'application/zip',
        ]);
    }

    /**
     * Inspect logs inside the ZIP file directly in the browser for Auditor review
     */
    public function inspectArchive(int $id)
    {
        $archive = SystemAuditArchive::findOrFail($id);

        if (!$archive->fileExists()) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบไฟล์ ZIP ในระบบจัดเก็บ',
            ], 404);
        }

        try {
            $logs = AuditLogService::readArchiveLogs($archive);

            return response()->json([
                'success' => true,
                'archive' => [
                    'id' => $archive->id,
                    'filename' => $archive->filename,
                    'period_label' => $archive->period_label,
                    'records_count' => $archive->records_count,
                    'file_size_human' => $archive->file_size_human,
                    'checksum_sha256' => $archive->checksum_sha256,
                    'created_at' => $archive->thai_datetime,
                ],
                'logs' => $logs,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถอ่านข้อมูลจากไฟล์ ZIP: ' . $e->getMessage(),
            ], 500);
        }
    }
}
