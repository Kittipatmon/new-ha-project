<?php

namespace App\Http\Controllers\Backend\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Recruitment\Application;
use App\Models\Recruitment\Interview;
use App\Models\Recruitment\InterviewScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\InterviewScheduled;

class InterviewController extends Controller
{
    /**
     * Store a newly created interview.
     */
    public function store(Request $request, Application $application)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'interview_round' => 'required|integer|min:1',
                'interview_type' => 'required|string',
                'interview_date' => 'required|date',
                'interview_time' => 'required',
                'location' => 'nullable|string',
                'meeting_link' => 'nullable|url',
                'interviewer_ids' => 'required|array|min:1',
                'interviewer_ids.*' => 'exists:userkml2025.userskml,id',
                'note' => 'nullable|string',
            ]);

            $interview = new Interview($validated);
            $interview->application_id = $application->id;
            // Set first interviewer for backward compatibility in the main table
            $interview->interviewer_id = $validated['interviewer_ids'][0];
            $interview->status = 'scheduled';
            $interview->save();

            // Store multiple interviewers
            // Manual sync to avoid cross-connection lock wait timeout
            // because User model uses `userkml2025` connection and DB::beginTransaction uses `mysql`.
            $database = config('database.connections.mysql.database');
            $tableName = "{$database}.recruitment_interview_interviewer";
            
            DB::table($tableName)->where('interview_id', $interview->id)->delete();
            
            $pivotData = [];
            $now = now();
            foreach ($validated['interviewer_ids'] as $userId) {
                $pivotData[] = [
                    'interview_id' => $interview->id,
                    'user_id' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table($tableName)->insert($pivotData);

            // อัปเดตสถานะใบสมัครอัตโนมัติเป็น 'interview' ถ้ายังไม่ได้เปลี่ยน
            $statusesToAutoUpdate = ['new', 'screening'];
            if (in_array($application->status, $statusesToAutoUpdate)) {
                $oldStatus = $application->status;
                $application->update([
                    'status' => 'interview',
                    'screened_by' => Auth::id(),
                    'screened_at' => now(),
                ]);

                \App\Models\Recruitment\StatusLog::create([
                    'application_id' => $application->id,
                    'old_status' => $oldStatus,
                    'new_status' => 'interview',
                    'changed_by' => Auth::id(),
                    'remark' => 'เปลี่ยนสถานะอัตโนมัติเมื่อนัดสัมภาษณ์รอบที่ ' . $validated['interview_round'],
                ]);
            }

            // แจ้งเตือนผู้สมัครทางอีเมล
            if ($application->applicant && $application->applicant->email) {
                try {
                    Mail::to($application->applicant->email)->send(new InterviewScheduled($interview));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send interview invitation email: ' . $e->getMessage());
                    // We don't roll back the DB here because the interview record is still valuable even if mail fails
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'นัดหมายการสัมภาษณ์เรียบร้อยแล้ว และส่งอีเมลแจ้งผู้สมัครแล้ว'
                ]);
            }

            return back()->with('success', 'นัดหมายการสัมภาษณ์เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();

            \Illuminate\Support\Facades\Log::error('Failed to store interview: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified interview status.
     */
    public function updateStatus(Request $request, Interview $interview)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:scheduled,completed,postponed,cancelled,absent',
        ]);

        $interview->update(['status' => $validated['status']]);

        return back()->with('success', 'อัปเดตสถานะการสัมภาษณ์เรียบร้อยแล้ว');
    }

    /**
     * Store interview scores.
     */
    public function storeScores(Request $request, Interview $interview)
    {
        $validated = $request->validate([
            'scores' => 'required|array',
            'scores.*.criteria' => 'required|string',
            'scores.*.score' => 'required|integer|min:0|max:10',
            'scores.*.comment' => 'nullable|string',
        ]);

        foreach ($validated['scores'] as $scoreData) {
            InterviewScore::create([
                'interview_id' => $interview->id,
                'criteria_name' => $scoreData['criteria'],
                'score' => $scoreData['score'],
                'max_score' => 10,
                'comment' => $scoreData['comment'],
            ]);
        }

        $interview->update(['status' => 'completed']);

        return back()->with('success', 'บันทึกคะแนนการสัมภาษณ์เรียบร้อยแล้ว');
    }
}
