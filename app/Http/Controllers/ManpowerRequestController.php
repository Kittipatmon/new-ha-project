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

        // Build unified "All Form Requests" collection for client-side DataTable
        $allFormRequests = collect();

        // Fetch all shares (including revoked) to identify shared documents
        $formShares = \App\Models\FormShare::with(['sender', 'recipientUser', 'recipientDept'])
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderBy('id', 'desc')
            ->get()
            ->groupBy(function ($s) {
                return $s->form_type . '_' . $s->form_id;
            });

        $user = auth()->user();

        // Helper closure to determine share info for a given form type and id
        $getShareData = function (string $formType, $formId) use ($formShares, $user) {
            $key = $formType . '_' . $formId;
            if (!$formShares->has($key)) {
                return ['is_shared' => false, 'is_revoked' => false, 'shared_by_name' => null, 'shared_to_name' => null, 'shared_at' => null];
            }

            $sharesForRecord = $formShares->get($key);

            // Find the most relevant share: specifically to this user, department, public (0), or any if admin
            $matchedShare = null;
            if ($user) {
                $matchedShare = $sharesForRecord->first(function ($s) use ($user) {
                    return $s->shared_to_user_id == $user->id;
                });
                if (!$matchedShare && !empty($user->dept_id)) {
                    $matchedShare = $sharesForRecord->first(function ($s) use ($user) {
                        return $s->shared_to_dept_id == $user->dept_id;
                    });
                }
                if (!$matchedShare) {
                    $matchedShare = $sharesForRecord->first(function ($s) {
                        return $s->shared_to_user_id === 0;
                    });
                }
            }

            if (!$matchedShare) {
                $matchedShare = $sharesForRecord->first();
            }

            if ($matchedShare) {
                $senderName = 'ไม่ระบุ';
                if ($matchedShare->sender) {
                    $senderName = !empty($matchedShare->sender->firstname)
                        ? $matchedShare->sender->firstname
                        : ($matchedShare->sender->fullname ?? $matchedShare->sender->username ?? 'ผู้ดูแลระบบ');
                }

                $recipientName = 'ทุกคนในระบบ';
                if (!empty($matchedShare->shared_to_user_id) && $matchedShare->recipientUser) {
                    $recipientName = !empty($matchedShare->recipientUser->firstname)
                        ? $matchedShare->recipientUser->firstname
                        : ($matchedShare->recipientUser->fullname ?? $matchedShare->recipientUser->username ?? 'ผู้ใช้งาน');
                } elseif (!empty($matchedShare->shared_to_dept_id) && $matchedShare->recipientDept) {
                    $recipientName = $matchedShare->recipientDept->name ?? 'แผนก';
                }

                $sharedAt = $matchedShare->created_at 
                    ? \Carbon\Carbon::parse($matchedShare->created_at)->format('d/m/Y H:i น.')
                    : '-';

                return [
                    'is_shared' => true,
                    'is_revoked' => !is_null($matchedShare->revoked_at),
                    'shared_by_name' => $senderName,
                    'shared_to_name' => $recipientName,
                    'shared_at' => $sharedAt,
                ];
            }

            return ['is_shared' => false, 'is_revoked' => false, 'shared_by_name' => null, 'shared_to_name' => null, 'shared_at' => null];
        };

        // 1. Manpower Requests
        foreach ($manpowerRequests as $req) {
            $shareInfo = $getShareData('manpower_request', $req->id);
            $allFormRequests->push([
                'form_type' => 'ใบขออนุมัติกำลังคน',
                'type_code' => 'manpower_request',
                'form_badge' => 'badge-blue',
                'id' => $req->id,
                'date' => \Carbon\Carbon::parse($req->date)->format('d/m/Y'),
                'raw_date' => $req->date,
                'details' => e($req->job_title_th) . ' (' . e($req->hire_type) . ')',
                'department' => self::formatDeptSection($req->department, $req->section),
                'department_full' => 'ฝ่าย: ' . ($req->department ?: '-') . ' | แผนก: ' . ($req->section ?: '-'),
                'status' => $req->status,
                'status_label' => $this->manpowerStatusLabel($req->status),
                'show_url' => route('manpower-request.show', $req->id),
                'delete_url' => route('manpower-request.destroy', $req->id),
                'is_shared' => $shareInfo['is_shared'],
                'is_revoked' => $shareInfo['is_revoked'],
                'shared_by_name' => $shareInfo['shared_by_name'],
                'shared_to_name' => $shareInfo['shared_to_name'],
                'shared_at' => $shareInfo['shared_at'],
            ]);
        }

        // 2. Probation Evaluations
        foreach ($probationEvaluations as $prob) {
            $shareInfo = $getShareData('probation_evaluation', $prob->id);
            $allFormRequests->push([
                'form_type' => 'แบบประเมินทดลองงาน',
                'type_code' => 'probation_evaluation',
                'form_badge' => 'badge-green',
                'id' => $prob->id,
                'date' => $prob->start_date ? \Carbon\Carbon::parse($prob->start_date)->format('d/m/Y') : '-',
                'raw_date' => $prob->start_date ?? '1970-01-01',
                'details' => e(($prob->prefix ?? '') . ($prob->employee_name ?? 'ไม่ระบุ')) . ' - ' . e($prob->position ?? '-'),
                'department' => self::formatDeptSection($prob->department),
                'department_full' => 'แผนก: ' . ($prob->department ?: '-'),
                'status' => $prob->status,
                'status_label' => $this->probationStatusLabel($prob->status),
                'show_url' => route('probation-evaluation.show', $prob->id),
                'delete_url' => route('probation-evaluation.destroy', $prob->id),
                'is_shared' => $shareInfo['is_shared'],
                'is_revoked' => $shareInfo['is_revoked'],
                'shared_by_name' => $shareInfo['shared_by_name'],
                'shared_to_name' => $shareInfo['shared_to_name'],
                'shared_at' => $shareInfo['shared_at'],
            ]);
        }

        // 3. Interview Evaluations
        try {
            $allInterviews = $this->scopedInterviewQuery()->orderBy('id', 'desc')->get();
            foreach ($allInterviews as $int) {
                $isComplete = !empty($int->hr_evaluator_name) && !empty($int->dept_evaluator_name);
                $shareInfo = $getShareData('interview_evaluation', $int->id);
                $allFormRequests->push([
                    'form_type' => 'แบบประเมินผลสัมภาษณ์',
                    'type_code' => 'interview_evaluation',
                    'form_badge' => 'badge-purple',
                    'id' => $int->id,
                    'date' => $int->evaluation_date ? \Carbon\Carbon::parse($int->evaluation_date)->format('d/m/Y') : '-',
                    'raw_date' => $int->evaluation_date ?? '1970-01-01',
                    'details' => e(($int->candidate_prefix ?? '') . ($int->candidate_name ?? 'ไม่ระบุ')) . ' - ' . e($int->position_applied ?? '-'),
                    'department' => self::formatDeptSection($int->department, $int->division),
                    'department_full' => 'แผนก: ' . ($int->department ?: '-') . ' | ฝ่าย: ' . ($int->division ?: '-'),
                    'status' => $isComplete ? 'approved' : 'pending',
                    'status_label' => $isComplete
                        ? '<span class="badge badge-green">ลงนามครบถ้วน</span>'
                        : '<span class="badge badge-purple">รอลงนาม</span>',
                    'show_url' => route('interview-evaluation.show', $int->id),
                    'delete_url' => route('interview-evaluation.destroy', $int->id),
                    'is_shared' => $shareInfo['is_shared'],
                    'is_revoked' => $shareInfo['is_revoked'],
                    'shared_by_name' => $shareInfo['shared_by_name'],
                    'shared_to_name' => $shareInfo['shared_to_name'],
                    'shared_at' => $shareInfo['shared_at'],
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('allFormRequests: Interview query failed: ' . $e->getMessage());
        }

        // Sort by raw_date descending (newest first)
        $allFormRequests = $allFormRequests->sortByDesc('raw_date')->values();

        return view('manpower-request.index', compact(
            'pendingApprovals', 'pendingProbations', 'pendingInterviews',
            'manpowerRequests', 'totalMpr', 'pendingMpr', 'approvedMpr', 'rejectedMpr',
            'probationEvaluations', 'totalPro', 'pendingPro', 'approvedPro', 'rejectedPro',
            'allFormRequests'
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
     * DataTables server-side source for all form types combined.
     */
    public function dataTableAllRequests(Request $request)
    {
        $statusFilter = $request->input('status_filter');

        // 1. Manpower Requests
        $mprs = collect();
        try {
            $mprQuery = $this->scopedManpowerQuery();
            if ($statusFilter === 'pending') {
                $mprQuery->whereNotIn('status', ['approved', 'rejected', 'draft']);
            } elseif (!empty($statusFilter)) {
                $mprQuery->where('status', $statusFilter);
            }
            $mprs = $mprQuery->get()->map(function ($req) {
                return [
                    'form_type' => 'ใบขออนุมัติกำลังคน',
                    'form_badge' => 'badge-blue',
                    'id' => $req->id,
                    'date' => \Carbon\Carbon::parse($req->date)->format('d/m/Y'),
                    'raw_date' => $req->date,
                    'details' => e($req->job_title_th) . ' (' . e($req->hire_type) . ')',
                    'department' => e($req->department) . ' / ' . e($req->section),
                    'status' => $req->status,
                    'status_label' => $this->manpowerStatusLabel($req->status),
                    'show_url' => route('manpower-request.show', $req->id),
                    'delete_url' => route('manpower-request.destroy', $req->id),
                ];
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('dataTableAllRequests: Manpower query failed: ' . $e->getMessage());
        }

        // 2. Probation Evaluations
        $pros = collect();
        try {
            $proQuery = $this->scopedProbationQuery();
            if ($statusFilter === 'pending') {
                $proQuery->whereNotIn('status', ['approved', 'rejected']);
            } elseif (!empty($statusFilter)) {
                $proQuery->where('status', $statusFilter);
            }
            $pros = $proQuery->get()->map(function ($prob) {
                return [
                    'form_type' => 'แบบประเมินทดลองงาน',
                    'form_badge' => 'badge-green',
                    'id' => $prob->id,
                    'date' => $prob->start_date ? \Carbon\Carbon::parse($prob->start_date)->format('d/m/Y') : '-',
                    'raw_date' => $prob->start_date ?? '1970-01-01',
                    'details' => e(($prob->prefix ?? '') . ($prob->employee_name ?? 'ไม่ระบุ')) . ' - ' . e($prob->position ?? '-'),
                    'department' => e($prob->department ?? '-'),
                    'status' => $prob->status,
                    'status_label' => $this->probationStatusLabel($prob->status),
                    'show_url' => route('probation-evaluation.show', $prob->id),
                    'delete_url' => route('probation-evaluation.destroy', $prob->id),
                ];
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('dataTableAllRequests: Probation query failed: ' . $e->getMessage());
        }

        // 3. Interview Evaluations
        $ints = collect();
        try {
            $intQuery = $this->scopedInterviewQuery();
            if ($statusFilter === 'pending') {
                $intQuery->where(function($q) {
                    $q->whereNull('hr_evaluator_name')->orWhereNull('dept_evaluator_name')
                      ->orWhere('hr_evaluator_name', '')->orWhere('dept_evaluator_name', '');
                });
            } elseif ($statusFilter === 'approved') {
                $intQuery->whereNotNull('hr_evaluator_name')->where('hr_evaluator_name', '!=', '')
                         ->whereNotNull('dept_evaluator_name')->where('dept_evaluator_name', '!=', '');
            }
            $ints = $intQuery->get()->map(function ($int) {
                $isComplete = !empty($int->hr_evaluator_name) && !empty($int->dept_evaluator_name);
                return [
                    'form_type' => 'แบบประเมินผลสัมภาษณ์',
                    'form_badge' => 'badge-purple',
                    'id' => $int->id,
                    'date' => $int->evaluation_date ? \Carbon\Carbon::parse($int->evaluation_date)->format('d/m/Y') : '-',
                    'raw_date' => $int->evaluation_date ?? '1970-01-01',
                    'details' => e(($int->candidate_prefix ?? '') . ($int->candidate_name ?? 'ไม่ระบุ')) . ' - ' . e($int->position_applied ?? '-'),
                    'department' => e($int->department ?? '-') . ($int->division ? ' / ' . e($int->division) : ''),
                    'status' => $isComplete ? 'approved' : 'pending',
                    'status_label' => $isComplete 
                        ? '<span class="badge badge-green">ลงนามครบถ้วน</span>' 
                        : '<span class="badge badge-purple">รอลงนาม</span>',
                    'show_url' => route('interview-evaluation.show', $int->id),
                    'delete_url' => route('interview-evaluation.destroy', $int->id),
                ];
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('dataTableAllRequests: Interview query failed: ' . $e->getMessage());
        }

        // 4. HR General Requests (hr_requests table)
        $hrReqs = collect();
        try {
            $hrReqQuery = \App\Models\hrrequest\HrRequests::with(['category', 'type', 'subtype']);
            if ($statusFilter === 'pending') {
                $hrReqQuery->whereIn('status', ['pending', 'approved_manager', 'approved_hr']);
            } elseif (!empty($statusFilter)) {
                $hrReqQuery->where('status', $statusFilter);
            }
            $hrReqs = $hrReqQuery->get()->map(function ($hrReq) {
                // Safely resolve user info (cross-database relation)
                $userName = '-';
                $deptName = '-';
                $secName = '';
                try {
                    $userObj = $hrReq->user;
                    if ($userObj) {
                        $userName = trim($userObj->firstname . ' ' . $userObj->lastname);
                        if ($userObj->department) {
                            $deptName = $userObj->department->department_name ?? '-';
                        }
                        if ($userObj->section) {
                            $secName = $userObj->section->section_name ?? '';
                        }
                    }
                } catch (\Throwable $e) {
                    // Cross-database relation may fail — use fallback
                }

                $catName = 'คำร้องทั่วไป';
                try {
                    if ($hrReq->category) {
                        $catName = $hrReq->category->name_th ?? 'คำร้องทั่วไป';
                    }
                } catch (\Throwable $e) {
                    // Fallback
                }

                $typeName = 'คำร้อง HR';
                try {
                    if ($hrReq->type) {
                        $typeName = $hrReq->type->name_th ?? $hrReq->type->type_name ?? 'คำร้อง HR';
                    }
                } catch (\Throwable $e) {
                    // Fallback
                }

                return [
                    'form_type' => e($catName),
                    'form_badge' => 'badge-yellow',
                    'id' => $hrReq->hr_request_id,
                    'date' => $hrReq->created_at ? \Carbon\Carbon::parse($hrReq->created_at)->format('d/m/Y') : '-',
                    'raw_date' => $hrReq->created_at ? $hrReq->created_at->format('Y-m-d H:i:s') : '1970-01-01',
                    'details' => e($hrReq->title ?: $typeName) . ($userName !== '-' ? ' (' . e($userName) . ')' : ''),
                    'department' => e($deptName) . ($secName ? ' / ' . e($secName) : ''),
                    'status' => $hrReq->status,
                    'status_label' => '<span class="badge ' . ($hrReq->status_color ?? 'badge-gray') . '">' . e($hrReq->status_label ?? $hrReq->status) . '</span>',
                    'show_url' => route('requesthr.dashboard', ['id' => $hrReq->hr_request_id]),
                    'delete_url' => route('requesthr.destroy', $hrReq->hr_request_id),
                ];
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('dataTableAllRequests: HR Requests query failed: ' . $e->getMessage());
        }

        // Combine
        $all = collect();
        $typeFilter = $request->input('type_filter');

        if (empty($typeFilter) || $typeFilter === 'ใบขออนุมัติกำลังคน') {
            $all = $all->concat($mprs);
        }
        if (empty($typeFilter) || $typeFilter === 'แบบประเมินทดลองงาน') {
            $all = $all->concat($pros);
        }
        if (empty($typeFilter) || $typeFilter === 'แบบประเมินผลสัมภาษณ์') {
            $all = $all->concat($ints);
        }
        if (empty($typeFilter) || $typeFilter === 'คำร้องทั่วไป HR') {
            $all = $all->concat($hrReqs);
        }

        // Searching
        $searchValue = mb_strtolower((string)$request->input('search.value'), 'UTF-8');
        if (!empty($searchValue)) {
            $all = $all->filter(function ($item) use ($searchValue) {
                return str_contains(mb_strtolower($item['form_type'], 'UTF-8'), $searchValue) ||
                       str_contains(mb_strtolower($item['details'], 'UTF-8'), $searchValue) ||
                       str_contains(mb_strtolower($item['department'], 'UTF-8'), $searchValue) ||
                       str_contains(mb_strtolower(strip_tags($item['status_label']), 'UTF-8'), $searchValue);
            });
        }

        $recordsTotal = $all->count();
        $recordsFiltered = $all->count();

        // Sorting
        $columns = ['form_type', 'id', 'raw_date', 'details', 'department', 'status'];
        $orderColumnIndex = (int) $request->input('order.0.column', 2);
        $orderDir = $request->input('order.0.dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'raw_date';
        
        $all = $orderDir === 'asc' ? $all->sortBy($orderColumn) : $all->sortByDesc($orderColumn);

        // Pagination
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $rows = $length > 0 ? $all->slice($start, $length)->values() : $all->values();

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    /**
     * Manpower requests visible to the current user: their own, plus any where
     * they are named as manager/VP approver, or where access was explicitly shared.
     */
    private function scopedManpowerQuery()
    {
        $user = auth()->user();
        if (!$user) {
            return ManpowerRequest::whereRaw('1 = 0');
        }

        // Admin, Backend Staff, and CEO have full visibility
        if ($user->canAccessBackend() || (method_exists($user, 'isCeo') && $user->isCeo()) || (string)$user->level_user === '9' || (method_exists($user, 'hasRole') && $user->hasRole(['admin', 'hr_manager', 'ceo']))) {
            return ManpowerRequest::query();
        }

        $userFullName = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));
        $userName = trim($user->firstname ?? '');
        $sharedIds = \App\Models\FormShare::getAccessibleFormIds('manpower_request', $user);

        return ManpowerRequest::query()->where(function ($q) use ($user, $userName, $userFullName, $sharedIds) {
            $q->where('user_id', $user->id);
            if (!empty($userName)) {
                $q->orWhere('manager_name', 'like', "%{$userName}%")
                  ->orWhere('vp_name', 'like', "%{$userName}%");
            }
            if (!empty($userFullName)) {
                $q->orWhere('manager_name', 'like', "%{$userFullName}%")
                  ->orWhere('vp_name', 'like', "%{$userFullName}%");
            }
            if (!empty($sharedIds)) {
                $q->orWhereIn('id', $sharedIds);
            }
            $q->orWhere('manager_approved_by', $user->id)
              ->orWhere('vp_approved_by', $user->id)
              ->orWhere('hr_approved_by', $user->id)
              ->orWhere('ceo_approved_by', $user->id);
        });
    }

    /**
     * Probation evaluations visible to the current user: their own, signed by them,
     * or where access was explicitly shared.
     */
    private function scopedProbationQuery()
    {
        $user = auth()->user();
        if (!$user) {
            return \App\Models\ProbationEvaluation::whereRaw('1 = 0');
        }

        if ($user->canAccessBackend() || (method_exists($user, 'isCeo') && $user->isCeo()) || (string)$user->level_user === '9' || (method_exists($user, 'hasRole') && $user->hasRole(['admin', 'hr_manager', 'ceo']))) {
            return \App\Models\ProbationEvaluation::query();
        }

        $userFullName = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));
        $sharedIds = \App\Models\FormShare::getAccessibleFormIds('probation_evaluation', $user);

        return \App\Models\ProbationEvaluation::query()->where(function ($q) use ($user, $userFullName, $sharedIds) {
            $q->where('user_id', $user->id)
              ->orWhereHas('signatures', function ($sq) use ($userFullName) {
                  $sq->whereIn('role', ['evaluator', 'manager', 'hr'])
                     ->where('name', $userFullName);
              });
            if (!empty($sharedIds)) {
                $q->orWhereIn('id', $sharedIds);
            }
        });
    }

    /**
     * Interview evaluations visible to the current user: their own, signed by them,
     * or where access was explicitly shared.
     */
    private function scopedInterviewQuery()
    {
        $user = auth()->user();
        if (!$user) {
            return \App\Models\InterviewEvaluation::whereRaw('1 = 0');
        }

        if ($user->canAccessBackend() || (method_exists($user, 'isCeo') && $user->isCeo()) || (string)$user->level_user === '9' || (method_exists($user, 'hasRole') && $user->hasRole(['admin', 'hr_manager', 'ceo']))) {
            return \App\Models\InterviewEvaluation::query();
        }

        $userFullName = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));
        $sharedIds = \App\Models\FormShare::getAccessibleFormIds('interview_evaluation', $user);

        return \App\Models\InterviewEvaluation::query()->where(function ($q) use ($user, $userFullName, $sharedIds) {
            $q->where('user_id', $user->id);
            if (!empty($userFullName)) {
                $q->orWhere('hr_evaluator_name', 'like', "%{$userFullName}%")
                  ->orWhere('dept_evaluator_name', 'like', "%{$userFullName}%");
            }
            if (!empty($sharedIds)) {
                $q->orWhereIn('id', $sharedIds);
            }
        });
    }

    /**
     * Check if the user is authorized to view or download a manpower request.
     */
    private function isUserAuthorizedForManpowerRequest(ManpowerRequest $manpowerRequest, $user): bool
    {
        if (!$user) {
            return false;
        }

        // 1. Admin or Editor (Backend role)
        if ($user->canAccessBackend()) {
            return true;
        }

        // 2. Creator
        if ($manpowerRequest->user_id === $user->id) {
            return true;
        }

        // 3. CEO (Level 9 or isCeo())
        if ((method_exists($user, 'isCeo') && $user->isCeo()) || (string)$user->level_user === '9') {
            return true;
        }

        $userFullName = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));
        $userName = trim($user->firstname ?? '');

        // 4. Current approver based on status
        if ($manpowerRequest->status === 'pending_manager') {
            if (empty($manpowerRequest->manager_name) ||
                (!empty($userName) && (str_contains($manpowerRequest->manager_name, $userName) || str_contains($userName, $manpowerRequest->manager_name))) ||
                (!empty($userFullName) && (str_contains($manpowerRequest->manager_name, $userFullName) || str_contains($userFullName, $manpowerRequest->manager_name))) ||
                $user->isDepartmentManager()) {
                return true;
            }
        } elseif ($manpowerRequest->status === 'pending_vp') {
            if (empty($manpowerRequest->vp_name) ||
                (!empty($userName) && (str_contains($manpowerRequest->vp_name, $userName) || str_contains($userName, $manpowerRequest->vp_name))) ||
                (!empty($userFullName) && (str_contains($manpowerRequest->vp_name, $userFullName) || str_contains($userFullName, $manpowerRequest->vp_name))) ||
                (int)$user->level_user >= 8) {
                return true;
            }
        } elseif ($manpowerRequest->status === 'pending_hr') {
            if ($user->dept_id == 15 || $user->hr_status == '0' || $user->canAccessBackend()) {
                return true;
            }
        } elseif ($manpowerRequest->status === 'pending_ceo') {
            if ((method_exists($user, 'isCeo') && $user->isCeo()) || (string)$user->level_user === '9') {
                return true;
            }
        }

        // 5. Named in manager_name or vp_name, or in approved/rejected logs
        if ((!empty($manpowerRequest->manager_name) && ((!empty($userName) && str_contains($manpowerRequest->manager_name, $userName)) || (!empty($userFullName) && str_contains($manpowerRequest->manager_name, $userFullName)))) ||
            (!empty($manpowerRequest->vp_name) && ((!empty($userName) && str_contains($manpowerRequest->vp_name, $userName)) || (!empty($userFullName) && str_contains($manpowerRequest->vp_name, $userFullName)))) ||
            $manpowerRequest->manager_approved_by == $user->id ||
            $manpowerRequest->vp_approved_by == $user->id ||
            $manpowerRequest->hr_approved_by == $user->id ||
            $manpowerRequest->ceo_approved_by == $user->id ||
            $manpowerRequest->rejected_by == $user->id) {
            return true;
        }

        // 6. Form shares
        if (\App\Models\FormShare::hasAccess('manpower_request', $manpowerRequest->id, $user)) {
            return true;
        }

        return false;
    }

    private function manpowerStatusLabel($status)
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
        $manpowerRequest = ManpowerRequest::findOrFail($id);

        if (!$this->isUserAuthorizedForManpowerRequest($manpowerRequest, $user)) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงเอกสารนี้ (เอกสารนี้ต้องได้รับการแชร์หรือได้รับสิทธิ์จากผู้มีอำนาจเท่านั้น)');
        }

        // Check if there is an active share for this document to display who shared it and when
        $activeShare = \App\Models\FormShare::with('sender')
            ->where('form_type', 'manpower_request')
            ->where('form_id', $id)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderBy('id', 'desc')
            ->first();

        return view('manpower-request.show', compact('manpowerRequest', 'activeShare'));
    }

    public function exportPdf($id)
    {
        $user = auth()->user();
        $manpowerRequest = \App\Models\ManpowerRequest::findOrFail($id);
        if (!$this->isUserAuthorizedForManpowerRequest($manpowerRequest, $user)) {
            abort(403, 'คุณไม่มีสิทธิ์ดาวน์โหลดเอกสารนี้');
        }
        
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

        // Also sync/create corresponding RecruitmentRequest so it appears in /recruitment/reports
        try {
            $dept = \App\Models\Recruitment\Department::where('department_name', 'like', "%{$manpowerRequest->department}%")
                ->orWhere('department_fullname', 'like', "%{$manpowerRequest->department}%")
                ->orWhere('department_description', 'like', "%{$manpowerRequest->department}%")
                ->first();
            $deptId = $dept ? $dept->department_id : (auth()->user()?->dept_id ?: (\App\Models\Recruitment\Department::first()?->department_id ?? 1));

            $managerUser = !empty($validatedData['manager_name']) 
                ? User::whereRaw("CONCAT(firstname, ' ', lastname) = ?", [$validatedData['manager_name']])->first()
                : null;
            $vpUser = !empty($validatedData['vp_name'])
                ? User::whereRaw("CONCAT(firstname, ' ', lastname) = ?", [$validatedData['vp_name']])->first()
                : null;

            $duties = array_filter([
                $manpowerRequest->res_1,
                $manpowerRequest->res_2,
                $manpowerRequest->res_3,
                $manpowerRequest->res_4,
                $manpowerRequest->res_5,
                $manpowerRequest->res_6,
            ]);

            \App\Models\Recruitment\RecruitmentRequest::create([
                'request_no' => 'REQ-' . strtoupper(\Illuminate\Support\Str::random(8)),
                'department_id' => $deptId,
                'position_name' => $manpowerRequest->job_title_th ?: $manpowerRequest->job_title_en,
                'requested_by' => auth()->id() ?? 0,
                'approver_manager_id' => $managerUser?->id,
                'approver_executive_id' => $vpUser?->id,
                'headcount' => $manpowerRequest->headcount ?? 1,
                'reason' => 'ลักษณะการว่าจ้าง: ' . $manpowerRequest->hire_type . ($manpowerRequest->hire_replacement_name ? ' (ทดแทน: ' . $manpowerRequest->hire_replacement_name . ')' : '') . ($manpowerRequest->hire_transfer_name ? ' (โอนย้าย: ' . $manpowerRequest->hire_transfer_name . ')' : ''),
                'job_description' => "ระดับ: " . ($manpowerRequest->job_level ?? '-') . "\nหน้าที่ความรับผิดชอบ:\n" . implode("\n", $duties),
                'qualification' => "เพศ: " . ($manpowerRequest->req_gender ?? '-') . ", อายุ: " . ($manpowerRequest->req_age ?? '-') . ", วุฒิ: " . ($manpowerRequest->req_education ?? '-') . ", สาขา: " . ($manpowerRequest->req_major ?? '-') . ", ประสบการณ์: " . ($manpowerRequest->req_experience ?? '-') . ($manpowerRequest->req_special ? ", คุณสมบัติพิเศษ: " . $manpowerRequest->req_special : ''),
                'required_start_date' => $manpowerRequest->expected_start_date,
                'status' => 'pending_manager',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('RecruitmentRequest sync failed: ' . $e->getMessage());
        }

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

        return redirect()->route('recruitment.reports')->with('success', 'บันทึกข้อมูลใบขออนุมัติกำลังคนเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => false, 'message' => 'ระบบไม่อนุญาตให้ลบรายการใบขออนุมัติกำลังคน'], 403);
        }

        return redirect()->back()->with('error', 'ระบบไม่อนุญาตให้ลบรายการใบขออนุมัติกำลังคน');
    }

    /**
     * Format Department and Section/Division cleanly:
     * - Abbreviates known long fullnames to short codes (e.g. Information Communication Technology -> ICT)
     * - Deduplicates identical department and section
     */
    public static function formatDeptSection(?string $dept, ?string $sec = null): string
    {
        static $codeMap = null;
        if ($codeMap === null) {
            $codeMap = [];
            try {
                foreach (\App\Models\Division::all() as $d) {
                    if (!empty($d->division_fullname) && !empty($d->division_name) && $d->division_name !== '-') {
                        $codeMap[mb_strtolower(trim($d->division_fullname))] = trim($d->division_name);
                    }
                }
                foreach (\App\Models\Department::all() as $d) {
                    if (!empty($d->department_fullname) && !empty($d->department_name) && $d->department_name !== '-') {
                        $codeMap[mb_strtolower(trim($d->department_fullname))] = trim($d->department_name);
                    }
                }
                foreach (\App\Models\Section::all() as $s) {
                    if (!empty($s->section_name) && !empty($s->section_code) && $s->section_code !== '-') {
                        $codeMap[mb_strtolower(trim($s->section_name))] = trim($s->section_code);
                    }
                }
            } catch (\Throwable $e) {}
        }

        $resolve = function($val) use ($codeMap) {
            if (!$val) return '';
            $trimmed = trim($val);
            $lower = mb_strtolower($trimmed);
            return $codeMap[$lower] ?? $trimmed;
        };

        $c1 = $resolve($dept);
        $c2 = $resolve($sec);

        if ($c1 && $c2) {
            if (mb_strtolower($c1) === mb_strtolower($c2)) {
                return $c1;
            }
            return $c1 . ' / ' . $c2;
        }

        return $c1 ?: ($c2 ?: '-');
    }
}
