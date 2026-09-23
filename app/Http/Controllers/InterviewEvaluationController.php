<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InterviewEvaluation;
use App\Models\InterviewEvaluationScore;

class InterviewEvaluationController extends Controller
{
    public static array $topics = [
        1 => 'บุคลิกลักษณะ : พิจารณาภายนอก รูปร่าง หน้าตา กริยา มารยาท การแต่งกายเหมาะสมกับตำแหน่งที่สมัคร',
        2 => 'อุปนิสัย ทัศนคติ : พิจารณาจากลักษณะนิสัยและทัศนคติโดยทั่วไป เช่น คิดบวก,เปิดเผย,ก้าวร้าว,เงียบ,ใจร้อน ฯลฯ',
        3 => 'การสื่อความ : พิจารณาจากความเข้าใจ คลองแคล่วและความชัดเจนในการตอบคำถามหรือซักถาม',
        4 => 'การศึกษา : พิจารณาจากวุฒิการศึกษา การฝึกอบรมเฉพาะด้านที่ตรงหรือเกี่ยวข้องกัน',
        5 => 'ประสบการณ์ : พิจารณาจากประสบการณ์หรือความสามารถที่เป็นประโยชน์ต่อตำแหน่งที่สมัคร',
        6 => 'ความรอบรู้ในงานที่สมัคร : พิจารณาจากความรู้ทางด้านเทคนิค หรือวิธีการที่เกี่ยวข้องกับตำแหน่งงาน',
        7 => 'สติปัญญา : พิจารณาจากไหวพริบ ความคล่องแคล่วในการตอบคำถาม ศักยภาพในการเรียนรู้และพัฒนาได้',
        8 => 'ความมุ่งหมายในชีวิตการทำงาน : พิจารณาจากความตั้งใจที่จะทำงาน การวางแผนชีวิตการทำงานในอนาคต',
        9 => 'มนุษยสัมพันธ์ / สังคม : พิจารณาถึงการใช้เวลาว่าง การทำกิจกรรม การเล่นกีฬา ทั้งระหว่างการศึกษาและทำงาน',
        10 => 'ความเหมาะสมกับตำแหน่งงานนี้ : เป็นผลสรุปจากหัวข้อต่างๆ ข้างต้น',
    ];

    public function create(Request $request)
    {
        $prefillInterview = null;
        $prefillApplication = null;

        if ($request->filled('interview_id')) {
            $prefillInterview = \App\Models\Recruitment\Interview::with([
                'application.applicant',
                'application.jobPost.department.division',
                'interviewers'
            ])->find($request->interview_id);
            $prefillApplication = $prefillInterview?->application;
        } elseif ($request->filled('application_id')) {
            $prefillApplication = \App\Models\Recruitment\Application::with([
                'applicant',
                'jobPost.department.division',
                'interviews.interviewers'
            ])->find($request->application_id);
            $prefillInterview = $prefillApplication?->interviews?->where('status', 'scheduled')->last() 
                ?? $prefillApplication?->interviews?->last();
        }

        return view('interview-evaluation.create', [
            'prefillInterview' => $prefillInterview,
            'prefillApplication' => $prefillApplication,
            'interviewId' => $request->interview_id ?? $prefillInterview?->id,
            'applicationId' => $request->application_id ?? $prefillApplication?->id,
            'returnUrl' => $request->return_url ?? ($prefillApplication ? route('backend.recruitment.applications.show', $prefillApplication->id) : null),
        ]);
    }

