<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ManpowerRequest;
use App\Models\User;
use App\Notifications\ManpowerRequestNotification;
use Barryvdh\DomPDF\Facade\Pdf;

class ManpowerRequestController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userName = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));
        $userFullName = trim($user->fullname ?? $userName);

        $pendingApprovalsQuery = ManpowerRequest::query()
            ->whereNotIn('status', ['approved', 'rejected', 'draft']);

        if ($user && $user->role !== 'admin') {
            $userDept = $user->department ? $user->department->department_name : '';
            $userSec = $user->section ? $user->section->section_name : '';
            $userDiv = $user->division ? $user->division->division_name : '';

            $pendingApprovalsQuery->where(function($q) use ($user, $userName, $userFullName, $userDept, $userSec, $userDiv) {
                $q->where(function($sub) use ($userName, $userFullName, $userDept, $userSec) {
                    $sub->where('status', 'pending_manager')
                        ->where(function($m) use ($userName, $userFullName, $userDept, $userSec) {
                            $m->where('manager_name', 'like', "%{$userName}%")
                              ->orWhere('manager_name', 'like', "%{$userFullName}%")
                              ->orWhereNull('manager_name')
                              ->orWhere('manager_name', '')
                              ->orWhere(function($d) use ($userDept, $userSec) {
                                  if ($userDept) $d->where('department', 'like', "%{$userDept}%");
                                  if ($userSec) $d->orWhere('section', 'like', "%{$userSec}%");
                              });
                        });
                })
                ->orWhere(function($sub) use ($userName, $userFullName, $userDiv) {
                    $sub->where('status', 'pending_vp')
                        ->where(function($v) use ($userName, $userFullName, $userDiv) {
                            $v->where('vp_name', 'like', "%{$userName}%")
                              ->orWhere('vp_name', 'like', "%{$userFullName}%")
                              ->orWhereNull('vp_name')
                              ->orWhere('vp_name', '')
                              ->orWhere(function($d) use ($userDiv) {
                                  if ($userDiv) $d->where('department', 'like', "%{$userDiv}%");
                              });
                        });
                });

                if ($user->isHrOrAdmin()) {
                    $q->orWhere('status', 'pending_hr');
                }
                if ($user->level_user == '9') {
                    $q->orWhere('status', 'pending_ceo');
                }
            });
        }

        $pendingApprovals = $pendingApprovalsQuery->orderBy('id', 'desc')->get();

        // 2. Pending Probation Evaluation Approvals
        $pendingProbationsQuery = \App\Models\ProbationEvaluation::query()
            ->whereNotIn('status', ['approved', 'rejected']);

        if ($user && $user->role !== 'admin') {
            $userDept = $user->department ? $user->department->department_name : '';
            $userSec = $user->section ? $user->section->section_name : '';

            $pendingProbationsQuery->where(function($q) use ($user, $userName, $userFullName, $userDept, $userSec) {
                $q->where(function($sub) use ($userFullName, $userDept, $userSec) {
                    $sub->whereHas('signatures', function($sq) use ($userFullName) {
                            $sq->whereIn('role', ['evaluator', 'manager', 'hr'])
                               ->where('name', $userFullName);
                        });
                    if ($userDept) {
                        $sub->orWhere('department', 'like', "%{$userDept}%");
                    }
                    if ($userSec) {
                        $sub->orWhere('department', 'like', "%{$userSec}%");
                    }
                });

                if ($user->isHrOrAdmin()) {
                    $q->orWhereDoesntHave('signatures', function($sq) {
                        $sq->where('role', 'hr');
                    });
                }
            });
        }

        $pendingProbations = $pendingProbationsQuery->orderBy('id', 'desc')->get();

        // 3. Pending Interview Evaluation Approvals/Signatures
        $pendingInterviewsQuery = \App\Models\InterviewEvaluation::query()
            ->where(function($q) use ($user, $userFullName) {
                $q->whereNull('hr_evaluator_name')
                  ->orWhereNull('dept_evaluator_name')
                  ->orWhere('hr_evaluator_name', '')
                  ->orWhere('dept_evaluator_name', '');
            });

        if ($user && $user->role !== 'admin') {
            $userDept = $user->department ? $user->department->department_name : '';
            $pendingInterviewsQuery->where(function($q) use ($user, $userFullName, $userDept) {
                if ($user->isHrOrAdmin()) {
                    $q->whereNull('hr_evaluator_name')->orWhere('hr_evaluator_name', '');
                } else {
                    $q->where(function($sub) use ($userFullName, $userDept) {
                        $sub->whereNull('dept_evaluator_name')
                            ->orWhere('dept_evaluator_name', '')
                            ->orWhere('dept_evaluator_name', 'like', "%{$userFullName}%");
                        if ($userDept) {
                            $sub->orWhere('department', 'like', "%{$userDept}%");
                        }
                    });
                }
            });
        }

        $pendingInterviews = $pendingInterviewsQuery->orderBy('id', 'desc')->get();

        $manpowerQuery = $this->scopedManpowerQuery();
        $manpowerRequests = (clone $manpowerQuery)->orderBy('id', 'desc')->get();
        $totalMpr = $manpowerRequests->count();
        $pendingMpr = $manpowerRequests->whereNotIn('status', ['approved', 'rejected', 'draft'])->count();
        $approvedMpr = $manpowerRequests->where('status', 'approved')->count();
        $rejectedMpr = $manpowerRequests->where('status', 'rejected')->count();

        $probationQuery = $this->scopedProbationQuery();
        $probationEvaluations = (clone $probationQuery)->orderBy('id', 'desc')->get();
        $totalPro = $probationEvaluations->count();
        $pendingPro = $probationEvaluations->whereNotIn('status', ['approved', 'rejected'])->count();
        $approvedPro = $probationEvaluations->where('status', 'approved')->count();
        $rejectedPro = $probationEvaluations->where('status', 'rejected')->count();

        return view('manpower-request.index', compact(
            'pendingApprovals', 'pendingProbations', 'pendingInterviews',
            'manpowerRequests', 'totalMpr', 'pendingMpr', 'approvedMpr', 'rejectedMpr',
            'probationEvaluations', 'totalPro', 'pendingPro', 'approvedPro', 'rejectedPro'
        ));
    }

    /**
     * DataTables server-side source for the current user's manpower requests.
     */
    public function dataTableRequests(Request $request)
    {
        $query = $this->scopedManpowerQuery();

        $statusFilter = $request->input('status_filter');
        if ($statusFilter === 'pending') {
            $query->whereNotIn('status', ['approved', 'rejected', 'draft']);
        } elseif (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        $recordsTotal = (clone $query)->count();

        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('department', 'like', "%{$searchValue}%")
                  ->orWhere('section', 'like', "%{$searchValue}%")
                  ->orWhere('job_title_th', 'like', "%{$searchValue}%")
                  ->orWhere('job_title_en', 'like', "%{$searchValue}%")
                  ->orWhere('hire_type', 'like', "%{$searchValue}%")
                  ->orWhere('status', 'like', "%{$searchValue}%");
            });
        }
        $recordsFiltered = (clone $query)->count();

        $columns = ['id', 'date', 'department', 'job_title_th', 'hire_type', 'status', 'id'];
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $query->orderBy($orderColumn, $orderDir);

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $rows = $query->get()->map(function ($req) {
            return [
                'id' => $req->id,
                'date' => \Carbon\Carbon::parse($req->date)->format('d/m/Y'),
                'department' => e($req->department) . ' / ' . e($req->section),
                'job_title_th' => e($req->job_title_th),
                'hire_type' => e($req->hire_type),
                'status' => $req->status,
                'status_label' => $this->manpowerStatusLabel($req->status),
                'show_url' => route('manpower-request.show', $req->id),
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    /**
     * DataTables server-side source for the current user's probation evaluations.
     */
    public function dataTableProbations(Request $request)
    {
        $query = $this->scopedProbationQuery();

        $statusFilter = $request->input('status_filter');
        if ($statusFilter === 'pending') {
            $query->whereNotIn('status', ['approved', 'rejected']);
        } elseif (!empty($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        $recordsTotal = (clone $query)->count();

        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('employee_name', 'like', "%{$searchValue}%")
                  ->orWhere('position', 'like', "%{$searchValue}%")
                  ->orWhere('department', 'like', "%{$searchValue}%")
                  ->orWhere('status', 'like', "%{$searchValue}%");
            });
        }
        $recordsFiltered = (clone $query)->count();

        $columns = ['id', 'start_date', 'employee_name', 'position', 'department', 'status', 'id'];
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $query->orderBy($orderColumn, $orderDir);

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $rows = $query->get()->map(function ($prob) {
            return [
                'id' => $prob->id,
                'start_date' => $prob->start_date ? \Carbon\Carbon::parse($prob->start_date)->format('d/m/Y') : '-',
                'employee_name' => e(($prob->prefix ?? '') . ($prob->employee_name ?? 'ไม่ระบุ')),
                'position' => e($prob->position ?? '-'),
                'department' => e($prob->department ?? '-'),
                'status' => $prob->status,
                'status_label' => $this->probationStatusLabel($prob->status),
                'show_url' => route('probation-evaluation.show', $prob->id),
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    /**
     * Manpower requests visible to the current user: their own, plus any where
     * they are named as the manager/VP approver, unless they hold an admin-tier role.
     */
    private function scopedManpowerQuery()
    {
        $user = auth()->user();
        $canApproveAll = $user ? ($user->isHrOrAdmin() || (method_exists($user, 'hasRole') && $user->hasRole(['admin', 'hr_manager', 'ceo']))) : false;

        $query = ManpowerRequest::query();
        if (!$canApproveAll && $user) {
            $userFullName = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));
            $query->where(function ($q) use ($userFullName) {
                $q->where('user_id', auth()->id())
                  ->orWhere('manager_name', $userFullName)
                  ->orWhere('vp_name', $userFullName);
            });
        }

        return $query;
    }

    /**
     * Probation evaluations visible to the current user: their own, plus any where
     * they are a named evaluator/manager/hr signatory, unless they hold an admin-tier role.
     */
    private function scopedProbationQuery()
    {
        $user = auth()->user();
        $canApproveAll = $user ? ($user->isHrOrAdmin() || (method_exists($user, 'hasRole') && $user->hasRole(['admin', 'hr_manager', 'ceo']))) : false;

        $query = \App\Models\ProbationEvaluation::query();
        if (!$canApproveAll && $user) {
            $userFullName = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));
            $query->where(function ($q) use ($userFullName) {
                $q->where('user_id', auth()->id())
                  ->orWhereHas('signatures', function ($sq) use ($userFullName) {
                      $sq->whereIn('role', ['evaluator', 'manager', 'hr'])
                         ->where('name', $userFullName);
                  });
            });
        }

        return $query;
    }

    private function manpowerStatusLabel($status)
    {
        $map = [
            'pending_manager' => ['badge-blue', 'รอ ผจก.แผนก'],
            'pending_vp' => ['badge-purple', 'รอ ปธ.สายงาน'],
            'pending_hr' => ['badge-yellow', 'รอ ผจก.HR'],
            'pending_ceo' => ['badge-yellow', 'รอ CEO'],
            'approved' => ['badge-green', 'อนุมัติแล้ว'],
            'rejected' => ['badge-red', 'ไม่อนุมัติ'],
        ];
        [$class, $label] = $map[$status] ?? ['badge-gray', $status];
        return '<span class="badge ' . $class . '">' . e($label) . '</span>';
    }

    private function probationStatusLabel($status)
    {
        $map = [
            'draft' => ['badge-gray', 'แบบร่าง'],
            'pending' => ['badge-blue', 'รออนุมัติ'],
            'approved' => ['badge-green', 'อนุมัติแล้ว'],
            'rejected' => ['badge-red', 'ไม่อนุมัติ'],
        ];
        [$class, $label] = $map[$status] ?? ['badge-gray', $status];
        return '<span class="badge ' . $class . '">' . e($label) . '</span>';
    }

    public function show($id)
    {
        $user = auth()->user();
        $canApproveAll = $user ? ($user->isHrOrAdmin() || (method_exists($user, 'hasRole') && $user->hasRole(['admin', 'hr_manager', 'ceo']))) : false;

        $query = ManpowerRequest::where('id', $id);
        if (!$canApproveAll && $user) {
            $userFullName = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));
            $userName = trim($user->firstname ?? '');
            $query->where(function ($q) use ($user, $userFullName, $userName) {
                $q->where('user_id', auth()->id())
                  ->orWhere('manager_name', 'like', "%{$userName}%")
                  ->orWhere('manager_name', 'like', "%{$userFullName}%")
                  ->orWhere('vp_name', 'like', "%{$userName}%")
                  ->orWhere('vp_name', 'like', "%{$userFullName}%");
            });
        }

        $manpowerRequest = $query->firstOrFail();
        return view('manpower-request.show', compact('manpowerRequest'));
    }

    public function exportPdf($id)
    {
        $manpowerRequest = \App\Models\ManpowerRequest::where('user_id', auth()->id())->findOrFail($id);
        
        $pdf = Pdf::loadView('admin.manpower-requests.pdf', compact('manpowerRequest'));
        $pdf->setOption(['isRemoteEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('manpower_request_' . $manpowerRequest->id . '.pdf');
    }

    public function create()
    {
        return view('manpower-request.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'date' => 'required|date',
            'department' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'job_title_th' => 'required|string|max:255',
            'job_title_en' => 'required|string|max:255',
            'headcount' => 'required|integer|min:1',
            'current_headcount' => 'required|integer|min:0',
            'expected_start_date' => 'required|date',
            'job_level' => 'required|string',
            'hire_type' => 'required|string',
            'hire_replacement_name' => 'required_if:hire_type,จ้างทดแทน|nullable|string',
            'hire_transfer_name' => 'required_if:hire_type,โอนย้าย|nullable|string',
            'hire_temp_start' => 'required_if:hire_type,จ้างชั่วคราว|nullable|date',
            'hire_temp_end' => 'required_if:hire_type,จ้างชั่วคราว|nullable|date',
            'attachment_org_chart' => 'required_without_all:attachment_jd,attachment_manpower_plan',
            'req_gender' => 'required|string',
            'req_age' => 'required|string',
            'req_education' => 'required|string',
            'req_major' => 'required|string',
            'req_experience' => 'required|string',
            'res_1' => 'required|string|max:255',
            'requester_name' => 'required|string|max:255',
            'requester_date' => 'nullable|date',
            'manager_name' => 'required|string|max:255',
            'manager_date' => 'nullable|date',
            'vp_name' => 'required|string|max:255',
            'vp_date' => 'nullable|date',
        ], [
            'date.required' => 'กรุณาระบุวันที่ยื่นขอ',
            'department.required' => 'กรุณาระบุฝ่าย',
            'section.required' => 'กรุณาระบุแผนก',
            'job_title_th.required' => 'กรุณาระบุชื่อตำแหน่ง (ไทย)',
            'job_title_en.required' => 'กรุณาระบุชื่อตำแหน่ง (อังกฤษ)',
            'headcount.required' => 'กรุณาระบุจำนวน (อัตรา)',
            'headcount.min' => 'จำนวนอัตราต้องมากกว่า 0',
            'current_headcount.required' => 'กรุณาระบุพนักงานทั้งหมด',
            'expected_start_date.required' => 'กรุณาระบุวันที่ต้องการรับเข้าทำงาน',
            'job_level.required' => 'กรุณาระบุระดับตำแหน่ง',
            'hire_type.required' => 'กรุณาระบุลักษณะการว่าจ้าง',
            'hire_replacement_name.required_if' => 'กรุณาระบุชื่อพนักงานที่ต้องการจ้างทดแทน',
            'hire_transfer_name.required_if' => 'กรุณาระบุชื่อพนักงานที่โอนย้าย',
            'hire_temp_start.required_if' => 'กรุณาระบุวันที่เริ่มต้นจ้างชั่วคราว',
            'hire_temp_end.required_if' => 'กรุณาระบุวันที่สิ้นสุดจ้างชั่วคราว',
            'attachment_org_chart.required_without_all' => 'กรุณาระบุเอกสารแนบอย่างน้อย 1 รายการ',
            'req_gender.required' => 'กรุณาระบุเพศ',
            'req_age.required' => 'กรุณาระบุอายุ',
            'req_education.required' => 'กรุณาระบุวุฒิการศึกษา',
            'req_major.required' => 'กรุณาระบุสาขาวิชา',
            'req_experience.required' => 'กรุณาระบุประสบการณ์ทำงาน',
            'res_1.required' => 'กรุณาระบุหน้าที่รับผิดชอบอย่างน้อย 1 ข้อ',
            'requester_name.required' => 'กรุณาระบุชื่อผู้ร้องขอ',
            'manager_name.required' => 'กรุณาระบุชื่อผู้จัดการแผนก/ฝ่าย',
            'vp_name.required' => 'กรุณาระบุชื่อประธานสายงาน',
        ]);

        // Whitelist only requester-submittable fields — never trust $request->all() here,
        // since it would let a requester inject 'status' or '*_approved_by' and self-approve.
        $data = $request->only([
            'date', 'department', 'section', 'job_title_th', 'job_title_en',
            'headcount', 'current_headcount', 'expected_start_date', 'job_level',
            'hire_type', 'hire_replacement_name', 'hire_transfer_name', 'hire_temp_start', 'hire_temp_end',
            'req_gender', 'req_age', 'req_education', 'req_major', 'req_experience', 'req_special', 'req_other',
            'res_1', 'res_2', 'res_3', 'res_4', 'res_5', 'res_6',
            'requester_name', 'requester_date',
            'manager_name', 'manager_date',
            'vp_name', 'vp_date',
        ]);
        $data['user_id'] = auth()->id();

        // Checkboxes return nothing if unchecked, so we handle them explicitly
        $data['attachment_org_chart'] = $request->has('attachment_org_chart') ? 1 : 0;
        $data['attachment_jd'] = $request->has('attachment_jd') ? 1 : 0;
        $data['attachment_manpower_plan'] = $request->has('attachment_manpower_plan') ? 1 : 0;

        $manpowerRequest = \App\Models\ManpowerRequest::create($data);

        // Notify the manager
        try {
            if (!empty($validatedData['manager_name'])) {
                $manager = User::whereRaw("CONCAT(firstname, ' ', lastname) = ?", [$validatedData['manager_name']])->first();
                if ($manager) {
                    $manager->notify(new ManpowerRequestNotification(
                        $manpowerRequest,
                        'มีคำขออัตรากำลังใหม่รอการอนุมัติจากคุณ',
                        route('admin.manpower-requests.show', $manpowerRequest->id)
                    ));
                }
            }
            
            // Notify the requester
            if (auth()->check()) {
                auth()->user()->notify(new ManpowerRequestNotification(
                    $manpowerRequest,
                    'ใบขออนุมัติกำลังคนของคุณถูกส่งเข้าระบบแล้ว (สถานะ: รอ ผจก.แผนกพิจารณา)',
                    route('manpower-request.show', $manpowerRequest->id)
                ));
            }
        } catch (\Throwable $e) {
            // Ignore notification errors if notifications table does not exist in DB
            \Illuminate\Support\Facades\Log::warning('Notification sending skipped: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'บันทึกข้อมูลใบขออนุมัติกำลังคนเรียบร้อยแล้ว');
    }
}
