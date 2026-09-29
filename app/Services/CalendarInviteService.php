<?php

namespace App\Services;

use App\Models\Recruitment\Interview;
use Carbon\Carbon;

class CalendarInviteService
{
    /**
     * Generate an RFC 5545 compliant iCalendar (.ics) string for an interview.
     *
     * @param Interview $interview
     * @param string|null $recipientEmail
     * @param string|null $recipientName
     * @param string|null $organizerEmail
     * @param string|null $organizerName
     * @param int $durationMinutes
     * @return string|null
     */
    public static function generateIcs(
        Interview $interview,
        ?string $recipientEmail = null,
        ?string $recipientName = null,
        ?string $organizerEmail = null,
        ?string $organizerName = null,
        int $durationMinutes = 60
    ): ?string {
        if (!$interview->interview_date || !$interview->interview_time) {
            return null;
        }

        try {
            $dateStr = $interview->interview_date instanceof \DateTimeInterface
                ? $interview->interview_date->format('Y-m-d')
                : (string)$interview->interview_date;

            $timeStr = (string)$interview->interview_time;
            // Clean time string (HH:MM or HH:MM:SS)
            $timeParts = explode(':', $timeStr);
            $hours = (int)($timeParts[0] ?? 9);
            $minutes = (int)($timeParts[1] ?? 0);
            $timeFormatted = sprintf('%02d:%02d:00', $hours, $minutes);

            $start = Carbon::createFromFormat('Y-m-d H:i:s', "{$dateStr} {$timeFormatted}", 'Asia/Bangkok');
            $end = (clone $start)->addMinutes($durationMinutes);

            $dtStartUtc = $start->copy()->utc()->format('Ymd\THis\Z');
            $dtEndUtc = $end->copy()->utc()->format('Ymd\THis\Z');
            $dtStampUtc = Carbon::now('UTC')->format('Ymd\THis\Z');

            $positionName = $interview->application?->jobPost?->position_name
                ?? ($interview->application?->jobPost?->jobPosition?->position_name ?? 'Kumwell Corporation');
            $applicant = $interview->application?->applicant;
            $applicantName = $applicant ? trim($applicant->first_name . ' ' . $applicant->last_name) : 'ผู้สมัครงาน';

            $round = $interview->interview_round ?? 1;
            $summary = "สัมภาษณ์งานตำแหน่ง {$positionName} (รอบที่ {$round}) - คุณ{$applicantName}";

            $isOnline = in_array(strtolower((string)$interview->interview_type), ['online', 'teams', 'zoom', 'google_meet']);
            $location = $isOnline
                ? ($interview->meeting_link ?: 'Online Meeting (Microsoft Teams)')
                : ($interview->location ?: 'บมจ. คัมเวล คอร์ปอเรชั่น (Kumwell Corporation)');

            $descriptionLines = [
                "การนัดสัมภาษณ์งาน บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)",
                "--------------------------------------------------",
                "ตำแหน่ง: {$positionName}",
                "ผู้สมัคร: คุณ{$applicantName}",
                "รูปแบบ: " . ($isOnline ? 'สัมภาษณ์ออนไลน์' : 'สัมภาษณ์ที่บริษัท (Onsite)'),
                "สถานที่ / ลิงก์: {$location}",
            ];

            if ($interview->meeting_link) {
                $descriptionLines[] = "Meeting Link: {$interview->meeting_link}";
            }
            if ($interview->note) {
                $descriptionLines[] = "หมายเหตุ: {$interview->note}";
            }
            if ($applicant && $applicant->phone) {
                $descriptionLines[] = "เบอร์ติดต่อผู้สมัคร: {$applicant->phone}";
            }
            $descriptionLines[] = "ติดต่อฝ่ายทรัพยากรบุคคล: recruitment@kumwell.com";

            $description = implode("\n", $descriptionLines);

            // Escape ICS special characters
            $escapeIcs = function ($text) {
                $text = str_replace('\\', '\\\\', $text);
                $text = str_replace(';', '\;', $text);
                $text = str_replace(',', '\,', $text);
                $text = str_replace("\r\n", "\n", $text);
                $text = str_replace("\n", "\\n", $text);
                return $text;
            };

            $escapedSummary = $escapeIcs($summary);
            $escapedLocation = $escapeIcs($location);
            $escapedDescription = $escapeIcs($description);

            $orgEmail = $organizerEmail ?: config('mail.from.address', 'recruitment@kumwell.com');
            $orgName = $organizerName ?: 'Kumwell Recruitment Team';
            $uid = "kumwell-interview-{$interview->id}-" . md5($start->toIso8601String()) . "@kumwell.com";

            $lines = [
                'BEGIN:VCALENDAR',
                'PRODID:-//Kumwell Corporation//Recruitment Portal//TH',
                'VERSION:2.0',
                'CALSCALE:GREGORIAN',
                'METHOD:REQUEST',
                'BEGIN:VEVENT',
                "UID:{$uid}",
                "DTSTAMP:{$dtStampUtc}",
                "DTSTART:{$dtStartUtc}",
                "DTEnd:{$dtEndUtc}",
                "SUMMARY:{$escapedSummary}",
                "DESCRIPTION:{$escapedDescription}",
                "LOCATION:{$escapedLocation}",
                'STATUS:CONFIRMED',
                "ORGANIZER;CN=\"{$orgName}\":mailto:{$orgEmail}",
            ];

            if ($recipientEmail) {
                $recName = $recipientName ?: $recipientEmail;
                $lines[] = "ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;CN=\"{$recName}\":mailto:{$recipientEmail}";
            }

            // Alarm 30 mins before
            $lines[] = 'BEGIN:VALARM';
            $lines[] = 'TRIGGER:-PT30M';
            $lines[] = 'ACTION:DISPLAY';
            $lines[] = 'DESCRIPTION:เตือนนัดหมายสัมภาษณ์งาน Kumwell';
            $lines[] = 'END:VALARM';

            $lines[] = 'END:VEVENT';
            $lines[] = 'END:VCALENDAR';

            return implode("\r\n", $lines) . "\r\n";
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('CalendarInviteService error: ' . $e->getMessage());
            return null;
        }
    }
}
