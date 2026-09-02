<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\ProbationEvaluationNotification;

class ProbationEvaluationController extends Controller
{


    public function create()
    {
        return view('probation-evaluation.create');
    }

    public function show($id)
    {
        $probationEvaluation = $this->authorizedProbationEvaluation($id);
        return view('probation-evaluation.show', compact('probationEvaluation'));
    }

    /**
     * Fetch a probation evaluation the current user is allowed to view:
     * the creator, a named evaluator/manager/hr signatory, or an admin-tier role.
     */
    private function authorizedProbationEvaluation($id)
    {
        $user = auth()->user();
        $canApproveAll = $user->hasRole(['admin', 'hr_manager', 'ceo']);

        $query = \App\Models\ProbationEvaluation::query();
        if (!$canApproveAll) {
            $userFullName = $user->firstname . ' ' . $user->lastname;
            $query->where(function ($q) use ($user, $userFullName) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('signatures', function ($sq) use ($userFullName) {
                      $sq->whereIn('role', ['evaluator', 'manager', 'hr'])
                         ->where('name', $userFullName);
                  });
            });
        }

        return $query->findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'prefix' => 'required|string',
            'employee_name' => 'required|string',
            'position' => 'required|string',
            'emp_code' => 'required|string',
            'department' => 'required|string',
            'start_date' => 'required|date',
            'probation_due_date' => 'required|date',
            
            // 1.1 Absence
            'absence' => 'nullable|array',
            'absence.1.start' => 'nullable|date',
            'absence.1.end' => 'nullable|date',
            'absence.1.business_leave' => 'nullable|numeric',
            'absence.1.sick_leave' => 'nullable|numeric',
            'absence.1.absent' => 'nullable|numeric',
            'absence.1.late_count' => 'nullable|numeric',
            'absence.1.late_mins' => 'nullable|numeric',
            
            // 1.2 Exam
            'exam_passed_round' => 'required|array',
            'exam_passed_round.0' => 'required',
            'exam_passed_round.1' => 'required',
            'exam_passed_round.2' => 'required',
            'exam_passed_round.3' => 'required',
            'exam_passed_round.4' => 'required',
            'exam_passed_round.5' => 'required',
            'exam_passed_round.*' => 'nullable',
            
            'exam_date' => 'required|array',
            'exam_date.0' => 'required|date',
            'exam_date.1' => 'required|date',
            'exam_date.2' => 'required|date',
            'exam_date.3' => 'required|date',
            'exam_date.4' => 'required|date',
            'exam_date.5' => 'required|date',
            'exam_date.*' => 'nullable|date',
            
            'exam_tester' => 'required|array',
            'exam_tester.0' => 'required|string',
            'exam_tester.1' => 'required|string',
            'exam_tester.2' => 'required|string',
            'exam_tester.3' => 'required|string',
            'exam_tester.4' => 'required|string',
            'exam_tester.5' => 'required|string',
            'exam_tester.*' => 'nullable|string',
            
            'exam_other_topic' => 'nullable|string',
            
            // 1.3 Performance
            'tasks_assigned' => 'required|string',
            'performance_result' => 'required|string',
            'performance_level' => 'required|string',
            'evaluation_result' => 'required|string',
            'evaluation_result_reason' => 'nullable|string',
            'evaluator_comment' => 'required|string',
            'hr_comment' => 'nullable|string',
            
            // Round 2
            'tasks_assigned_2' => 'required|string',
            'performance_result_2' => 'required|string',
            'performance_level_2' => 'required|string',
            'evaluation_result_2' => 'required|string',
            'evaluation_result_reason_2' => 'nullable|string',
            'evaluator_comment_2' => 'required|string',
            'hr_comment_2' => 'nullable|string',
            
            // Round 3
            'tasks_assigned_3' => 'required|string',
            'performance_result_3' => 'required|string',
            'performance_level_3' => 'required|string',
            'evaluation_result_3' => 'required|string',
            'evaluation_result_reason_3' => 'nullable|string',
            'evaluator_comment_3' => 'required|string',
            'hr_comment_3' => 'nullable|string',
            
            // Signatures
            'evaluatee_name' => 'required|string',
            'evaluator_name' => 'required|string',
            'manager_name' => 'required|string',
            'hr_name' => 'nullable|string',
            
            // Signatures Round 2
            'evaluatee_name_2' => 'required|string',
            'evaluator_name_2' => 'required|string',
            'manager_name_2' => 'required|string',
            'hr_name_2' => 'nullable|string',
            
