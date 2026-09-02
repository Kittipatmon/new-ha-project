<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManpowerRequest;
use App\Models\User;
use App\Notifications\ManpowerRequestNotification;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class ManpowerRequestController extends Controller
{
    public function index(): View
    {
        $manpowerRequests = ManpowerRequest::orderBy('id', 'desc')->get();
        $totalMpr = $manpowerRequests->count();
        $pendingMpr = $manpowerRequests->whereNotIn('status', ['approved', 'rejected', 'draft'])->count();
        $approvedMpr = $manpowerRequests->where('status', 'approved')->count();
        $rejectedMpr = $manpowerRequests->where('status', 'rejected')->count();

        return view('backend.manpower-requests.index', compact('manpowerRequests', 'totalMpr', 'pendingMpr', 'approvedMpr', 'rejectedMpr'));
    }

    public function dataTable(Request $request)
    {
        $query = ManpowerRequest::query();
        $recordsTotal = (clone $query)->count();

        $statusFilter = $request->input('status_filter');
        if ($statusFilter === 'pending') {
            $query->whereNotIn('status', ['approved', 'rejected', 'draft']);
        } elseif (in_array($statusFilter, ['approved', 'rejected'], true)) {
            $query->where('status', $statusFilter);
        }

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

        $statusLabels = [
            'pending_manager' => ['bg-blue-100 text-blue-800', 'รอ ผจก.แผนก'],
            'pending_vp' => ['bg-blue-100 text-blue-800', 'รอ ปธ.สายงาน'],
            'pending_hr' => ['bg-blue-100 text-blue-800', 'รอ ผจก.HR'],
            'pending_ceo' => ['bg-blue-100 text-blue-800', 'รอ CEO'],
            'approved' => ['bg-green-100 text-green-800', 'อนุมัติแล้ว'],
            'rejected' => ['bg-red-100 text-red-800', 'ไม่อนุมัติ'],
        ];

        $rows = $query->get()->map(function ($req) use ($statusLabels) {
            [$class, $label] = $statusLabels[$req->status] ?? ['bg-gray-100 text-gray-800', $req->status];
            $statusHtml = '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $class . '">' . e($label) . '</span>';

            $actionsHtml = '<div class="flex items-center justify-end gap-2 text-gray-500 dark:text-gray-400">'
                . '<a href="' . (Route::has('admin.manpower-requests.show') ? route('admin.manpower-requests.show', $req->id) : url('/admin/manpower-requests/' . $req->id)) . '" class="p-2 hover:text-gray-700 dark:hover:text-gray-300 transition-colors" title="ดูรายละเอียด">'
                . '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>'
                . '</a>'
                . '<a href="' . (Route::has('manpower-request.pdf') ? route('manpower-request.pdf', $req->id) : url('/manpower-request/pdf/' . $req->id)) . '" target="_blank" class="p-2 hover:text-gray-700 dark:hover:text-gray-300 transition-colors" title="Export PDF">'
                . '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>'
                . '</a>'
                . '<a href="' . (Route::has('manpower-request.create') ? route('manpower-request.create') : url('/manpower-request/create')) . '" class="p-2 hover:text-gray-700 dark:hover:text-gray-300 transition-colors" title="Edit">'
                . '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>'
                . '</a>'
                . '</div>';

            return [
                'id' => $req->id,
                'date' => \Carbon\Carbon::parse($req->date)->format('d/m/Y'),
                'department' => e($req->department) . ' / ' . e($req->section),
                'job_title_th' => e($req->job_title_th),
                'hire_type' => e($req->hire_type),
                'status_label' => $statusHtml,
                'actions' => $actionsHtml,
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function approve(Request $request, ManpowerRequest $manpowerRequest): RedirectResponse
    {
        $statusFlow = [
            'pending_manager' => 'pending_vp',
            'pending_vp' => 'pending_hr',
            'pending_hr' => 'pending_ceo',
            'pending_ceo' => 'approved',
        ];

        $currentStatus = $manpowerRequest->status;

        if ($currentStatus === 'pending_manager') {
            $manpowerRequest->manager_approved_by = auth()->id();
            $manpowerRequest->manager_approved_at = now();
        } elseif ($currentStatus === 'pending_vp') {
            $manpowerRequest->vp_approved_by = auth()->id();
            $manpowerRequest->vp_approved_at = now();
        } elseif ($currentStatus === 'pending_hr') {
            $manpowerRequest->hr_approved_by = auth()->id();
            $manpowerRequest->hr_approved_at = now();
            if ($request->filled('onboard_employee_code')) {
                $manpowerRequest->onboard_employee_code = $request->input('onboard_employee_code');
                $manpowerRequest->onboard_employee_name = $request->input('onboard_employee_name');
                $manpowerRequest->onboard_date = $request->input('onboard_date');
            }
        } elseif ($currentStatus === 'pending_ceo') {
            $manpowerRequest->ceo_approved_by = auth()->id();
            $manpowerRequest->ceo_approved_at = now();
        }

        if (isset($statusFlow[$currentStatus])) {
            $manpowerRequest->status = $statusFlow[$currentStatus];
            $manpowerRequest->save();
        }

        return redirect()->back()->with('success', 'พิจารณาอนุมัติคำขอเรียบร้อยแล้ว');
    }

    public function show($id): View
    {
        $manpowerRequest = ManpowerRequest::findOrFail($id);
        return view('admin.manpower-requests.show', compact('manpowerRequest'));
    }

    public function reject(Request $request, ManpowerRequest $manpowerRequest): RedirectResponse
    {
        $manpowerRequest->status = 'rejected';
        $manpowerRequest->rejected_by = auth()->id();
        $manpowerRequest->rejected_at = now();
        $manpowerRequest->rejection_reason = $request->input('reason', 'ไม่อนุมัติคำขอ');
        $manpowerRequest->save();

        return redirect()->back()->with('success', 'ไม่อนุมัติคำขอเรียบร้อยแล้ว');
    }
}
