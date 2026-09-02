<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InterviewEvaluation;
use Illuminate\View\View;
use Illuminate\Support\Facades\Route;

class InterviewEvaluationController extends Controller
{
    public function index(): View
    {
        $evaluations = InterviewEvaluation::orderBy('id', 'desc')->get();
        $totalCount = $evaluations->count();
        $hireCount = $evaluations->where('summary_result', 'hire')->count();
        $reserveCount = $evaluations->where('summary_result', 'reserve')->count();
        $rejectCount = $evaluations->where('summary_result', 'reject')->count();

        return view('backend.interview-evaluations.index', compact('evaluations', 'totalCount', 'hireCount', 'reserveCount', 'rejectCount'));
    }

    public function dataTable(Request $request)
    {
        $query = InterviewEvaluation::query();
        $recordsTotal = (clone $query)->count();

        $resultFilter = $request->input('result_filter');
        if (in_array($resultFilter, ['hire', 'reserve', 'reject'], true)) {
            $query->where('summary_result', $resultFilter);
        }

        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('candidate_name', 'like', "%{$searchValue}%")
                  ->orWhere('position_applied', 'like', "%{$searchValue}%")
                  ->orWhere('department', 'like', "%{$searchValue}%")
                  ->orWhere('division', 'like', "%{$searchValue}%");
            });
        }
        $recordsFiltered = (clone $query)->count();

        $columns = ['evaluation_date', 'candidate_name', 'position_applied', 'department', 'grand_total_score', 'summary_result', 'id'];
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $orderColumn = $columns[$orderColumnIndex] ?? 'evaluation_date';
        $query->orderBy($orderColumn, $orderDir);

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $rows = $query->get()->map(function ($eval) {
            if ($eval->summary_result === 'hire') {
                $resultHtml = '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">ควรว่าจ้าง (30-40 คะแนน)</span>';
            } elseif ($eval->summary_result === 'reserve') {
                $resultHtml = '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium">ควรสำรอง (20-29 คะแนน)</span>';
            } elseif ($eval->summary_result === 'reject') {
                $resultHtml = '<span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full font-medium">ปฏิเสธ (<20 คะแนน)</span>';
            } else {
                $resultHtml = '<span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full font-medium">ไม่ระบุ</span>';
            }

            $actionsHtml = '<div class="flex items-center justify-end gap-2 text-gray-500 dark:text-gray-400">'
                . '<a href="' . (Route::has('interview-evaluation.show') ? route('interview-evaluation.show', $eval->id) : url('/interview-evaluation/show/' . $eval->id)) . '" class="p-2 hover:text-gray-700 dark:hover:text-gray-300 transition-colors" title="ดูรายละเอียด">'
                . '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>'
                . '</a>'
                . '<a href="' . (Route::has('interview-evaluation.pdf') ? route('interview-evaluation.pdf', $eval->id) : url('/interview-evaluation/pdf/' . $eval->id)) . '" target="_blank" class="p-2 hover:text-gray-700 dark:hover:text-gray-300 transition-colors" title="Export PDF">'
                . '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>'
                . '</a>'
                . '</div>';

            return [
                'evaluation_date' => $eval->evaluation_date ? $eval->evaluation_date->format('d/m/Y') : '-',
                'candidate_name' => e($eval->full_candidate_name),
                'position_applied' => e($eval->position_applied ?? '-'),
                'department' => e($eval->department ?? '-'),
                'scores_summary' => '<div class="text-xs font-semibold">1: ' . $eval->total_hr_score . ' | 2: ' . $eval->total_dept_score . ' <br><span class="text-blue-600">เฉลี่ย: ' . number_format($eval->average_score, 1) . '</span></div>',
                'summary_result_label' => $resultHtml,
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