            // Signatures Round 3
            'evaluatee_name_3' => 'required|string',
            'evaluator_name_3' => 'required|string',
            'manager_name_3' => 'required|string',
            'hr_name_3' => 'nullable|string',
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $evaluation = \App\Models\ProbationEvaluation::create([
                'user_id' => auth()->id(),
                'prefix' => $validated['prefix'] ?? null,
                'employee_name' => $validated['employee_name'],
                'position' => $validated['position'] ?? null,
                'emp_code' => $validated['emp_code'] ?? null,
                'department' => $validated['department'] ?? null,
                'start_date' => $validated['start_date'] ?? null,
                'probation_due_date' => $validated['probation_due_date'] ?? null,
                'tasks_assigned' => $validated['tasks_assigned'] ?? null,
                'performance_result' => $validated['performance_result'] ?? null,
                'performance_level' => $validated['performance_level'] ?? null,
                'evaluation_result' => $validated['evaluation_result'] ?? null,
                'evaluation_result_reason' => $validated['evaluation_result_reason'] ?? null,
                'evaluator_comment' => $validated['evaluator_comment'] ?? null,
                'hr_comment' => $validated['hr_comment'] ?? null,
                // Round 2
                'tasks_assigned_2' => $validated['tasks_assigned_2'] ?? null,
                'performance_result_2' => $validated['performance_result_2'] ?? null,
                'performance_level_2' => $validated['performance_level_2'] ?? null,
                'evaluation_result_2' => $validated['evaluation_result_2'] ?? null,
                'evaluation_result_reason_2' => $validated['evaluation_result_reason_2'] ?? null,
                'evaluator_comment_2' => $validated['evaluator_comment_2'] ?? null,
                'hr_comment_2' => $validated['hr_comment_2'] ?? null,
                // Round 3
                'tasks_assigned_3' => $validated['tasks_assigned_3'] ?? null,
                'performance_result_3' => $validated['performance_result_3'] ?? null,
                'performance_level_3' => $validated['performance_level_3'] ?? null,
                'evaluation_result_3' => $validated['evaluation_result_3'] ?? null,
                'evaluation_result_reason_3' => $validated['evaluation_result_reason_3'] ?? null,
                'evaluator_comment_3' => $validated['evaluator_comment_3'] ?? null,
                'hr_comment_3' => $validated['hr_comment_3'] ?? null,
            ]);

            // 1.1 Absence Records
            if (!empty($validated['absence'])) {
                foreach ($validated['absence'] as $round => $data) {
                    $evaluation->absenceRecords()->create([
                        'round' => $round,
                        'start_date' => $data['start'] ?? null,
                        'end_date' => $data['end'] ?? null,
                        'business_leave' => $data['business_leave'] ?? null,
                        'sick_leave' => $data['sick_leave'] ?? null,
                        'absent' => $data['absent'] ?? null,
                        'late_count' => $data['late_count'] ?? null,
                        'late_mins' => $data['late_mins'] ?? null,
                    ]);
                }
            }

            // 1.2 Exam Results
            $topics = [
                '1. การใช้งานคอมพิวเตอร์และ IT เบื้องต้น',
                '2. กฎระเบียบของบริษัท',
                '3. ทิศทางการบริหาร นโยบาย วิสัยทัศน์ พันธกิจ ของบริษัท',
                '4. ผลิตภัณฑ์ของบริษัท',
                '5. การทำ Creating Shared Value (CSV) ของบริษัท',
                '6. การทำ 5 ส',
                '7. อื่นๆ ' . ($validated['exam_other_topic'] ?? '')
            ];

            foreach ($topics as $index => $topic) {
                $evaluation->examResults()->create([
                    'topic' => $topic,
                    'passed_round' => $validated['exam_passed_round'][$index] ?? null,
                    'exam_date' => $validated['exam_date'][$index] ?? null,
                    'exam_tester' => $validated['exam_tester'][$index] ?? null,
                ]);
            }

            // Signatures
            $roles = [
                'evaluatee' => $validated['evaluatee_name'] ?? null,
                'evaluator' => $validated['evaluator_name'] ?? null,
                'manager' => $validated['manager_name'] ?? null,
                'hr' => $validated['hr_name'] ?? null,
            ];

            foreach ($roles as $role => $name) {
                if ($name) {
                    $evaluation->signatures()->create([
                        'role' => $role,
                        'name' => $name,
                        'signed_at' => now(),
                    ]);
                }
            }

            // Notify Evaluator
            try {
                if (!empty($validated['evaluator_name'])) {
                    $evaluator = User::whereRaw("CONCAT(firstname, ' ', lastname) = ?", [$validated['evaluator_name']])->first();
                    if ($evaluator) {
                        $evaluator->notify(new ProbationEvaluationNotification(
                            $evaluation,
                            'มีแบบประเมินทดลองงานใหม่รอการลงนาม/ประเมินจากคุณ',
                            route('admin.probation-evaluations.show', $evaluation->id)
                        ));
                    }
                }
                
                // Notify Creator
                if (auth()->check()) {
                    auth()->user()->notify(new ProbationEvaluationNotification(
                        $evaluation,
                        'แบบประเมินทดลองงานถูกสร้างเรียบร้อยแล้ว',
                        route('probation-evaluation.show', $evaluation->id)
                    ));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Probation Notification sending skipped: ' . $e->getMessage());
            }

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->back()->with('success', 'บันทึกแบบประเมินผลเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    public function sign(Request $request, $id)
    {
        $evaluation = \App\Models\ProbationEvaluation::findOrFail($id);
        $role = $request->input('role');
        $userName = auth()->user()->firstname . ' ' . auth()->user()->lastname;

        if ($role === 'evaluatee') {
            $evaluation->update(['evaluatee_name' => $userName]);
        } elseif ($role === 'evaluator') {
            $evaluation->update(['evaluator_name' => $userName]);
        } elseif ($role === 'manager') {
            $evaluation->update(['manager_name' => $userName]);
        } elseif ($role === 'hr') {
            $evaluation->update(['hr_name' => $userName]);
        }

        $evaluation->signatures()->updateOrCreate(
            ['role' => $role],
            ['name' => $userName, 'signed_at' => now()]
        );

        return redirect()->back()->with('success', 'ลงชื่อเรียบร้อยแล้ว');
    }

    public function exportPdf($id)
    {
        $probationEvaluation = $this->authorizedProbationEvaluation($id);
        $absences = $probationEvaluation->absenceRecords->keyBy('round');
        $exams = $probationEvaluation->examResults;
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.probation-evaluations.pdf', compact('probationEvaluation', 'absences', 'exams'));
        $pdf->setOption(['isRemoteEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('probation_evaluation_' . $probationEvaluation->id . '.pdf');
    }
}
