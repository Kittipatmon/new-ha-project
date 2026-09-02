<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProbationEvaluation;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProbationEvaluationController extends Controller
{
    public function index(): View
    {
        $probationEvaluations = ProbationEvaluation::orderBy('id', 'desc')->get();
        $totalPro = $probationEvaluations->count();
        $approvedPro = $probationEvaluations->where('status', 'approved')->count();
        $rejectedPro = $probationEvaluations->where('status', 'rejected')->count();
        $pendingPro = $probationEvaluations->whereNotIn('status', ['approved', 'rejected'])->count();

        return view('backend.probation-evaluations.index', compact('probationEvaluations', 'totalPro', 'approvedPro', 'rejectedPro', 'pendingPro'));
    }

    public function dataTable(Request $request)
    {
        $query = ProbationEvaluation::query();
        $recordsTotal = (clone $query)->count();

        $statusFilter = $request->input('status_filter');
        if ($statusFilter === 'pending') {
            $query->whereNotIn('status', ['approved', 'rejected']);
        } elseif (in_array($statusFilter, ['approved', 'rejected'], true)) {
            $query->where('status', $statusFilter);
        }

        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('emp_code', 'like', "%{$searchValue}%")
                  ->orWhere('employee_name', 'like', "%{$searchValue}%")
                  ->orWhere('department', 'like', "%{$searchValue}%")
                  ->orWhere('status', 'like', "%{$searchValue}%");
            });
        }
        $recordsFiltered = (clone $query)->count();

        $columns = ['emp_code', 'employee_name', 'department', 'start_date', 'status', 'id'];
        $orderColumnIndex = (int) $request->input('order.0.column', 3);
        $orderDir = $request->input('order.0.dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'start_date';
        $query->orderBy($orderColumn, $orderDir);

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $rows = $query->get()->map(function ($eval) {
            if ($eval->status === 'approved') {
                $statusHtml = '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">อนุมัติแล้ว</span>';
            } elseif ($eval->status === 'rejected') {
                $statusHtml = '<span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">ไม่อนุมัติ</span>';
            } else {
                $statusHtml = '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">รอพิจารณา</span>';
            }

            $actionsHtml = '<div class="flex items-center justify-end gap-2 text-gray-500 dark:text-gray-400">'
                . '<a href="' . (Route::has('probation-evaluation.show') ? route('probation-evaluation.show', $eval->id) : url('/probation-evaluation/show/' . $eval->id)) . '" class="p-2 hover:text-gray-700 dark:hover:text-gray-300 transition-colors" title="ดูรายละเอียด">'
                . '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>'
                . '</a>'
                . '<a href="' . (Route::has('probation-evaluation.pdf') ? route('probation-evaluation.pdf', $eval->id) : url('/probation-evaluation/pdf/' . $eval->id)) . '" target="_blank" class="p-2 hover:text-gray-700 dark:hover:text-gray-300 transition-colors" title="Export PDF">'
                . '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>'
                . '</a>'
                . '</div>';

            return [
                'emp_code' => e($eval->emp_code ?? '-'),
                'employee_name' => e(($eval->prefix ?? '') . ($eval->employee_name ?? 'ไม่ระบุ')),
                'department' => e($eval->department ?? '-'),
                'start_date' => $eval->start_date ? \Carbon\Carbon::parse($eval->start_date)->format('d/m/Y') : '-',
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
}
