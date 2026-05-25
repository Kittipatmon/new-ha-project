<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\hrrequest\RequestCategories;
use App\Models\hrrequest\RequestType;
use App\Models\hrrequest\RequestSubtypes;
use App\Models\hrrequest\HrRequests;
use App\Models\hrrequest\Request_Safety_Docs;
use App\Models\hrrequest\Request_Safety_Items;
use App\Models\hrrequest\Request_Time_Edits;
use App\Models\hrrequest\Request_Uniforms;
use App\Models\hrrequest\RequestWelfare;
use App\Models\hrrequest\RequestCertificates;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Models\Department;
use App\Models\Division;
use App\Models\Section;

class RequestHRController extends Controller
{
    public function dashboard()
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            abort(403);
        }
        $departments = Department::all();
        $sections = Section::all();
        $divisions = Division::all();

        // 1. Chart by request status
        $statusData = HrRequests::select('status', DB::raw('count(*) as total'))
            ->whereNotNull('status') // Ensure we don't group null statuses
            ->groupBy('status')
            ->get();

        $statusOptions = HrRequests::getStatusOptions();
        $allStatuses = collect($statusOptions)->map(fn() => 0);

        foreach ($statusData as $data) {
            $allStatuses[$data->status] = $data->total;
        }

        $statusLabels = collect($statusOptions)->pluck('label');
        $statusCounts = $allStatuses->values();
        $statusColors = collect($statusOptions)->pluck('color');
        $totalRequests = HrRequests::count();
        $statusCompleted = HrRequests::where('status', HrRequests::STATUS_COMPLETED)->count();
        $statusPending = HrRequests::where('status', HrRequests::STATUS_PENDING)->count();
        $statusAPPROVEDHR = HrRequests::where('status', HrRequests::STATUS_APPROVED_HR)->count();
        $statusCancelled = HrRequests::where('status', HrRequests::STATUS_CANCELLED)
            ->orWhere('status', HrRequests::STATUS_REJECTED)
            ->count();

        $statuses = [
            ['id' => HrRequests::STATUS_PENDING, 'name' => 'อนุมัติโดยผู้จัดการ'],
            // ['id' => HrRequests::STATUS_APPROVED_MANAGER, 'name' => 'อนุมัติโดยผู้จัดการ'],
            ['id' => HrRequests::STATUS_APPROVED_HR, 'name' => 'อนุมัติโดย HR'],
            ['id' => HrRequests::STATUS_COMPLETED, 'name' => 'ดำเนินการเสร็จสิ้น'],
            ['id' => HrRequests::STATUS_REJECTED, 'name' => 'ปฏิเสธ'],
            ['id' => HrRequests::STATUS_CANCELLED, 'name' => 'ยกเลิก'],
        ];

        // 2. Chart by division
        // แก้ไข: เปลี่ยน userkml2025 เป็น userkmlsystem
        $divisionData = HrRequests::join('userkmlsystem.userskml', 'hr_requests.employee_id', '=', 'userkmlsystem.userskml.id')
            ->join('sections', 'userkmlsystem.userskml.section_id', '=', 'sections.section_id')
            ->select('sections.section_code as section_code', DB::raw('count(*) as total'))
            ->groupBy('sections.section_code')
            ->get();
        $divisionLabels = $divisionData->pluck('section_code');
        $divisionCounts = $divisionData->pluck('total');

        // 3. Chart by category
        $categoryData = HrRequests::join('request_categories', 'hr_requests.category_id', '=', 'request_categories.id')
            ->select('request_categories.name_th as name_th', DB::raw('count(*) as total'))
            ->groupBy('request_categories.name_th')
            ->get();
        $categoryLabels = $categoryData->pluck('name_th');
        $categoryCounts = $categoryData->pluck('total');

        // 4. Chart by department
        // แก้ไข: เปลี่ยน userkml2025 เป็น userkmlsystem
        $departmentData = HrRequests::join('userkmlsystem.userskml', 'hr_requests.employee_id', '=', 'userkmlsystem.userskml.id')
            ->join('department', 'userkmlsystem.userskml.department_id', '=', 'department.department_id')
            ->select('department.department_name as department_name', DB::raw('count(*) as total'))
            ->groupBy('department.department_name')
            ->get();
        $departmentLabels = $departmentData->pluck('department_name');
        $departmentCounts = $departmentData->pluck('total');

        // 5. Monthly trend chart
        $monthlyData = HrRequests::select(DB::raw('YEAR(created_at) as year, MONTH(created_at) as month'), DB::raw('count(*) as total'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $monthlyLabels = $monthlyData->map(function ($item) {
            return date('M Y', mktime(0, 0, 0, $item->month, 1, $item->year));
        });
        $monthlyCounts = $monthlyData->pluck('total');

        return view('requesthr.dashboard', compact(
            'departments',
            'sections',
            'divisions',
            'statusLabels',
            'statusCounts',
            'statusColors',
            'divisionLabels',
            'divisionCounts',
            'categoryLabels',
            'categoryCounts',
            'departmentLabels',
            'departmentCounts',
            'monthlyLabels',
            'monthlyCounts',
            'statusCompleted',
            'statusPending',
            'statusCancelled',
            'totalRequests',
            'statusAPPROVEDHR',
            'statuses'
        ));
    }

    public function dashboardFilter(Request $request)
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        // Base query builder
        $baseQuery = HrRequests::query();

        // Apply Date Filter
        if ($request->start_date) {
            $baseQuery->whereDate('hr_requests.created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $baseQuery->whereDate('hr_requests.created_at', '<=', $request->end_date);
        }

        $joinedUsers = false;
        if ($request->section_id || $request->division_id || $request->department_id) {
            $baseQuery->join('userkmlsystem.userskml', 'hr_requests.employee_id', '=', 'userkmlsystem.userskml.id');
            $joinedUsers = true;
        }

        if ($request->section_id) {
            $baseQuery->where('userkmlsystem.userskml.section_id', $request->section_id);
        }

        if ($request->division_id) {
            $baseQuery->where('userkmlsystem.userskml.division_id', $request->division_id);
        }

        // Apply Department Filter
        if ($request->department_id) {
            $baseQuery->where('userkmlsystem.userskml.department_id', $request->department_id);
        }

        // Apply Status Filter
        if ($request->status) {
            $baseQuery->where('hr_requests.status', $request->status);
        }

        // 1. Status Data
        $q1 = clone $baseQuery;
        $statusData = $q1->select('hr_requests.status', DB::raw('count(*) as total'))
            ->whereNotNull('hr_requests.status')
            ->groupBy('hr_requests.status')
            ->get();

        $statusOptions = HrRequests::getStatusOptions();
        $allStatuses = collect($statusOptions)->map(fn() => 0);
        foreach ($statusData as $data) {
            $allStatuses[$data->status] = $data->total;
        }
        $statusLabels = collect($statusOptions)->pluck('label');
        $statusCounts = $allStatuses->values();

        // Counts
        $qCount = clone $baseQuery;
        $totalRequests = $qCount->count();

        $qCount = clone $baseQuery;
        $statusCompleted = $qCount->where('hr_requests.status', HrRequests::STATUS_COMPLETED)->count();

        $qCount = clone $baseQuery;
        $statusPending = $qCount->where('hr_requests.status', HrRequests::STATUS_PENDING)->count();

        $qCount = clone $baseQuery;
        $statusAPPROVEDHR = $qCount->where('hr_requests.status', HrRequests::STATUS_APPROVED_HR)->count();

        $qCount = clone $baseQuery;
        $statusCancelled = $qCount->where(function ($q) {
            $q->where('hr_requests.status', HrRequests::STATUS_CANCELLED)
                ->orWhere('hr_requests.status', HrRequests::STATUS_REJECTED);
        })->count();


        // 2. Chart by division
        $q2 = clone $baseQuery;
        // Check if already joined users
        if (!$joinedUsers) {
            $q2->join('userkmlsystem.userskml', 'hr_requests.employee_id', '=', 'userkmlsystem.userskml.id');
        }
        $divisionData = $q2->join('sections', 'userkmlsystem.userskml.section_id', '=', 'sections.section_id')
            ->select('sections.section_code as section_code', DB::raw('count(*) as total'))
            ->groupBy('sections.section_code')
            ->get();
        $divisionLabels = $divisionData->pluck('section_code');
        $divisionCounts = $divisionData->pluck('total');

        // 3. Chart by category
        $q3 = clone $baseQuery;
        $categoryData = $q3->join('request_categories', 'hr_requests.category_id', '=', 'request_categories.id')
            ->select('request_categories.name_th as name_th', DB::raw('count(*) as total'))
            ->groupBy('request_categories.name_th')
            ->get();
        $categoryLabels = $categoryData->pluck('name_th');
        $categoryCounts = $categoryData->pluck('total');

        // 4. Chart by department
        $q4 = clone $baseQuery;
        if (!$joinedUsers) {
            $q4->join('userkmlsystem.userskml', 'hr_requests.employee_id', '=', 'userkmlsystem.userskml.id');
        }
        $departmentData = $q4->join('department', 'userkmlsystem.userskml.department_id', '=', 'department.department_id')
            ->select('department.department_name as department_name', DB::raw('count(*) as total'))
            ->groupBy('department.department_name')
            ->get();
        $departmentLabels = $departmentData->pluck('department_name');
        $departmentCounts = $departmentData->pluck('total');

        // 5. Monthly trend chart
        $q5 = clone $baseQuery;
        $monthlyData = $q5->select(DB::raw('YEAR(hr_requests.created_at) as year, MONTH(hr_requests.created_at) as month'), DB::raw('count(*) as total'))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $monthlyLabels = $monthlyData->map(function ($item) {
            return date('M Y', mktime(0, 0, 0, $item->month, 1, $item->year));
        });
        $monthlyCounts = $monthlyData->pluck('total');

        return response()->json([
            'statusLabels' => $statusLabels,
            'statusCounts' => $statusCounts,
            'divisionLabels' => $divisionLabels,
            'divisionCounts' => $divisionCounts,
            'categoryLabels' => $categoryLabels,
            'categoryCounts' => $categoryCounts,
            'departmentLabels' => $departmentLabels,
            'departmentCounts' => $departmentCounts,
            'monthlyLabels' => $monthlyLabels,
            'monthlyCounts' => $monthlyCounts,
            'totalRequests' => $totalRequests,
            'statusCompleted' => $statusCompleted,
            'statusPending' => $statusPending,
            'statusAPPROVEDHR' => $statusAPPROVEDHR,
            'statusCancelled' => $statusCancelled,
        ]);
    }

    public function welcomeRequest()
    {
        $breadcrumbs = [
            // ['label' => 'หน้าหลัก', 'url' => route('dashboard')],
            ['label' => 'งานบริการ', 'url' => route('welcome.system')],
            ['label' => 'Request HR', 'url' => null],
        ];

        $hrrequestsCount = HrRequests::where('employee_id', Auth::id())
            ->count();
        $hrrequests = HrRequests::where('employee_id', Auth::id())
            ->whereIn('status', [
                HrRequests::STATUS_PENDING,
                HrRequests::STATUS_APPROVED_MANAGER,
                HrRequests::STATUS_APPROVED_HR,
                HrRequests::STATUS_RETURNED
            ])
            ->count();

        $hrrequestapprovemanacount = HrRequests::where('approver_manager_id', Auth::id())
            // ->where('approver_manager_status', HrRequests::APPROVER_MANAGER_PENDING)
            ->where('status', HrRequests::STATUS_PENDING)
            ->count();

        $hrrequestapprovehrcount = HrRequests::where('status', HrRequests::STATUS_APPROVED_HR)
            ->count();

        $hrrequestCounts = HrRequests::count();

        return view('requesthr.welcomerequest', compact('breadcrumbs', 'hrrequestsCount', 'hrrequests', 'hrrequestapprovemanacount', 'hrrequestapprovehrcount', 'hrrequestCounts'));
    }

    public function requestHR()
    {
        $Requestcategories = RequestCategories::all();
        $Requesttypes = RequestType::all();
        $Requestsubtypes = RequestSubtypes::all();

        return view('requesthr.requesthr', compact('Requestcategories', 'Requesttypes', 'Requestsubtypes'));
    }


    public function requestStore(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'type_id' => 'required',
            'subtype_id' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            // Generate Request Code
            $date = now();
            // $prefix = 'HR-';
            $prefix = 'HR-' . $date->format('ymd') . '-';
            $lastRequest = HrRequests::where('request_code', 'like', $prefix . '%')
                ->orderBy('request_code', 'desc')
                ->first();

            $nextNumber = 1;
            if ($lastRequest) {
                $lastNumber = (int) substr($lastRequest->request_code, -4);
                $nextNumber = $lastNumber + 1;
            }
            $requestCode = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // Create Main Request
            $hrRequest = new HrRequests();
            $hrRequest->request_code = $requestCode;
            $hrRequest->employee_id = Auth::user()->id;
            $hrRequest->category_id = $request->category_id;
            $hrRequest->type_id = $request->type_id;
            $hrRequest->subtype_id = $request->subtype_id;
            $hrRequest->status = HrRequests::STATUS_PENDING;
            $hrRequest->submitted_at = now();

            // Determine approver_manager_id based on creator's level_user
            $currentUser = Auth::user();
            $approverManagerId = null;

            if ($currentUser) {
                $level = (int) $currentUser->level_user;

                if (in_array($level, [1, 2, 3, 4, 5, 6], true)) {
                    $approver = User::where('level_user', User::LEVEL_USER_DEPT_MGR)
                        ->where('department_id', $currentUser->department_id)
                        ->where('division_id', $currentUser->division_id)
                        ->first();

                    if (!$approver) {
                        $approver = User::where('level_user', User::LEVEL_USER_DEPT_MGR)
                            ->where('department_id', $currentUser->department_id)
                            ->first();
                    }

                    if (!$approver) {
                        $approver = User::where('level_user', User::LEVEL_USER_DIVISION_MGR)
                            ->where('division_id', $currentUser->division_id)
                            ->first();
                    }

                    if (!$approver) {
                        $approver = User::where('level_user', User::LEVEL_USER_C_LEVEL)->first();
                    }

                    $approverManagerId = $approver?->id;

                } elseif (in_array($level, [7, 8], true)) {
                    $approver = User::where('level_user', User::LEVEL_USER_C_LEVEL)
                        ->where('section_id', $currentUser->section_id)
                        ->first();

                    if (!$approver) {
                        $approver = User::where('level_user', User::LEVEL_USER_C_LEVEL)->first();
                    }

                    $approverManagerId = $approver?->id;
                }
            }

            $hrRequest->approver_manager_id = $approverManagerId;
            $hrRequest->approver_manager_status = HrRequests::APPROVER_MANAGER_PENDING;

            // Determine Title based on type
            $type = RequestType::find($request->type_id);
            $hrRequest->title = $type ? $type->name_th : 'คำร้องทั่วไป';

            $hrRequest->save();

            // 1. Time Edit
            if ($request->has('edit_reason') && $request->filled('edit_reason')) {
                $timeEdit = new Request_Time_Edits();
                $timeEdit->request_id = $hrRequest->hr_request_id;
                $timeEdit->edit_reason = $request->edit_reason;
                $timeEdit->edit_start_date = $request->edit_start_date;
                $timeEdit->edit_end_date = $request->edit_end_date;
                $timeEdit->edit_start_time = $request->edit_start_time;
                $timeEdit->edit_end_time = $request->edit_end_time;

                if ($request->hasFile('timefile')) {
                    $file = $request->file('timefile');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('files/hrrequest'), $filename);
                    $timeEdit->timefile = 'files/hrrequest/' . $filename;
                }

                $timeEdit->created_at = now();
                $timeEdit->save();
            }

            // 2. Uniform
            if ($request->has('uniform_gender') && $request->filled('uniform_gender')) {
                $uniform = new Request_Uniforms();
                $uniform->request_id = $hrRequest->hr_request_id;
                $uniform->uniform_gender = $request->uniform_gender;
                $uniform->uniform_size = $request->uniform_size;
                $uniform->uniform_reason = $request->uniform_reason;
                $uniform->created_at = now();
                $uniform->save();
            }

            // 3. Safety Items
            if ($request->has('safety_item_name')) {
                $names = $request->safety_item_name;
                $quantities = $request->safety_item_quantity;

                foreach ($names as $index => $name) {
                    if (!empty($name)) {
                        $safetyItem = new Request_Safety_Items();
                        $safetyItem->request_id = $hrRequest->hr_request_id;
                        $safetyItem->item_name = $name;
                        $safetyItem->quantity = $quantities[$index] ?? 1;
                        $safetyItem->created_at = now();
                        $safetyItem->save();
                    }
                }
            }

            // 4. Safety Docs
            if ($request->has('safety_reason') && $request->filled('safety_reason')) {
                $safetyDoc = new Request_Safety_Docs();
                $safetyDoc->request_id = $hrRequest->hr_request_id;
                $safetyDoc->safety_reason = $request->safety_reason;
                $safetyDoc->created_at = now();
                $safetyDoc->save();
            }

            // 5. Certificate
            if ($request->has('certificate_reason') && $request->filled('certificate_reason')) {
                $cert = new RequestCertificates();
                $cert->request_id = $hrRequest->hr_request_id;
                $cert->certificate_reason = $request->certificate_reason;
                $cert->created_at = now();
                $cert->save();
            }

            // 6. Welfare
            if ($request->has('welfare_reason') && $request->filled('welfare_reason')) {
                $welfare = new RequestWelfare();
                $welfare->request_id = $hrRequest->hr_request_id;
                $welfare->welfare_reason = $request->welfare_reason;
                $welfare->created_at = now();
                $welfare->save();
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'บันทึกข้อมูลสำเร็จ']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    public function requestHREdit($id)
    {
        $hrrequest = HrRequests::findOrFail($id);
        $Requestcategories = RequestCategories::all();
        $Requesttypes = RequestType::all();
        $Requestsubtypes = RequestSubtypes::all();

        return view('requesthr.requesthredit', compact('hrrequest', 'Requestcategories', 'Requesttypes', 'Requestsubtypes'));
    }


    public function requesthrList()
    {
        $user = Auth::user();
        $hrrequests = collect();
        if ($user?->id) {
            $hrrequests = HrRequests::where('employee_id', $user->id)
                ->whereIn('status', [
                    HrRequests::STATUS_PENDING,
                    HrRequests::STATUS_APPROVED_MANAGER,
                    HrRequests::STATUS_APPROVED_HR,
                    HrRequests::STATUS_RETURNED
                ])
                ->orderBy('created_at', 'desc')
                ->get();
        }
        return view('requesthr.requesthrlist', compact('hrrequests'));
    }

    public function requesthrlistall()
    {
        $user = Auth::user();
        $hrrequests = HrRequests::where('employee_id', $user->id)->orderBy('created_at', 'desc')->get();
        return view('requesthr.requesthrlistall', compact('hrrequests'));
    }



    public function detailUser($id)
    {
        $hrrequest = HrRequests::findOrFail($id);

        return view('requesthr.detail.detailuser', compact('hrrequest'));
    }

    public function detailMana($id)
    {
        $hrrequest = HrRequests::findOrFail($id);

        return view('requesthr.detail.detailmana', compact('hrrequest'));
    }

    public function detailHr($id)
    {
        $hrrequest = HrRequests::findOrFail($id);

        return view('requesthr.detail.detailhr', compact('hrrequest'));
    }


    public function requestHrCancel(Request $request, $id)
    {
        $hrrequest = HrRequests::findOrFail($id);

        if ($hrrequest->employee_id !== auth()->id()) {
            abort(403, 'คุณไม่มีสิทธิ์ยกเลิกคำขอนี้');
        }

        $data = $request->validate([
            'cancel_reason' => 'required|string|max:1000',
        ]);

        $hrrequest->cancel_id = auth()->id();
        $hrrequest->cancel_status = 1; // ยกเลิกคำขอ
        $hrrequest->cancel_comment = $data['cancel_reason'];
        $hrrequest->cancel_date = now();
        $hrrequest->status = HrRequests::STATUS_CANCELLED;

        if ($hrrequest->save()) {
            return redirect()->route('requesthr.list')->with('success', 'ยกเลิกคำขอเรียบร้อยแล้ว');
        }

        return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการยกเลิกคำขอ');
    }

    public function requestUpdate(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required',
            'type_id' => 'required',
            'subtype_id' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $hrRequest = HrRequests::findOrFail($id);

            // Check permission (optional but good practice)
            if ($hrRequest->employee_id !== Auth::id()) {
                throw new \Exception('คุณไม่มีสิทธิ์แก้ไขคำร้องนี้');
            }

            $hrRequest->category_id = $request->category_id;
            $hrRequest->type_id = $request->type_id;
            $hrRequest->subtype_id = $request->subtype_id;

            // Update title if type changed
            $type = RequestType::find($request->type_id);
            $hrRequest->title = $type ? $type->name_th : 'คำร้องทั่วไป';

            // Reset status if needed? Usually editing a returned request sets it back to pending?
            // If status is RETURNED, set back to PENDING or APPROVED_MANAGER_PENDING?
            if ($hrRequest->status == HrRequests::STATUS_RETURNED) {
                $hrRequest->status = HrRequests::STATUS_PENDING;
                $hrRequest->approver_manager_status = HrRequests::APPROVER_MANAGER_PENDING;
                $hrRequest->approver_hr_status = HrRequests::APPROVER_HR_PENDING;
            }

            $hrRequest->save();

            // 1. Time Edit
            if ($request->has('edit_reason')) {
                $timeEdit = Request_Time_Edits::where('request_id', $hrRequest->hr_request_id)->first();
                if (!$timeEdit && $request->filled('edit_reason')) {
                    $timeEdit = new Request_Time_Edits();
                    $timeEdit->request_id = $hrRequest->hr_request_id;
                }

                if ($timeEdit) {
                    $timeEdit->edit_reason = $request->edit_reason;
                    $timeEdit->edit_start_date = $request->edit_start_date;
                    $timeEdit->edit_end_date = $request->edit_end_date;
                    $timeEdit->edit_start_time = $request->edit_start_time;
                    $timeEdit->edit_end_time = $request->edit_end_time;

                    if ($request->hasFile('timefile')) {
                        // Delete old file if exists?
                        if ($timeEdit->timefile && file_exists(public_path($timeEdit->timefile))) {
                            // unlink(public_path($timeEdit->timefile)); // Optional
                        }

                        $file = $request->file('timefile');
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $file->move(public_path('files/hrrequest'), $filename);
                        $timeEdit->timefile = 'files/hrrequest/' . $filename;
                    }
                    $timeEdit->save();
                }
            }

            // 2. Uniform
            if ($request->has('uniform_gender')) {
                $uniform = Request_Uniforms::where('request_id', $hrRequest->hr_request_id)->first();
                if (!$uniform && $request->filled('uniform_gender')) {
                    $uniform = new Request_Uniforms();
                    $uniform->request_id = $hrRequest->hr_request_id;
                }

                if ($uniform) {
                    $uniform->uniform_gender = $request->uniform_gender;
                    $uniform->uniform_size = $request->uniform_size;
                    $uniform->uniform_reason = $request->uniform_reason;
                    $uniform->save();
                }
            }

            // 3. Safety Items
            // Delete all existing and recreate
            if ($request->has('safety_item_name')) {
                Request_Safety_Items::where('request_id', $hrRequest->hr_request_id)->delete();

                $names = $request->safety_item_name;
                $quantities = $request->safety_item_quantity;

                foreach ($names as $index => $name) {
                    if (!empty($name)) {
                        $safetyItem = new Request_Safety_Items();
                        $safetyItem->request_id = $hrRequest->hr_request_id;
                        $safetyItem->item_name = $name;
                        $safetyItem->quantity = $quantities[$index] ?? 1;
                        $safetyItem->created_at = now();
                        $safetyItem->save();
                    }
                }
            }

            // 4. Safety Docs
            if ($request->has('safety_reason')) {
                $safetyDoc = Request_Safety_Docs::where('request_id', $hrRequest->hr_request_id)->first();
                if (!$safetyDoc && $request->filled('safety_reason')) {
                    $safetyDoc = new Request_Safety_Docs();
                    $safetyDoc->request_id = $hrRequest->hr_request_id;
                }
                if ($safetyDoc) {
                    $safetyDoc->safety_reason = $request->safety_reason;
                    $safetyDoc->save();
                }
            }

            // 5. Certificate
            if ($request->has('certificate_reason')) {
                $cert = RequestCertificates::where('request_id', $hrRequest->hr_request_id)->first();
                if (!$cert && $request->filled('certificate_reason')) {
                    $cert = new RequestCertificates();
                    $cert->request_id = $hrRequest->hr_request_id;
                }
                if ($cert) {
                    $cert->certificate_reason = $request->certificate_reason;
                    $cert->save();
                }
            }

            // 6. Welfare
            if ($request->has('welfare_reason')) {
                $welfare = RequestWelfare::where('request_id', $hrRequest->hr_request_id)->first();
                if (!$welfare && $request->filled('welfare_reason')) {
                    $welfare = new RequestWelfare();
                    $welfare->request_id = $hrRequest->hr_request_id;
                }
                if ($welfare) {
                    $welfare->welfare_reason = $request->welfare_reason;
                    $welfare->save();
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'แก้ไขข้อมูลสำเร็จ']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }
}