    public function store(Request $request)
    {
        $hasDept = !empty($request->input('dept_score')) && count(array_filter($request->input('dept_score'), fn($v) => $v !== null && $v !== ''));
        $hasHr = !empty($request->input('hr_score')) && count(array_filter($request->input('hr_score'), fn($v) => $v !== null && $v !== ''));

        $rules = [
            'interview_id' => 'nullable|integer',
            'application_id' => 'nullable|integer',
            'return_url' => 'nullable|string',

            'evaluation_date' => 'required|date',
            'candidate_prefix' => 'required|string',
            'candidate_name' => 'required|string',
            'position_applied' => 'required|string',
            'department' => 'required|string',
            'division' => 'required|string',
            'interview_times' => 'nullable|integer',

            'remarks' => 'nullable|string',
            'summary_result' => 'nullable|string|in:hire,reserve,reject',

            'hr_evaluator_name' => 'nullable|string',
            'hr_position' => 'nullable|string',
            'hr_signed_date' => 'nullable|date',

            'dept_evaluator_name' => 'nullable|string',
            'dept_position' => 'nullable|string',
            'dept_signed_date' => 'nullable|date',
        ];

        if ($hasDept && !$hasHr) {
            for ($i = 1; $i <= 10; $i++) {
                $rules["dept_score.{$i}"] = 'required|integer|min:1|max:4';
            }
        } else {
            for ($i = 1; $i <= 10; $i++) {
                $rules["hr_score.{$i}"] = 'required|integer|min:1|max:4';
            }
        }

        $messages = [
            'evaluation_date.required' => 'กรุณาระบุข้อมูล',
            'candidate_prefix.required' => 'กรุณาระบุข้อมูล',
            'candidate_name.required' => 'กรุณาระบุข้อมูล',
            'position_applied.required' => 'กรุณาระบุข้อมูล',
            'department.required' => 'กรุณาระบุข้อมูล',
            'division.required' => 'กรุณาระบุข้อมูล',
        ];

        for ($i = 1; $i <= 10; $i++) {
            $messages["hr_score.{$i}.required"] = 'กรุณาระบุข้อมูล';
            $messages["dept_score.{$i}.required"] = 'กรุณาระบุข้อมูล';
        }

        $validated = $request->validate($rules, $messages);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $hrScores = $validated['hr_score'] ?? [];
            $deptScores = $validated['dept_score'] ?? [];

            $totalHr = array_sum(array_filter($hrScores, fn($v) => is_numeric($v)));
            $totalDept = array_sum(array_filter($deptScores, fn($v) => is_numeric($v)));
            $grandTotal = $totalHr + $totalDept;
            $avgScore = round($grandTotal / 2, 2);

            $evaluation = InterviewEvaluation::create([
                'user_id' => auth()->id(),
                'interview_id' => $validated['interview_id'] ?? null,
                'application_id' => $validated['application_id'] ?? null,
                'evaluation_date' => $validated['evaluation_date'] ?? null,
                'candidate_prefix' => $validated['candidate_prefix'] ?? null,
                'candidate_name' => $validated['candidate_name'],
                'position_applied' => $validated['position_applied'] ?? null,
                'department' => $validated['department'] ?? null,
                'division' => $validated['division'] ?? null,
                'interview_times' => $validated['interview_times'] ?? 1,

                'total_hr_score' => $totalHr,
                'total_dept_score' => $totalDept,
                'grand_total_score' => $grandTotal,
                'average_score' => $avgScore,

                'remarks' => $validated['remarks'] ?? null,
                'summary_result' => $validated['summary_result'] ?? null,

                'hr_evaluator_name' => $validated['hr_evaluator_name'] ?? null,
                'hr_position' => $validated['hr_position'] ?? null,
                'hr_signed_date' => $validated['hr_signed_date'] ?? null,

                'dept_evaluator_name' => $validated['dept_evaluator_name'] ?? null,
                'dept_position' => $validated['dept_position'] ?? null,
                'dept_signed_date' => $validated['dept_signed_date'] ?? null,

                'status' => 'completed',
            ]);

            foreach (self::$topics as $itemNo => $topicTitle) {
                $evaluation->scores()->create([
                    'item_no' => $itemNo,
                    'topic_title' => $topicTitle,
                    'hr_score' => isset($hrScores[$itemNo]) && $hrScores[$itemNo] !== '' ? (int)$hrScores[$itemNo] : null,
                    'dept_score' => isset($deptScores[$itemNo]) && $deptScores[$itemNo] !== '' ? (int)$deptScores[$itemNo] : null,
                ]);
            }

            // ซิงค์สถานะการสัมภาษณ์และใบสมัคร (Recruitment Integration)
            $interviewId = $validated['interview_id'] ?? null;
            $applicationId = $validated['application_id'] ?? null;
            $interview = null;

            if ($interviewId) {
                $interview = \App\Models\Recruitment\Interview::find($interviewId);
                if ($interview) {
                    $interview->update(['status' => 'completed']);
                    $applicationId = $applicationId ?: $interview->application_id;
                }
            }

            if ($applicationId) {
                $application = \App\Models\Recruitment\Application::find($applicationId);
                if ($application) {
                    if (in_array($application->status, ['interview', 'interview_scheduled'])) {
                        $application->update(['status' => 'interview_completed']);
                    }

                    $roundNum = $validated['interview_times'] ?? ($interview?->interview_round ?? 1);
                    \App\Models\Recruitment\StatusLog::create([
                        'application_id' => $application->id,
                        'old_status' => $application->status,
                        'new_status' => $application->status,
                        'changed_by' => auth()->id(),
                        'remark' => 'บันทึกแบบประเมินผลการสัมภาษณ์ผู้สมัครงาน (QF-HR-15) รอบที่ ' . $roundNum . ' เรียบร้อยแล้ว (ผลการประเมิน: ' . ($evaluation->summary_result_label ?? '-') . ', คะแนนรวม: ' . $avgScore . '/40)',
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            if (!empty($validated['return_url'])) {
                return redirect($validated['return_url'])->with('success', 'บันทึกแบบประเมินผลการสัมภาษณ์ผู้สมัครงานเรียบร้อยแล้ว');
            }

            if (!empty($applicationId)) {
                return redirect()->route('backend.recruitment.applications.show', $applicationId)->with('success', 'บันทึกแบบประเมินผลการสัมภาษณ์ผู้สมัครงานเรียบร้อยแล้ว');
            }

            return redirect()->route('interview-evaluation.show', $evaluation->id)->with('success', 'บันทึกแบบประเมินผลการสัมภาษณ์เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $user = auth()->user();
        $canApproveAll = $user ? ($user->canAccessBackend() || (method_exists($user, 'isCeo') && $user->isCeo()) || (string)$user->level_user === '9' || (method_exists($user, 'hasRole') && $user->hasRole(['admin', 'hr_manager', 'ceo']))) : false;

        $evaluation = InterviewEvaluation::with('scores')->findOrFail($id);

        if (!$canApproveAll && $user) {
            $userFullName = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));
            $isOwner = ($evaluation->user_id === $user->id);
            $isEvaluator = (!empty($evaluation->hr_evaluator_name) && str_contains($evaluation->hr_evaluator_name, $userFullName)) ||
                           (!empty($evaluation->dept_evaluator_name) && str_contains($evaluation->dept_evaluator_name, $userFullName));
            $hasSharedAccess = \App\Models\FormShare::hasAccess('interview_evaluation', $id, $user);

            if (!$isOwner && !$isEvaluator && !$hasSharedAccess) {
                abort(403, 'คุณไม่มีสิทธิ์เข้าถึงเอกสารนี้ (เอกสารนี้ต้องได้รับการแชร์หรือได้รับสิทธิ์จากผู้มีอำนาจเท่านั้น)');
            }
        }

        $scoresByItem = $evaluation->scores->keyBy('item_no');

        $activeShare = \App\Models\FormShare::with('sender')
            ->where('form_type', 'interview_evaluation')
            ->where('form_id', $id)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderBy('id', 'desc')
            ->first();

        return view('interview-evaluation.show', compact('evaluation', 'scoresByItem', 'activeShare'));
    }

    public function sign(Request $request, $id)
    {
        $evaluation = InterviewEvaluation::findOrFail($id);
        $role = $request->input('role');
        $userName = auth()->user()->firstname . ' ' . auth()->user()->lastname;

        if ($role === 'hr') {
            $evaluation->update([
                'hr_evaluator_name' => $userName,
                'hr_position' => $request->input('position', 'ฝ่ายทรัพยากรบุคคล'),
                'hr_signed_date' => now(),
            ]);
        } elseif ($role === 'dept') {
            $evaluation->update([
                'dept_evaluator_name' => $userName,
                'dept_position' => $request->input('position', 'ต้นสังกัด'),
                'dept_signed_date' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'ลงชื่อแบบประเมินสัมภาษณ์เรียบร้อยแล้ว');
    }

    public function exportPdf($id)
    {
        $user = auth()->user();
        $canApproveAll = $user ? ($user->canAccessBackend() || (method_exists($user, 'isCeo') && $user->isCeo()) || (string)$user->level_user === '9' || (method_exists($user, 'hasRole') && $user->hasRole(['admin', 'hr_manager', 'ceo']))) : false;

        $evaluation = InterviewEvaluation::with('scores')->findOrFail($id);

        if (!$canApproveAll && $user) {
            $userFullName = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));
            $isOwner = ($evaluation->user_id === $user->id);
            $isEvaluator = (!empty($evaluation->hr_evaluator_name) && str_contains($evaluation->hr_evaluator_name, $userFullName)) ||
                           (!empty($evaluation->dept_evaluator_name) && str_contains($evaluation->dept_evaluator_name, $userFullName));
            $hasSharedAccess = \App\Models\FormShare::hasAccess('interview_evaluation', $id, $user);

            if (!$isOwner && !$isEvaluator && !$hasSharedAccess) {
                abort(403, 'คุณไม่มีสิทธิ์ดาวน์โหลดเอกสารนี้');
            }
        }

        $scoresByItem = $evaluation->scores->keyBy('item_no');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('interview-evaluation.pdf', compact('evaluation', 'scoresByItem'));
        $pdf->setOption(['isRemoteEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('interview_evaluation_' . $evaluation->id . '.pdf');
    }

    public function destroy($id)
    {
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => false, 'message' => 'ระบบไม่อนุญาตให้ลบรายการแบบประเมินผลสัมภาษณ์'], 403);
        }

        return redirect()->back()->with('error', 'ระบบไม่อนุญาตให้ลบรายการแบบประเมินผลสัมภาษณ์');
    }
}
