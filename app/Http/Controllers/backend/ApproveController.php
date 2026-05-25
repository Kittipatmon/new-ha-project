<?php

namespace App\Http\Controllers\backend;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\hrrequest\RequestCategories;
use App\Models\hrrequest\RequestSubtypes;
use App\Models\hrrequest\RequestType;
use App\Models\hrrequest\HrRequests;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\DB;

use App\Models\Section;
use App\Models\Department;
use App\Models\Division;

class ApproveController extends Controller
{

    public function approvemanalist()
    {
        $hrrequests = HrRequests::with(['user', 'approverManager', 'approverhr', 'category', 'type', 'subtype'])
            ->whereIn('status', [
                HrRequests::STATUS_PENDING,
            ])
            ->where('approver_manager_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('requesthr.approve.approvemanalist', compact('hrrequests'));
    }

    public function managerCheck(Request $request, $id)
    {
        $hrrequest = HrRequests::findOrFail($id);

        if ($hrrequest->approver_manager_id !== auth()->id()) {
            abort(403, 'คุณไม่มีสิทธิ์อนุมัติคำขอนี้');
        }

        $data = $request->validate([
            'status' => 'required|integer|in:1,2,3',
            'comment' => 'nullable|string|max:1000',
        ]);

        // 0 = รออนุมัติ 1 = อนุมัติ 2 = ไม่อนุมัติ 3 = ส่งกลับแก้ไข
        $status = (int) ($data['status'] ?? $hrrequest->approver_manager_status);

        $hrrequest->approver_manager_status = $status;
        $hrrequest->approver_manager_comment = $data['comment'] ?? null;
        $hrrequest->approver_manager_at = now();

        // กำหนดสถานะหลัก + ข้อความตอบกลับ
        $flashType = 'info';
        $flashMsg = 'อัปเดตสถานะคำขอเรียบร้อยแล้ว';

        if ($status === 1) {
            $hrrequest->status = HrRequests::STATUS_APPROVED_HR;
            $hrrequest->approver_hr_id = null;
            $hrrequest->approver_hr_status = 0;
            $flashType = 'success';
            $flashMsg = 'คำขอได้รับการอนุมัติแล้ว';
        } elseif ($status === 2) {
            $hrrequest->status = HrRequests::STATUS_REJECTED;
            $flashType = 'error';
            $flashMsg = 'คำขอถูกปฏิเสธแล้ว';
        } elseif ($status === 3) {
            $hrrequest->status = HrRequests::STATUS_PENDING;
            $flashType = 'warning';
            $flashMsg = 'คำขอถูกส่งกลับแก้ไขแล้ว';
        }

        if ($hrrequest->save()) {
            if ($hrrequest->save()) {
                return redirect()->route('approve.approvemanalist')
                    ->with($flashType, $flashMsg);
            }
        }

        return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการอนุมัติคำขอ');
    }

    public function approvehrlist()
    {
        $hrrequests = HrRequests::with(['user', 'approverManager', 'approverhr', 'category', 'type', 'subtype'])
            ->whereIn('status', [
                HrRequests::STATUS_APPROVED_HR,
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('requesthr.approve.approvehrlist', compact('hrrequests'));
    }

    public function approvehrlistall(Request $request)
    {
        $categories = RequestCategories::all();
        $types = RequestType::all();
        $subtypes = RequestSubtypes::all();

        $sections = Section::orderBy('section_name')->get();
        $departments = Department::orderBy('department_name')->get();
        $divisions = Division::orderBy('division_name')->get();

        $statuses = [
            ['id' => HrRequests::STATUS_PENDING, 'name' => 'อนุมัติโดยผู้จัดการ'],
            // ['id' => HrRequests::STATUS_APPROVED_MANAGER, 'name' => 'อนุมัติโดยผู้จัดการ'],
            ['id' => HrRequests::STATUS_APPROVED_HR, 'name' => 'อนุมัติโดย HR'],
            ['id' => HrRequests::STATUS_COMPLETED, 'name' => 'ดำเนินการเสร็จสิ้น'],
            ['id' => HrRequests::STATUS_REJECTED, 'name' => 'ปฏิเสธ'],
            ['id' => HrRequests::STATUS_CANCELLED, 'name' => 'ยกเลิก'],
        ];

        $query = HrRequests::with(['user', 'approverManager', 'approverhr', 'category', 'type', 'subtype'])
            ->orderBy('created_at', 'desc');

        // Free-text search across request_code, title, and user fullname via separate lookup (cross-connection safe)
        if ($request->filled('search')) {
            $search = trim($request->search);
            // Find matching users on their own connection, then filter hr_requests by employee_id
            $userIds = User::query()
                ->whereRaw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,'')) LIKE ?", ["%{$search}%"])
                ->pluck('id');

            $query->where(function ($q) use ($search, $userIds) {
                $q->where('request_code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
                if ($userIds->isNotEmpty()) {
                    $q->orWhereIn('employee_id', $userIds->all());
                }
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('type')) {
            $query->where('type_id', $request->type);
        }

        if ($request->filled('subtype')) {
            $query->where('subtype_id', $request->subtype);
        }

        // Filter by organization hierarchy via user IDs (cross-connection safe)
        if ($request->filled('section') || $request->filled('division') || $request->filled('department')) {
            $userOrgQuery = User::query();
            if ($request->filled('section')) {
                $userOrgQuery->where('section_id', $request->section);
            }
            if ($request->filled('division')) {
                $userOrgQuery->where('division_id', $request->division);
            }
            if ($request->filled('department')) {
                $userOrgQuery->where('department_id', $request->department);
            }
            $userIdsByOrg = $userOrgQuery->pluck('id');
            // If no users match, ensure empty result
            if ($userIdsByOrg->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('employee_id', $userIdsByOrg->all());
            }
        }

        // Clone before applying status to compute consistent counts using all other filters
        $baseQuery = clone $query;

        // Summary counts for current filters (excluding status)
        $totalCount = (clone $baseQuery)->count();
        $statusCompleted = (clone $baseQuery)->where('status', HrRequests::STATUS_COMPLETED)->count();
        $statusPending = (clone $baseQuery)->where('status', HrRequests::STATUS_PENDING)->count();
        $statusAPPROVEDHR = (clone $baseQuery)->where('status', HrRequests::STATUS_APPROVED_HR)->count();
        $statusCancelled = (clone $baseQuery)->whereIn('status', [HrRequests::STATUS_CANCELLED, HrRequests::STATUS_REJECTED])->count();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }


        $hrrequests = $query->get();
        return view('requesthr.approve.approvehrlistall', compact('hrrequests', 'categories', 'types', 'subtypes', 'statuses', 'statusCompleted', 'statusPending', 'statusAPPROVEDHR', 'statusCancelled', 'totalCount', 'sections', 'departments', 'divisions'));
    }

    /**
     * AJAX endpoint: return table rows HTML for filtered HR requests
     */
    public function approvehrlistallData(Request $request)
    {
        $query = HrRequests::with(['user', 'category', 'type', 'subtype'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $userIds = User::query()
                ->whereRaw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,'')) LIKE ?", ["%{$search}%"])
                ->pluck('id');

            $query->where(function ($q) use ($search, $userIds) {
                $q->where('request_code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
                if ($userIds->isNotEmpty()) {
                    $q->orWhereIn('employee_id', $userIds->all());
                }
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('type')) {
            $query->where('type_id', $request->type);
        }

        if ($request->filled('subtype')) {
            $query->where('subtype_id', $request->subtype);
        }

        // Filter by organization hierarchy via user IDs (cross-connection safe)
        if ($request->filled('section') || $request->filled('division') || $request->filled('department')) {
            $userOrgQuery = User::query();
            if ($request->filled('section')) {
                $userOrgQuery->where('section_id', $request->section);
            }
            if ($request->filled('division')) {
                $userOrgQuery->where('division_id', $request->division);
            }
            if ($request->filled('department')) {
                $userOrgQuery->where('department_id', $request->department);
            }
            $userIdsByOrg = $userOrgQuery->pluck('id');
            if ($userIdsByOrg->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('employee_id', $userIdsByOrg->all());
            }
        }

        // Clone before applying status to compute consistent counts using all other filters
        $baseQuery = clone $query;

        // Summary counts used by the PDF view (excluding status filter)
        $totalCount = (clone $baseQuery)->count();
        $statusCompleted = (clone $baseQuery)->where('status', HrRequests::STATUS_COMPLETED)->count();
        $statusPending = (clone $baseQuery)->where('status', HrRequests::STATUS_PENDING)->count();
        $statusAPPROVEDHR = (clone $baseQuery)->where('status', HrRequests::STATUS_APPROVED_HR)->count();
        $statusCancelled = (clone $baseQuery)->whereIn('status', [HrRequests::STATUS_CANCELLED, HrRequests::STATUS_REJECTED])->count();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $hrrequests = $query->get();

        // Return only the rows HTML to be injected into tbody
        return response()->view('requesthr.approve.partials.approvehrlistall_rows', compact('hrrequests'));
    }

    public function approvehrlistallExport(Request $request)
    {
        $query = HrRequests::with(['user', 'category', 'type', 'subtype'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $userIds = User::query()
                ->whereRaw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,'')) LIKE ?", ["%{$search}%"])
                ->pluck('id');

            $query->where(function ($q) use ($search, $userIds) {
                $q->where('request_code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
                if ($userIds->isNotEmpty()) {
                    $q->orWhereIn('employee_id', $userIds->all());
                }
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('type')) {
            $query->where('type_id', $request->type);
        }

        if ($request->filled('subtype')) {
            $query->where('subtype_id', $request->subtype);
        }

        // Filter by organization hierarchy via user IDs (cross-connection safe)
        if ($request->filled('section') || $request->filled('division') || $request->filled('department')) {
            $userOrgQuery = User::query();
            if ($request->filled('section')) {
                $userOrgQuery->where('section_id', $request->section);
            }
            if ($request->filled('division')) {
                $userOrgQuery->where('division_id', $request->division);
            }
            if ($request->filled('department')) {
                $userOrgQuery->where('department_id', $request->department);
            }
            $userIdsByOrg = $userOrgQuery->pluck('id');
            if ($userIdsByOrg->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('employee_id', $userIdsByOrg->all());
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Clone before applying status/date to compute consistent counts (same as PDF)
        $baseQuery = clone $query;

        $totalCount = (clone $baseQuery)->count();
        $statusCompleted = (clone $baseQuery)->where('status', HrRequests::STATUS_COMPLETED)->count();
        $statusPending = (clone $baseQuery)->where('status', HrRequests::STATUS_PENDING)->count();
        $statusAPPROVEDHR = (clone $baseQuery)->where('status', HrRequests::STATUS_APPROVED_HR)->count();
        $statusCancelled = (clone $baseQuery)->whereIn('status', [HrRequests::STATUS_CANCELLED, HrRequests::STATUS_REJECTED])->count();

        $hrrequests = $query->get();

        $filename = 'hr_requests_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $columns = [
            'เลขที่รายการ',
            'รหัสพนักงาน',
            'ชื่อ-สกุล',
            'สายงาน',
            'ฝ่าย',
            'แผนก',
            'หมวดหมู่คำร้อง',
            'ประเภทคำร้อง',
            'ประเภทย่อย',
            'วันที่ส่งคำร้อง',
            'สถานะ',
        ];

        $callback = function () use ($hrrequests, $columns, $totalCount, $statusCompleted, $statusPending, $statusAPPROVEDHR, $statusCancelled) {
            $output = fopen('php://output', 'w');

            // BOM for Excel UTF-8
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Summary rows (Thai labels) before the data header
            fputcsv($output, ['รายการคำขอทั้งหมด', $totalCount]);
            fputcsv($output, ['รอตรวจสอบโดยผู้จัดการ', $statusPending]);
            fputcsv($output, ['รอตรวจสอบโดยฝ่ายบุคคล', $statusAPPROVEDHR]);
            fputcsv($output, ['ยกเลิก/ปฏิเสธ', $statusCancelled]);
            // Optional: completed count if needed
            // fputcsv($output, ['ดำเนินการเสร็จสิ้น', $statusCompleted]);

            // Blank line before table header
            fputcsv($output, []);

            // Header columns
            fputcsv($output, $columns);

            foreach ($hrrequests as $req) {
                fputcsv($output, [
                    $req->request_code,
                    optional($req->user)->employee_code,
                    optional($req->user)->fullname,
                    optional($req->user->section)->section_code ?? '-',
                    optional($req->user->department->division)->division_name ?? '-',
                    optional($req->user->department)->department_name ?? '-',
                    optional($req->category)->name_th,
                    optional($req->type)->name_th,
                    optional($req->subtype)->name_th,
                    optional($req->created_at)->format('d/m/Y'),
                    method_exists($req, 'getAttribute') ? ($req->status_label ?? $req->status) : $req->status,
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function approvehrlistallPdf(Request $request)
    {
        $query = HrRequests::with(['user', 'category', 'type', 'subtype'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $userIds = User::query()
                ->whereRaw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,'')) LIKE ?", ["%{$search}%"])
                ->pluck('id');

            $query->where(function ($q) use ($search, $userIds) {
                $q->where('request_code', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
                if ($userIds->isNotEmpty()) {
                    $q->orWhereIn('employee_id', $userIds->all());
                }
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('type')) {
            $query->where('type_id', $request->type);
        }

        if ($request->filled('subtype')) {
            $query->where('subtype_id', $request->subtype);
        }

        // Filter by organization hierarchy via user IDs (cross-connection safe)
        if ($request->filled('section') || $request->filled('division') || $request->filled('department')) {
            $userOrgQuery = User::query();
            if ($request->filled('section')) {
                $userOrgQuery->where('section_id', $request->section);
            }
            if ($request->filled('division')) {
                $userOrgQuery->where('division_id', $request->division);
            }
            if ($request->filled('department')) {
                $userOrgQuery->where('department_id', $request->department);
            }
            $userIdsByOrg = $userOrgQuery->pluck('id');
            if ($userIdsByOrg->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('employee_id', $userIdsByOrg->all());
            }
        }

        // Clone before applying status/date to compute consistent counts
        $baseQuery = clone $query;

        // Summary counts (excluding status/date filters)
        $totalCount = (clone $baseQuery)->count();
        $statusCompleted = (clone $baseQuery)->where('status', HrRequests::STATUS_COMPLETED)->count();
        $statusPending = (clone $baseQuery)->where('status', HrRequests::STATUS_PENDING)->count();
        $statusAPPROVEDHR = (clone $baseQuery)->where('status', HrRequests::STATUS_APPROVED_HR)->count();
        $statusCancelled = (clone $baseQuery)->whereIn('status', [HrRequests::STATUS_CANCELLED, HrRequests::STATUS_REJECTED])->count();

        // Apply status/date filters for the actual list
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $hrrequests = $query->get();


        $filename = 'hr_requests_' . now()->format('Ymd_His') . '.pdf';

        // Use Dompdf if available
        if (class_exists(Pdf::class)) {
            $pdf = Pdf::loadView('requesthr.approve.approvehrlistall_pdf', [
                'hrrequests' => $hrrequests,
                'generated_at' => now(),
                'totalCount' => $totalCount,
                'statusCompleted' => $statusCompleted,
                'statusPending' => $statusPending,
                'statusAPPROVEDHR' => $statusAPPROVEDHR,
                'statusCancelled' => $statusCancelled,
            ]);
            // A4 portrait
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download($filename);
        }

        // Fallback if package not installed
        return response(
            'PDF library not installed. Please run: composer require barryvdh/laravel-dompdf',
            501
        );
    }

    public function hrcheck(Request $request, $id)
    {
        $hrrequest = HrRequests::findOrFail($id);

        // if ($hrrequest->approver_hr_id !== auth()->id()) {
        //     abort(403, 'คุณไม่มีสิทธิ์อนุมัติคำขอนี้');
        // }

        $data = $request->validate([
            'status' => 'required|integer|in:1,2,3',
            'comment' => 'nullable|string|max:1000',
        ]);

        // 0 = รออนุมัติ 1 = อนุมัติ 2 = ไม่อนุมัติ 3 = ส่งกลับแก้ไข
        $status = (int) ($data['status'] ?? $hrrequest->approver_hr_status);

        $hrrequest->approver_hr_id = auth()->id();
        $hrrequest->approver_hr_status = $status;
        $hrrequest->approver_hr_comment = $data['comment'] ?? null;
        $hrrequest->approver_hr_at = now();

        // กำหนดสถานะหลัก + ข้อความตอบกลับ
        $flashType = 'info';
        $flashMsg = 'อัปเดตสถานะคำขอเรียบร้อยแล้ว';

        if ($status === 1) {
            $hrrequest->status = HrRequests::STATUS_COMPLETED;
            $flashType = 'success';
            $flashMsg = 'คำขอได้รับการอนุมัติแล้ว';
        } elseif ($status === 2) {
            $hrrequest->status = HrRequests::STATUS_REJECTED;
            $flashType = 'error';
            $flashMsg = 'คำขอถูกปฏิเสธแล้ว';
        } elseif ($status === 3) {
            $hrrequest->status = HrRequests::STATUS_PENDING;
            $flashType = 'warning';
            $flashMsg = 'คำขอถูกส่งกลับแก้ไขแล้ว';
        }

        if ($hrrequest->save()) {
            return redirect()->route('approve.approvehrlist')->with($flashType, $flashMsg);
        }

        return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการอนุมัติคำขอ');
    }



    // public function approveManager(Request $request, $id)
    // {
    //     $hrrequest = HrRequests::findOrFail($id);

    //     // ตรวจสอบว่าเป็นผู้อนุมัติที่ถูกต้อง
    //     if ($hrrequest->approver_manager_id !== auth()->user()->id) {
    //         return redirect()->back()->with('error', 'คุณไม่มีสิทธิ์อนุมัติคำขอนี้');
    //     }

    //     // อัปเดตสถานะการอนุมัติ
    //     $hrrequest->approver_manager_status = $request->input('status');
    //     $hrrequest->approver_manager_comment = $request->input('comment');
    //     $hrrequest->approver_manager_at = now();
    //     $hrrequest->save();

    //     return redirect()->back()->with('success', 'อนุมัติคำขอเรียบร้อยแล้ว');
    // }

    // public function approveHR(Request $request, $id)
    // {
    //     $hrrequest = HrRequests::findOrFail($id);

    //     // ตรวจสอบว่าเป็นผู้อนุมัติที่ถูกต้อง
    //     if ($hrrequest->approver_hr_id !== auth()->user()->id) {
    //         return redirect()->back()->with('error', 'คุณไม่มีสิทธิ์อนุมัติคำขอนี้');
    //     }

    //     // อัปเดตสถานะการอนุมัติ
    //     $hrrequest->approver_hr_status = $request->input('status');
    //     $hrrequest->approver_hr_comment = $request->input('comment');
    //     $hrrequest->approver_hr_at = now();
    //     $hrrequest->save();

    //     return redirect()->back()->with('success', 'อนุมัติคำขอเรียบร้อยแล้ว');
    // }


    public function filterRequestHR()
    {

    }
}