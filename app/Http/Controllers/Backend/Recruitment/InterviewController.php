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
use App\Mail\InterviewScheduledDepartmentNotify;
use App\Models\OrgDepartment;
use App\Models\User;

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
                'interview_id' => 'nullable|integer',
                'interview_round' => 'required|integer|min:1',
                'interview_type' => 'required|string',
                'interview_date' => 'required|date',
                'interview_time' => 'required',
                'location' => 'nullable|string',
                'meeting_link' => 'nullable|url',
                'interviewer_ids' => 'required|array|min:1',
                'interviewer_ids.*' => 'exists:userkml2025.employees,id',
                'note' => 'nullable|string',
            ]);

            $isUpdate = false;
            if ($request->filled('interview_id')) {
                $interview = Interview::where('id', $request->interview_id)
                    ->where('application_id', $application->id)
                    ->first();

                if ($interview) {
                    $interview->update($validated);
                    $interview->interviewer_id = $validated['interviewer_ids'][0];
                    $interview->status = 'scheduled';
                    $interview->save();
                    $isUpdate = true;
                }
            }

            if (!$isUpdate) {
                // ตรวจสอบว่ามีรอบสัมภาษณ์ก่อนหน้าที่ยังไม่เสร็จสิ้นหรือไม่ (ต้องสัมภาษณ์และประเมินผลรอบก่อนหน้าให้เสร็จก่อน)
                $uncompletedInterview = Interview::where('application_id', $application->id)
                    ->where('status', 'scheduled')
                    ->whereDoesntHave('evaluation')
                    ->latest('interview_round')
                    ->first();

                if ($uncompletedInterview) {
                    DB::rollBack();
                    return back()->with('error', "ยังไม่สามารถนัดสัมภาษณ์รอบใหม่ได้ กรุณาบันทึกแบบประเมินผลการสัมภาษณ์รอบที่ {$uncompletedInterview->interview_round} ให้เสร็จสิ้นก่อน");
                }

                $interview = new Interview($validated);
                $interview->application_id = $application->id;
                // Set first interviewer for backward compatibility in the main table
                $interview->interviewer_id = $validated['interviewer_ids'][0];
                $interview->status = 'scheduled';
                $interview->save();
            }

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

            // อัปเดตสถานะใบสมัครอัตโนมัติเป็น 'interview_scheduled' (HA กำหนดวันนัดและแจ้งผู้สมัครเรียบร้อย)
            $statusesToUpdateScheduled = ['new', 'screening', 'submitted', 'dept_review', 'interview'];
            if (in_array($application->status, $statusesToUpdateScheduled)) {
                $oldStatus = $application->status;
                $application->update([
                    'status' => 'interview_scheduled',
                ]);

                \App\Models\Recruitment\StatusLog::create([
                    'application_id' => $application->id,
                    'old_status' => $oldStatus,
                    'new_status' => 'interview_scheduled',
                    'changed_by' => Auth::id(),
                    'remark' => ($isUpdate ? 'HA ปรับเวลานัดสัมภาษณ์รอบที่ ' : 'HA กำหนดวันเวลานัดสัมภาษณ์รอบที่ ') . $validated['interview_round'] . ' และแจ้งผู้สมัครเรียบร้อยแล้ว',
                ]);
            } elseif ($isUpdate) {
                \App\Models\Recruitment\StatusLog::create([
                    'application_id' => $application->id,
                    'old_status' => $application->status,
                    'new_status' => $application->status,
                    'changed_by' => Auth::id(),
                    'remark' => 'HA ปรับเวลานัดสัมภาษณ์รอบที่ ' . $validated['interview_round'] . ' เป็นวันที่ ' . \Carbon\Carbon::parse($validated['interview_date'])->format('d/m/Y') . ' เวลา ' . \Carbon\Carbon::parse($validated['interview_time'])->format('H:i') . ' น.',
                ]);
            }

            // 1. แจ้งเตือนผู้สมัครทางอีเมล
            if ($application->applicant && $application->applicant->email) {
                try {
                    $mailable = new InterviewScheduled($interview, Auth::user(), $isUpdate);
                    \App\Services\RecruitmentMailService::queueMailable(
                        $mailable,
                        $application->applicant->email,
                        $application->applicant->full_name,
                        $isUpdate ? 'interview_rescheduled' : 'interview_scheduled',
                        ['interview_id' => $interview->id, 'application_id' => $application->id],
                        Auth::user()
                    );
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to queue interview invitation email to applicant: ' . $e->getMessage());
                    // We don't roll back the DB here because the interview record is still valuable even if mail fails
                }
            }

            // 2. แจ้งเตือนหัวหน้าแผนกที่ส่งเรื่อง และกรรมการสัมภาษณ์ทางอีเมล
            try {
                $interview->loadMissing([
                    'interviewers',
                    'application.jobPost.recruitmentRequest.requester',
                    'application.deptReviewer'
                ]);

                $deptRecipients = collect();

                // 2.1 หัวหน้าแผนกที่ส่งเรื่องขออัตรากำลัง (Requester)
                $requester = $application->jobPost?->recruitmentRequest?->requester;
                if ($requester && !empty($requester->email) && filter_var($requester->email, FILTER_VALIDATE_EMAIL)) {
                    $deptRecipients->put($requester->email, $requester);
                }

                // 2.2 หัวหน้าแผนกผู้ตรวจประเมินก่อนหน้า (Dept Reviewer)
                $deptReviewer = $application->deptReviewer;
                if ($deptReviewer && !empty($deptReviewer->email) && filter_var($deptReviewer->email, FILTER_VALIDATE_EMAIL)) {
                    $deptRecipients->put($deptReviewer->email, $deptReviewer);
                }

                // 2.3 ผู้จัดการแผนกตามโครงสร้างองค์กร (Department Manager)
                $deptId = $application->jobPost?->department_id;
                if ($deptId) {
                    $deptManagerId = OrgDepartment::where('id', $deptId)->value('manager_id');
                    if ($deptManagerId && $manager = User::find($deptManagerId)) {
                        if (!empty($manager->email) && filter_var($manager->email, FILTER_VALIDATE_EMAIL)) {
                            $deptRecipients->put($manager->email, $manager);
                        }
                    }
                }

                // 2.4 คณะกรรมการผู้สัมภาษณ์ที่ได้รับมอบหมายในรอบนี้ (Interviewers)
                foreach ($interview->interviewers as $interviewer) {
                    if (!empty($interviewer->email) && filter_var($interviewer->email, FILTER_VALIDATE_EMAIL)) {
                        $deptRecipients->put($interviewer->email, $interviewer);
                    }
                }

                // ส่งอีเมลแจ้งเตือนไปยังหัวหน้าแผนกและกรรมการแต่ละท่าน
                foreach ($deptRecipients as $recipientEmail => $recipientUser) {
                    // หลีกเลี่ยงการส่งซ้ำไปยังอีเมลของผู้สมัคร
                    if ($recipientEmail === $application->applicant?->email) {
                        continue;
                    }

                    $deptMailable = new InterviewScheduledDepartmentNotify($interview, $recipientUser, Auth::user(), $isUpdate);
                    \App\Services\RecruitmentMailService::queueMailable(
                        $deptMailable,
                        $recipientEmail,
                        $recipientUser->fullname ?? $recipientUser->name,
                        $isUpdate ? 'interview_rescheduled_dept' : 'interview_scheduled_dept',
                        [
                            'interview_id' => $interview->id,
                            'application_id' => $application->id,
                            'recipient_user_id' => $recipientUser->id,
                        ],
                        Auth::user()
                    );
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to queue interview notification to department head: ' . $e->getMessage());
            }

            DB::commit();

            $msg = $isUpdate 
                ? 'ปรับเวลานัดหมายสัมภาษณ์รอบที่ ' . $validated['interview_round'] . ' เรียบร้อยแล้ว และส่งอีเมลแจ้งผู้สมัครรวมถึงหัวหน้าแผนกแล้ว'
                : 'นัดหมายการสัมภาษณ์เรียบร้อยแล้ว และส่งอีเมลแจ้งผู้สมัครรวมถึงหัวหน้าแผนกแล้ว';

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg
                ]);
            }

            return back()->with('success', $msg);
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

        // Update application status to interview_completed if currently in interview stage
        $application = $interview->application;
        if ($application && in_array($application->status, ['interview', 'interview_scheduled'])) {
            $oldStatus = $application->status;
            $application->update([
                'status' => 'interview_completed',
            ]);

            \App\Models\Recruitment\StatusLog::create([
                'application_id' => $application->id,
                'old_status' => $oldStatus,
                'new_status' => 'interview_completed',
                'changed_by' => Auth::id(),
                'remark' => 'บันทึกคะแนนการสัมภาษณ์รอบที่ ' . $interview->interview_round . ' เรียบร้อยแล้ว (รอพิจารณาอนุมัติผ่านการคัดเลือก)',
            ]);
        }

        return back()->with('success', 'บันทึกคะแนนการสัมภาษณ์เรียบร้อยแล้ว');
    }
}
