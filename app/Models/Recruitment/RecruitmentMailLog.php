<?php

namespace App\Models\Recruitment;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RecruitmentMailLog extends Model
{
    protected $connection = 'mysql';
    protected $table = 'recruitment_mail_logs';

    protected $fillable = [
        'user_id',
        'recipient_email',
        'recipient_name',
        'subject',
        'mail_type',
        'channel',
        'status',
        'error_message',
        'payload',
        'sent_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'sent' => 'ส่งสำเร็จ',
            'failed' => 'ส่งล้มเหลว',
            'queued' => 'อยู่ในคิว',
            default => $this->status,
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'sent' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800',
            'failed' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800',
            'queued' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }

    public function getMailTypeLabelAttribute(): string
    {
        return match ($this->mail_type) {
            'interview_scheduled' => 'นัดหมายสัมภาษณ์งาน',
            'interview_scheduled_dept' => 'แจ้งแผนก/กรรมการสัมภาษณ์',
            'interview_rescheduled' => 'ปรับเวลานัดสัมภาษณ์ (ผู้สมัคร)',
            'interview_rescheduled_dept' => 'แจ้งแผนกปรับเวลานัดสัมภาษณ์',
            'application_hired' => 'แจ้งผลรับเข้าทำงาน',
            'application_rejected' => 'แจ้งผลไม่ผ่านการคัดเลือก',
            'direct_email' => 'อีเมลติดต่อโดยตรง',
            'application_received' => 'ยืนยันการรับใบสมัคร',
            default => $this->mail_type,
        };
    }

    public function getChannelLabelAttribute(): string
    {
        return match ($this->channel) {
            'microsoft_graph' => 'Microsoft Graph API',
            'smtp' => 'SMTP',
            default => strtoupper($this->channel),
        };
    }

    /**
     * Get the position name the email relates to
     */
    public function getPositionNameAttribute(): ?string
    {
        $payload = $this->payload ?? [];

        // 1. Check direct key in payload
        if (!empty($payload['position_name'])) {
            return $payload['position_name'];
        }

        // 2. Check application_id
        if (!empty($payload['application_id'])) {
            $application = \App\Models\Recruitment\Application::with('jobPost.jobPosition')->find($payload['application_id']);
            if ($application && $application->jobPost) {
                return $application->jobPost->position_name ?? ($application->jobPost->jobPosition?->position_name ?? $application->jobPost->title);
            }
        }

        // 3. Check interview_id
        if (!empty($payload['interview_id'])) {
            $interview = \App\Models\Recruitment\Interview::with('application.jobPost.jobPosition')->find($payload['interview_id']);
            if ($interview && $interview->application?->jobPost) {
                return $interview->application->jobPost->position_name ?? ($interview->application->jobPost->jobPosition?->position_name ?? $interview->application->jobPost->title);
            }
        }

        // 4. Extract from subject e.g. "ตำแหน่ง test developer (รอบที่ 1)" or "ตำแหน่ง วิศวกร"
        if (preg_match('/ตำแหน่ง\s+([^(\-]+?)(?:\s*\(|\s*\-|$)/u', $this->subject, $matches)) {
            return trim($matches[1]);
        }

        // 5. Lookup applicant latest application by email
        if (!empty($this->recipient_email)) {
            $applicant = \App\Models\Recruitment\Applicant::where('email', $this->recipient_email)->first();
            if ($applicant) {
                $latestApp = $applicant->applications()->with('jobPost.jobPosition')->latest()->first();
                if ($latestApp && $latestApp->jobPost) {
                    return $latestApp->jobPost->position_name ?? ($latestApp->jobPost->jobPosition?->position_name ?? $latestApp->jobPost->title);
                }
            }
        }

        return null;
    }
}
