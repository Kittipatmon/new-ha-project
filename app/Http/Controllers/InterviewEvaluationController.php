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

        // ค้นหาแบบประเมินที่มีอยู่เดิม (ถ้ามี เพื่อนำมาต่อยอดการประเมินแยกส่วน)
        $existingEvaluation = null;
        if ($request->filled('evaluation_id')) {
            $existingEvaluation = InterviewEvaluation::with('scores')->find($request->evaluation_id);
        }
        if (!$existingEvaluation && $prefillInterview) {
            $existingEvaluation = InterviewEvaluation::with('scores')
                ->where('interview_id', $prefillInterview->id)
                ->first();
        }
        if (!$existingEvaluation && $prefillApplication) {
            $round = $request->input('round', $prefillInterview?->interview_round ?? 1);
            $existingEvaluation = InterviewEvaluation::with('scores')
                ->where('application_id', $prefillApplication->id)
                ->where('interview_times', $round)
                ->first();
        }

        $existingScores = $existingEvaluation ? $existingEvaluation->scores->keyBy('item_no') : collect([]);

        // กำหนดสิทธิ์และสถานะขั้นตอนการประเมิน
        $user = auth()->user();
        $userDeptName = $user?->department?->department_name ?: ($user?->department?->department_fullname ?: '');
        $userPos = (string)($user?->position ?? '');
        $isHrDept = ((int)$user?->dept_id === 15 || (int)$user?->department_id === 15 
                    || (int)$user?->dept_id === 14 || (int)$user?->department_id === 14 
                    || (strcasecmp($userDeptName, 'HAM') === 0) 
                    || (strcasecmp($userDeptName, 'HAMS') === 0) 
                    || (strcasecmp($userDeptName, 'HR') === 0) 
                    || ($userDeptName === 'Human Assets Management')
                    || preg_match('/(Human\s*Assets|HAM|HAMS|ฝ่ายทรัพยากรบุคคล|ฝ่ายบุคคล)/ui', $userDeptName));
        $isHrPos = preg_match('/\b(HR|Recruitment)\b|(ทรัพยากรบุคคล|เจ้าหน้าที่บุคคล|สรรหา|human\s*resource)/ui', $userPos);
        $isSystemAdmin = ((int)($user?->level_user ?? -1) === 0 || in_array(strtolower((string)($user?->role ?? '')), ['admin', 'superadmin', 'administrator']));
        $isHrUser = $user && ($isHrDept || $isHrPos || (method_exists($user, 'isCentralHr') && $user->isCentralHr()));

        $targetInterview = $prefillInterview;
        $targetApp = $prefillApplication;
        $isAssignedInterviewer = false;
        if ($targetInterview && $user) {
            if ($targetInterview->interviewer_id == $user->id) {
                $isAssignedInterviewer = true;
            }
            if ($targetInterview->relationLoaded('interviewers') || method_exists($targetInterview, 'interviewers')) {
                try {
                    if ($targetInterview->interviewers && $targetInterview->interviewers->contains('id', $user->id)) {
                        $isAssignedInterviewer = true;
                    }
                } catch (\Throwable $e) {}
            }
        }
        $jobDeptId = $targetApp?->jobPost?->department_id;
        $isSameDepartment = ($user && $jobDeptId && ($user->department_id == $jobDeptId || $user->dept_id == $jobDeptId));
        $isDeptUser = $user && ($isAssignedInterviewer || $isSameDepartment || (!$isHrDept && !$isHrPos));

        $canSignHr = $user && ($isHrUser || $isSystemAdmin);
        $canSignDept = $user && ($isDeptUser || $isSystemAdmin);

        // กำหนดขั้นตอน (Active Phase):
        // 1. ฝ่ายบุคคล (HR) ประเมิน 10 ข้อ และกดลงชื่อก่อน (hr_eval)
        // 2. เมื่อส่งแล้ว สถานะเป็น pending_dept ส่งต่อให้ต้นสังกัดประเมิน 10 ข้อ และกดลงชื่อก่อนส่ง (dept_eval)
        // 3. เสร็จสมบูรณ์ทั้งสองฝ่าย (completed)
        if ($existingEvaluation && $existingEvaluation->status === 'completed') {
            $activePhase = 'completed';
        } elseif ($existingEvaluation && $existingEvaluation->status === 'pending_dept' && !empty($existingEvaluation->hr_evaluator_name)) {
            $activePhase = 'dept_eval';
        } else {
            $activePhase = 'hr_eval';
        }

        // อนุญาตให้ Admin หรือทดสอบ สลับดู/จำลองขั้นตอนได้ผ่าน ?phase=hr หรือ ?phase=dept
        if ($request->has('phase')) {
            $p = $request->get('phase');
            if (in_array($p, ['hr', 'dept', 'completed'])) {
                $activePhase = ($p === 'hr' ? 'hr_eval' : ($p === 'dept' ? 'dept_eval' : 'completed'));
            }
        }

        return view('interview-evaluation.create', [
            'prefillInterview' => $prefillInterview,
            'prefillApplication' => $prefillApplication,
            'existingEvaluation' => $existingEvaluation,
            'existingScores' => $existingScores,
            'interviewId' => $request->interview_id ?? $prefillInterview?->id ?? $existingEvaluation?->interview_id,
            'applicationId' => $request->application_id ?? $prefillApplication?->id ?? $existingEvaluation?->application_id,
            'returnUrl' => $request->return_url ?? ($prefillApplication ? route('backend.recruitment.applications.show', $prefillApplication->id) : null),
            'activePhase' => $activePhase,
            'canSignHr' => $canSignHr,
            'canSignDept' => $canSignDept,
            'isHrUser' => $isHrUser,
            'isDeptUser' => $isDeptUser,
            'isSystemAdmin' => $isSystemAdmin,
        ]);
    }

    public function store(Request $request)
    {
        $evaluationId = $request->input('evaluation_id');
        $existingEvaluation = null;
        if ($evaluationId) {
            $existingEvaluation = InterviewEvaluation::with('scores')->find($evaluationId);
        }
        if (!$existingEvaluation && $request->filled('interview_id')) {
            $existingEvaluation = InterviewEvaluation::with('scores')
                ->where('interview_id', $request->input('interview_id'))
                ->first();
        }
        if (!$existingEvaluation && $request->filled('application_id')) {
            $existingEvaluation = InterviewEvaluation::with('scores')
                ->where('application_id', $request->input('application_id'))
                ->where('interview_times', $request->input('interview_times', 1))
                ->first();
        }

        $existingScores = $existingEvaluation ? $existingEvaluation->scores->keyBy('item_no') : collect([]);

        // กำหนด Active Phase
        $activePhase = $request->input('active_phase');
        if (!$activePhase) {
            if ($existingEvaluation && $existingEvaluation->status === 'pending_dept') {
                $activePhase = 'dept_eval';
            } else {
                $activePhase = 'hr_eval';
            }
        }

        $rules = [
            'evaluation_id' => 'nullable|integer',
            'interview_id' => 'nullable|integer',
            'application_id' => 'nullable|integer',
            'return_url' => 'nullable|string',
            'active_phase' => 'nullable|string|in:hr_eval,dept_eval,completed',

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

        $messages = [
            'evaluation_date.required' => 'กรุณาระบุวันที่ประเมิน',
            'candidate_prefix.required' => 'กรุณาระบุคำนำหน้า',
            'candidate_name.required' => 'กรุณาระบุชื่อ-นามสกุลผู้สมัคร',
            'position_applied.required' => 'กรุณาระบุตำแหน่งที่สมัคร',
            'department.required' => 'กรุณาระบุฝ่าย/แผนก',
            'division.required' => 'กรุณาระบุสายงาน',
        ];

        // ตรวจสอบตามขั้นตอน (Workflow Stage Enforcement)
        if ($activePhase === 'hr_eval') {
            // ฝ่ายบุคคลต้องกดลงชื่อก่อนส่งได้
            $rules['hr_evaluator_name'] = 'required|string|min:2';
            $messages['hr_evaluator_name.required'] = 'ฝ่ายบุคคลต้องกดลงชื่อก่อนที่จะกดส่งแบบประเมินได้';

            // ฝ่ายบุคคลต้องประเมินครบ 10 ข้อ
            for ($i = 1; $i <= 10; $i++) {
                $rules["hr_score.{$i}"] = 'required|integer|min:1|max:4';
                $messages["hr_score.{$i}.required"] = 'กรุณาระบุคะแนนฝ่ายบุคคลข้อ ' . $i;
            }
        } elseif ($activePhase === 'dept_eval') {
            // ต้นสังกัดต้องกดลงชื่อก่อนส่งได้
            $rules['dept_evaluator_name'] = 'required|string|min:2';
            $messages['dept_evaluator_name.required'] = 'ต้นสังกัดต้องกดลงชื่อก่อนที่จะกดส่งแบบประเมินได้';

            // ต้นสังกัดต้องประเมินครบ 10 ข้อ
            for ($i = 1; $i <= 10; $i++) {
                $rules["dept_score.{$i}"] = 'required|integer|min:1|max:4';
                $messages["dept_score.{$i}.required"] = 'กรุณาระบุคะแนนต้นสังกัดข้อ ' . $i;
            }

            // สรุปผลการสัมภาษณ์
            $rules['summary_result'] = 'required|string|in:hire,reserve,reject';
            $messages['summary_result.required'] = 'กรุณาเลือกสรุปผลการสัมภาษณ์ (ควรว่าจ้าง / ควรสำรอง / ปฏิเสธ)';
        }

        $validated = $request->validate($rules, $messages);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $submittedHr = $request->input('hr_score', []);
            $submittedDept = $request->input('dept_score', []);

            $mergedHrScores = [];
            $mergedDeptScores = [];

            // ป้องกันการประเมินในช่องของคนอื่น (Strict Role & Column Isolation)
            for ($i = 1; $i <= 10; $i++) {
                if ($activePhase === 'hr_eval') {
                    // ฝ่ายบุคคลประเมิน: บันทึกเฉพาะคะแนน HR และห้ามบันทึกคะแนนต้นสังกัดในขั้นตอนนี้
                    $mergedHrScores[$i] = isset($submittedHr[$i]) && $submittedHr[$i] !== '' ? (int)$submittedHr[$i] : null;
                    $mergedDeptScores[$i] = isset($existingScores[$i]->dept_score) ? (int)$existingScores[$i]->dept_score : null;
                } elseif ($activePhase === 'dept_eval') {
                    // ต้นสังกัดประเมิน: รักษาคะแนนเดิมของฝ่ายบุคคลไว้ 100% ไม่ให้แก้ไขข้ามช่อง
                    $mergedHrScores[$i] = isset($existingScores[$i]->hr_score) ? (int)$existingScores[$i]->hr_score : null;
                    $mergedDeptScores[$i] = isset($submittedDept[$i]) && $submittedDept[$i] !== '' ? (int)$submittedDept[$i] : null;
                } else {
                    $mergedHrScores[$i] = isset($submittedHr[$i]) && $submittedHr[$i] !== '' ? (int)$submittedHr[$i] : ($existingScores[$i]->hr_score ?? null);
                    $mergedDeptScores[$i] = isset($submittedDept[$i]) && $submittedDept[$i] !== '' ? (int)$submittedDept[$i] : ($existingScores[$i]->dept_score ?? null);
                }
            }

            $validHr = array_filter($mergedHrScores, fn($v) => is_numeric($v));
            $validDept = array_filter($mergedDeptScores, fn($v) => is_numeric($v));

            $totalHr = array_sum($validHr);
            $totalDept = array_sum($validDept);

            if ($activePhase === 'hr_eval') {
                $status = 'pending_dept'; // ค่อยส่งต่อให้ต้นสังกัดประเมินต่อ
                $grandTotal = $totalHr;
                $avgScore = round($totalHr / 2, 2);
            } elseif ($activePhase === 'dept_eval') {
                $status = 'completed'; // ต้นสังกัดประเมินและลงชื่อเสร็จสมบูรณ์
                $grandTotal = $totalHr + $totalDept;
                $avgScore = round($grandTotal / 2, 2);
            } else {
                $grandTotal = $totalHr + $totalDept;
                $avgScore = round($grandTotal / 2, 2);
                $status = ($existingEvaluation ? $existingEvaluation->status : 'draft');
            }

            // จัดการข้อมูลลายเซ็น (ไม่สามารถลงชื่อข้ามช่องได้)
            if ($activePhase === 'hr_eval') {
                $hrName = $validated['hr_evaluator_name'];
                $hrPos = $validated['hr_position'] ?: 'ฝ่ายทรัพยากรบุคคล';
                $hrDate = $validated['hr_signed_date'] ?: now()->format('Y-m-d');

                $deptName = $existingEvaluation?->dept_evaluator_name;
                $deptPos = $existingEvaluation?->dept_position;
                $deptDate = $existingEvaluation?->dept_signed_date;
            } elseif ($activePhase === 'dept_eval') {
                // รักษาลายเซ็นฝ่ายบุคคลเดิมไว้
                $hrName = $existingEvaluation?->hr_evaluator_name ?: ($validated['hr_evaluator_name'] ?? null);
                $hrPos = $existingEvaluation?->hr_position ?: ($validated['hr_position'] ?? null);
                $hrDate = $existingEvaluation?->hr_signed_date ?: ($validated['hr_signed_date'] ?? null);

                $deptName = $validated['dept_evaluator_name'];
                $deptPos = $validated['dept_position'] ?: 'ต้นสังกัด';
                $deptDate = $validated['dept_signed_date'] ?: now()->format('Y-m-d');
            } else {
                $hrName = $validated['hr_evaluator_name'] ?: ($existingEvaluation?->hr_evaluator_name);
                $hrPos = $validated['hr_position'] ?: ($existingEvaluation?->hr_position);
                $hrDate = $validated['hr_signed_date'] ?: ($existingEvaluation?->hr_signed_date);
                $deptName = $validated['dept_evaluator_name'] ?: ($existingEvaluation?->dept_evaluator_name);
                $deptPos = $validated['dept_position'] ?: ($existingEvaluation?->dept_position);
                $deptDate = $validated['dept_signed_date'] ?: ($existingEvaluation?->dept_signed_date);
            }

            $summaryResult = $validated['summary_result'] ?? ($existingEvaluation?->summary_result);
            $remarks = $validated['remarks'] ?? ($existingEvaluation?->remarks);

            $evalData = [
                'user_id' => $existingEvaluation ? $existingEvaluation->user_id : auth()->id(),
                'interview_id' => $validated['interview_id'] ?? $existingEvaluation?->interview_id,
                'application_id' => $validated['application_id'] ?? $existingEvaluation?->application_id,
                'evaluation_date' => $validated['evaluation_date'] ?? $existingEvaluation?->evaluation_date,
                'candidate_prefix' => $validated['candidate_prefix'] ?? $existingEvaluation?->candidate_prefix,
                'candidate_name' => $validated['candidate_name'],
                'position_applied' => $validated['position_applied'] ?? ($existingEvaluation?->position_applied),
                'department' => $validated['department'] ?? ($existingEvaluation?->department),
                'division' => $validated['division'] ?? ($existingEvaluation?->division),
                'interview_times' => $validated['interview_times'] ?? ($existingEvaluation?->interview_times ?? 1),

                'total_hr_score' => $totalHr,
                'total_dept_score' => $totalDept,
                'grand_total_score' => $grandTotal,
                'average_score' => $avgScore,

                'remarks' => $remarks,
                'summary_result' => $summaryResult,

                'hr_evaluator_name' => $hrName,
                'hr_position' => $hrPos,
                'hr_signed_date' => $hrDate,

                'dept_evaluator_name' => $deptName,
                'dept_position' => $deptPos,
                'dept_signed_date' => $deptDate,

                'status' => $status,
            ];

            if ($existingEvaluation) {
                $existingEvaluation->update($evalData);
                $evaluation = $existingEvaluation;
            } else {
                $evaluation = InterviewEvaluation::create($evalData);
            }

            // บันทึกคะแนนแต่ละข้อลงใน interview_evaluation_scores
            foreach (self::$topics as $itemNo => $topicTitle) {
                $evaluation->scores()->updateOrCreate(
                    ['item_no' => $itemNo],
                    [
                        'topic_title' => $topicTitle,
                        'hr_score' => $mergedHrScores[$itemNo],
                        'dept_score' => $mergedDeptScores[$itemNo],
                    ]
                );
            }

            // ซิงค์สถานะการสัมภาษณ์และใบสมัคร (Recruitment Integration)
            $interviewId = $evaluation->interview_id;
            $applicationId = $evaluation->application_id;
            $interview = $interviewId ? \App\Models\Recruitment\Interview::find($interviewId) : null;
            $application = $applicationId ? \App\Models\Recruitment\Application::find($applicationId) : null;

            if ($status === 'completed') {
                if ($interview) {
                    $interview->update(['status' => 'completed']);
                }
                if ($application) {
                    if (in_array($application->status, ['interview', 'interview_scheduled'])) {
                        $application->update(['status' => 'interview_completed']);
                    }

                    $roundNum = $evaluation->interview_times ?? ($interview?->interview_round ?? 1);
                    \App\Models\Recruitment\StatusLog::create([
                        'application_id' => $application->id,
                        'old_status' => $application->status,
                        'new_status' => $application->status,
                        'changed_by' => auth()->id(),
                        'remark' => 'บันทึกแบบประเมินผลการสัมภาษณ์ผู้สมัครงาน (QF-HR-15) รอบที่ ' . $roundNum . ' เสร็จสมบูรณ์ทั้งสองฝ่าย (ผลการประเมิน: ' . ($evaluation->summary_result_label ?? '-') . ', คะแนนรวม: ' . $avgScore . '/40)',
                    ]);
                }
                $successMsg = 'บันทึกแบบประเมินผลการสัมภาษณ์เสร็จสมบูรณ์ทั้งสองฝ่ายเรียบร้อยแล้ว (คะแนนเฉลี่ย: ' . $avgScore . '/40)';
            } elseif ($status === 'pending_dept') {
                if ($application) {
                    $roundNum = $evaluation->interview_times ?? 1;
                    \App\Models\Recruitment\StatusLog::create([
                        'application_id' => $application->id,
                        'old_status' => $application->status,
                        'new_status' => $application->status,
                        'changed_by' => auth()->id(),
                        'remark' => 'ฝ่ายบุคคล (HR) บันทึกคะแนนสัมภาษณ์รอบที่ ' . $roundNum . ' เรียบร้อยแล้ว (' . $totalHr . '/40 คะแนน) — อยู่ระหว่างรอต้นสังกัดประเมินต่อ',
                    ]);
                }
                $successMsg = 'บันทึกคะแนนส่วนของฝ่ายบุคคลเรียบร้อยแล้ว (' . $totalHr . '/40 คะแนน) — รอต้นสังกัดประเมิน';
            } elseif ($status === 'pending_hr') {
                if ($application) {
                    $roundNum = $evaluation->interview_times ?? 1;
                    \App\Models\Recruitment\StatusLog::create([
                        'application_id' => $application->id,
                        'old_status' => $application->status,
                        'new_status' => $application->status,
                        'changed_by' => auth()->id(),
                        'remark' => 'ต้นสังกัดบันทึกคะแนนสัมภาษณ์รอบที่ ' . $roundNum . ' เรียบร้อยแล้ว (' . $totalDept . '/40 คะแนน) — อยู่ระหว่างรอฝ่ายบุคคลประเมินต่อ',
                    ]);
                }
                $successMsg = 'บันทึกคะแนนส่วนของต้นสังกัดเรียบร้อยแล้ว (' . $totalDept . '/40 คะแนน) — รอฝ่ายบุคคลประเมิน';
            } else {
                $successMsg = 'บันทึกร่างแบบประเมินเรียบร้อยแล้ว';
            }

            \Illuminate\Support\Facades\DB::commit();

            if (!empty($validated['return_url'])) {
                return redirect($validated['return_url'])->with('success', $successMsg);
            }

            if (!empty($applicationId)) {
                return redirect()->route('backend.recruitment.applications.show', $applicationId)->with('success', $successMsg);
            }

            return redirect()->route('interview-evaluation.show', $evaluation->id)->with('success', $successMsg);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $user = auth()->user();
        $canApproveAll = $user ? ($user->canAccessBackend() || (method_exists($user, 'isCeo') && $user->isCeo()) || (string)$user->level_user === '9' || (method_exists($user, 'hasRole') && $user->hasRole(['admin', 'hr_manager', 'ceo']))) : false;

        $evaluation = InterviewEvaluation::with(['scores', 'interview.interviewers', 'application.jobPost.department'])->findOrFail($id);

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

        // Calculate signatory permissions based on user position and department
        $userDeptName = $user?->department?->department_name ?: ($user?->department?->department_fullname ?: '');
        $isHrDept = ((int)$user?->dept_id === 15 || (int)$user?->department_id === 15 
                    || (strcasecmp($userDeptName, 'HAM') === 0) 
                    || (strcasecmp($userDeptName, 'HR') === 0) 
                    || ($userDeptName === 'Human Assets Management')
                    || preg_match('/(ฝ่ายทรัพยากรบุคคล|ฝ่ายบุคคล)/ui', $userDeptName));

        $isHrPos = preg_match('/\b(HR|Recruitment)\b|(ทรัพยากรบุคคล|เจ้าหน้าที่บุคคล|สรรหา|human\s*resource)/ui', $userPos);
        $isSystemAdmin = ((int)($user?->level_user ?? -1) === 0 || in_array(strtolower((string)($user?->role ?? '')), ['admin', 'superadmin', 'administrator']));

        $canSignHr = $user && ($isHrDept || $isHrPos || $isSystemAdmin);

        $isAssignedInterviewer = false;
        if ($evaluation->interview && $user) {
            if ($evaluation->interview->interviewer_id == $user->id) {
                $isAssignedInterviewer = true;
            }
            if ($evaluation->interview->relationLoaded('interviewers') || method_exists($evaluation->interview, 'interviewers')) {
                try {
                    if ($evaluation->interview->interviewers && $evaluation->interview->interviewers->contains('id', $user->id)) {
                        $isAssignedInterviewer = true;
                    }
                } catch (\Throwable $e) {}
            }
        }

        $jobDeptId = $evaluation->application?->jobPost?->department_id;
        $jobDeptName = $evaluation->application?->jobPost?->department?->department_name 
                    ?: ($evaluation->application?->jobPost?->department?->department_fullname ?: '');

        $isSameDept = false;
        if ($user && $jobDeptId && ($user->department_id == $jobDeptId || $user->dept_id == $jobDeptId)) {
            $isSameDept = true;
        }
        if ($user && $jobDeptName && $userDeptName && (strcasecmp($jobDeptName, $userDeptName) === 0 || str_contains($userDeptName, $jobDeptName) || str_contains($jobDeptName, $userDeptName))) {
            $isSameDept = true;
        }

        $canSignDept = $user && ($isAssignedInterviewer || $isSameDept || $isSystemAdmin);
        if (($isHrDept || $isHrPos) && !$isAssignedInterviewer && !$isSystemAdmin) {
            $canSignDept = false;
        }

        return view('interview-evaluation.show', compact('evaluation', 'scoresByItem', 'activeShare', 'canSignHr', 'canSignDept'));
    }

    public function sign(Request $request, $id)
    {
        $evaluation = InterviewEvaluation::with(['interview.interviewers', 'application.jobPost.department'])->findOrFail($id);
        $role = $request->input('role');
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }
        $userName = $user->firstname . ' ' . $user->lastname;

        $userDeptName = $user->department?->department_name ?: ($user->department?->department_fullname ?: '');
        $userPos = (string)($user->position ?? '');
        $isHrDept = ((int)$user->dept_id === 15 || (int)$user->department_id === 15 
                    || (strcasecmp($userDeptName, 'HAM') === 0) 
                    || (strcasecmp($userDeptName, 'HR') === 0) 
                    || ($userDeptName === 'Human Assets Management')
                    || preg_match('/(ฝ่ายทรัพยากรบุคคล|ฝ่ายบุคคล)/ui', $userDeptName));

        $isHrPos = preg_match('/\b(HR|Recruitment)\b|(ทรัพยากรบุคคล|เจ้าหน้าที่บุคคล|สรรหา|human\s*resource)/ui', $userPos);
        $isSystemAdmin = ((int)($user->level_user ?? -1) === 0 || in_array(strtolower((string)($user->role ?? '')), ['admin', 'superadmin', 'administrator']));

        if ($role === 'hr') {
            if (!$isHrDept && !$isHrPos && !$isSystemAdmin) {
                return redirect()->back()->with('error', 'คุณไม่มีสิทธิ์ลงชื่อในส่วนของฝ่ายบุคคล (เนื่องจากตำแหน่งหรือหน่วยงานไม่ได้สังกัดฝ่ายบุคคล)');
            }
            $evaluation->update([
                'hr_evaluator_name' => $userName,
                'hr_position' => $request->input('position', $user->position ?: 'ฝ่ายทรัพยากรบุคคล'),
                'hr_signed_date' => now(),
            ]);
        } elseif ($role === 'dept') {
            $isAssignedInterviewer = false;
            if ($evaluation->interview) {
                if ($evaluation->interview->interviewer_id == $user->id) {
                    $isAssignedInterviewer = true;
                }
                if ($evaluation->interview->relationLoaded('interviewers') || method_exists($evaluation->interview, 'interviewers')) {
                    try {
                        if ($evaluation->interview->interviewers && $evaluation->interview->interviewers->contains('id', $user->id)) {
                            $isAssignedInterviewer = true;
                        }
                    } catch (\Throwable $e) {}
                }
            }

            $jobDeptId = $evaluation->application?->jobPost?->department_id;
            $jobDeptName = $evaluation->application?->jobPost?->department?->department_name 
                        ?: ($evaluation->application?->jobPost?->department?->department_fullname ?: '');

            $isSameDept = false;
            if ($jobDeptId && ($user->department_id == $jobDeptId || $user->dept_id == $jobDeptId)) {
                $isSameDept = true;
            }
            if ($jobDeptName && $userDeptName && (strcasecmp($jobDeptName, $userDeptName) === 0 || str_contains($userDeptName, $jobDeptName) || str_contains($jobDeptName, $userDeptName))) {
                $isSameDept = true;
            }

            if (!$isAssignedInterviewer && !$isSameDept && ($isHrDept || $isHrPos) && !$isSystemAdmin) {
                return redirect()->back()->with('error', 'คุณไม่มีสิทธิ์ลงชื่อในส่วนของต้นสังกัด');
            }

            $evaluation->update([
                'dept_evaluator_name' => $userName,
                'dept_position' => $request->input('position', $user->position ?: 'ต้นสังกัด'),
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
