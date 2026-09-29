<?php

namespace App\Http\Controllers\Backend\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Recruitment\RecruitmentMailLog;
use App\Jobs\SendRecruitmentEmailJob;
use Illuminate\Http\Request;

class MailLogController extends Controller
{
    /**
     * Display a listing of recruitment mail logs.
     */
    public function index(Request $request)
    {
        $query = RecruitmentMailLog::with('user')->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by mail type
        if ($request->filled('mail_type')) {
            $query->where('mail_type', $request->mail_type);
        }

        // Filter by channel
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        // Search by keyword (Recipient, Subject, Position, etc.)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('recipient_email', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('payload', 'like', "%{$search}%");
            });
        }

        // Statistics
        $stats = [
            'total' => RecruitmentMailLog::count(),
            'sent' => RecruitmentMailLog::where('status', 'sent')->count(),
            'failed' => RecruitmentMailLog::where('status', 'failed')->count(),
            'queued' => RecruitmentMailLog::where('status', 'queued')->count(),
            'graph' => RecruitmentMailLog::where('channel', 'microsoft_graph')->count(),
            'smtp' => RecruitmentMailLog::where('channel', 'smtp')->count(),
        ];

        $logs = $query->get();

        // Group logs by Position Name (matching Image 1 expandable row layout)
        $groupedLogs = $logs->groupBy(function ($log) {
            return $log->position_name ?: 'ทั่วไป / ระบบทดสอบ';
        })->map(function ($group, $positionName) {
            return (object) [
                'position_name' => $positionName,
                'logs' => $group,
                'total_count' => $group->count(),
                'sent_count' => $group->where('status', 'sent')->count(),
                'failed_count' => $group->where('status', 'failed')->count(),
                'queued_count' => $group->where('status', 'queued')->count(),
                'skipped_count' => $group->where('status', 'skipped')->count(),
                'latest_log' => $group->first(),
                'latest_sent_at' => $group->max('created_at'),
                'channels' => $group->pluck('channel')->unique()->values()->all(),
                'recipients_count' => $group->pluck('recipient_email')->unique()->count(),
                'mail_types_count' => $group->pluck('mail_type')->unique()->count(),
            ];
        })->sortByDesc(function ($group) {
            return $group->latest_sent_at ? $group->latest_sent_at->timestamp : 0;
        })->values();

        return view('backend.recruitment.mail_logs.index', compact('logs', 'groupedLogs', 'stats'));
    }

    /**
     * Retry sending a failed or queued email.
     */
    public function retry(RecruitmentMailLog $log)
    {
        $log->update([
            'status' => 'queued',
            'error_message' => null,
        ]);

        $payload = $log->payload ?? [];
        $htmlContent = $payload['html_content'] ?? '';

        if (empty($htmlContent) && !empty($payload['interview_id'])) {
            $interview = \App\Models\Recruitment\Interview::find($payload['interview_id']);
            if ($interview) {
                $isRescheduled = str_contains($log->mail_type, 'rescheduled');
                if (in_array($log->mail_type, ['interview_scheduled_dept', 'interview_rescheduled_dept'])) {
                    $recipientUser = !empty($payload['recipient_user_id']) ? \App\Models\User::find($payload['recipient_user_id']) : null;
                    $mailable = new \App\Mail\InterviewScheduledDepartmentNotify($interview, $recipientUser, $log->user, $isRescheduled);
                    $htmlContent = $mailable->render();
                } else {
                    $mailable = new \App\Mail\InterviewScheduled($interview, $log->user, $isRescheduled);
                    $htmlContent = $mailable->render();
                }
            }
        }

        if (empty($htmlContent)) {
            $htmlContent = '<p>' . e($log->subject) . '</p>';
        }

        SendRecruitmentEmailJob::dispatchAfterResponse(
            $log->id,
            $log->user_id,
            $log->recipient_email,
            $log->recipient_name,
            $log->subject,
            $htmlContent,
            [],
            $log->mail_type
        );

        return back()->with('success', 'เริ่มดำเนินการส่งอีเมลใหม่อีกครั้งเรียบร้อยแล้ว');
    }
}
