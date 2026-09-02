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

    public function create()
    {
        return view('interview-evaluation.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'evaluation_date' => 'nullable|date',
            'candidate_prefix' => 'nullable|string',
            'candidate_name' => 'required|string',
            'position_applied' => 'nullable|string',
            'department' => 'nullable|string',
            'division' => 'nullable|string',
            'interview_times' => 'nullable|integer',

            'hr_score' => 'nullable|array',
            'hr_score.*' => 'nullable|integer|min:1|max:4',
            'dept_score' => 'nullable|array',
            'dept_score.*' => 'nullable|integer|min:1|max:4',

            'remarks' => 'nullable|string',
            'summary_result' => 'nullable|string|in:hire,reserve,reject',

            'hr_evaluator_name' => 'nullable|string',
            'hr_position' => 'nullable|string',
            'hr_signed_date' => 'nullable|date',

            'dept_evaluator_name' => 'nullable|string',
            'dept_position' => 'nullable|string',
            'dept_signed_date' => 'nullable|date',
        ]);

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

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->back()->with('success', 'บันทึกแบบประเมินผลการสัมภาษณ์เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $evaluation = InterviewEvaluation::with('scores')->findOrFail($id);
        $scoresByItem = $evaluation->scores->keyBy('item_no');
        return view('interview-evaluation.show', compact('evaluation', 'scoresByItem'));
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
        $evaluation = InterviewEvaluation::with('scores')->findOrFail($id);
        $scoresByItem = $evaluation->scores->keyBy('item_no');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('interview-evaluation.pdf', compact('evaluation', 'scoresByItem'));
        $pdf->setOption(['isRemoteEnabled' => true]);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('interview_evaluation_' . $evaluation->id . '.pdf');
    }
}
