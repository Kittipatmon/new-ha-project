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
            'new_application_ha_notification' => 'แจ้งเตือนผู้สมัครใหม่ (HA)',
            'dept_review_notification' => 'ส่งต่อหัวหน้าแผนกพิจารณา',
            'application_received' => 'ยืนยันการรับสมัครงาน',
            'interview_scheduled' => 'นัดหมายสัมภาษณ์งาน',
            'interview_scheduled_dept', 'interview_scheduled_department' => 'แจ้งแผนก/กรรมการสัมภาษณ์',
            'interview_rescheduled' => 'ปรับเวลานัดสัมภาษณ์ (ผู้สมัคร)',
            'interview_rescheduled_dept', 'interview_rescheduled_department' => 'แจ้งแผนกปรับเวลานัดสัมภาษณ์',
            'application_hired', 'hired' => 'แจ้งผลรับเข้าทำงาน',
            'application_rejected', 'rejected' => 'แจ้งผลไม่ผ่านการคัดเลือก',
            'direct_email' => 'อีเมลติดต่อโดยตรง',
            'test' => 'ทดสอบระบบอีเมล',
            default => !empty($this->mail_type) ? ucwords(str_replace('_', ' ', $this->mail_type)) : 'ทั่วไป',
        };
    }

    public function getMailTypeBadgeAttribute(): string
    {
        return match ($this->mail_type) {
            'new_application_ha_notification' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
            'dept_review_notification' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800',
            'application_received' => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/40 dark:text-sky-300 dark:border-sky-800',
            'interview_scheduled' => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800',
            'interview_scheduled_dept', 'interview_scheduled_department' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800',
            'interview_rescheduled', 'interview_rescheduled_dept', 'interview_rescheduled_department' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800',
            'application_hired', 'hired' => 'bg-teal-50 text-teal-700 border-teal-200 dark:bg-teal-950/40 dark:text-teal-300 dark:border-teal-800',
            'application_rejected', 'rejected' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800',
            'direct_email' => 'bg-violet-50 text-violet-700 border-violet-200 dark:bg-violet-950/40 dark:text-violet-300 dark:border-violet-800',
            'test' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
            default => 'bg-slate-50 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        };
    }

    public function getMailTypeIconAttribute(): string
    {
        return match ($this->mail_type) {
            'new_application_ha_notification' => 'fa-solid fa-bell',
            'dept_review_notification' => 'fa-solid fa-clipboard-check',
            'application_received' => 'fa-solid fa-file-invoice',
            'interview_scheduled' => 'fa-solid fa-calendar-days',
            'interview_scheduled_dept', 'interview_scheduled_department' => 'fa-solid fa-users',
            'interview_rescheduled', 'interview_rescheduled_dept', 'interview_rescheduled_department' => 'fa-solid fa-clock-rotate-left',
            'application_hired', 'hired' => 'fa-solid fa-circle-check',
            'application_rejected', 'rejected' => 'fa-solid fa-circle-xmark',
            'direct_email' => 'fa-solid fa-paper-plane',
            'test' => 'fa-solid fa-flask',
            default => 'fa-solid fa-envelope',
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

        // 4. Extract from subject e.g. "ตำแหน่ง test developer (รอบที่ 1)" or "ยืนยันการรับสมัครงาน - นักพัฒนาซอฟต์แวร์"
        if (preg_match('/(?:ตำแหน่ง|รับสมัครงาน\s*-\s*)([^(]+?)(?:\s*\(|\s*\-|$)/u', $this->subject, $matches)) {
            $extracted = trim($matches[1]);
            if (!empty($extracted)) {
                return $extracted;
            }
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
