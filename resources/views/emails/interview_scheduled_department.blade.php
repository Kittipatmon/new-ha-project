@extends('emails.layouts.master', [
    'themeColor' => '#dc2626',
    'titleColor' => '#b91c1c',
    'emailTitle' => !empty($isUpdate) ? 'แจ้งเปลี่ยนแปลงกำหนดการสัมภาษณ์งาน' : 'แจ้งกำหนดการสัมภาษณ์งาน'
])

@section('subject', (!empty($isUpdate) ? '[แจ้งเปลี่ยนแปลงเวลานัดสัมภาษณ์งาน]' : '[นัดสัมภาษณ์งาน]') . " ตำแหน่ง {$positionName} - คุณ{$applicantName} (รอบที่ " . ($interview->interview_round ?? 1) . ")")

@section('content')
    @if(!empty($isUpdate))
        <div class="alert-update" style="background-color: #fff7ed; border: 1.5px solid #fdba74; border-left: 5px solid #ea580c; border-radius: 8px; padding: 14px 18px; margin-bottom: 22px;">
            <div style="color: #c2410c; font-weight: 700; font-size: 15px; margin-bottom: 4px;">
                ⚠️ แจ้งเปลี่ยนแปลงกำหนดการสัมภาษณ์งาน (รอบที่ {{ $interview->interview_round }})
            </div>
            <div style="color: #9a3412; font-size: 13px; line-height: 1.5;">
                ฝ่ายทรัพยากรบุคคล (HA) ได้ปรับปรุงวันและเวลานัดสัมภาษณ์ใหม่ โปรดตรวจสอบและยึดกำหนดการใหม่ตามรายละเอียดด้านล่างนี้
            </div>
        </div>
    @endif

    <p class="greeting">
        เรียน {{ $recipient ? ($recipient->fullname ?? $recipient->name) : 'หัวหน้าแผนก / คณะกรรมการสัมภาษณ์' }}
        @if(!empty($recipient?->position))
            <span style="font-size: 13px; font-weight: normal; color: #64748b;">({{ $recipient->position }})</span>
        @endif
    </p>

    <div class="message-body">
        @if(!empty($isUpdate))
            <p>
                ฝ่ายทรัพยากรบุคคล (HA) ขอแจ้ง<strong>เปลี่ยนแปลงวันและเวลานัดสัมภาษณ์งาน</strong> สำหรับผู้สมัครในตำแหน่ง <strong>"{{ $positionName }}"</strong> เพื่อโปรดเข้าร่วมการสัมภาษณ์ตามกำหนดการฉบับปรับปรุงดังนี้:
            </p>
        @else
            <p>
                ฝ่ายทรัพยากรบุคคล (HA) ได้ทำการกำหนดวันและเวลานัดสัมภาษณ์งาน สำหรับผู้สมัครในตำแหน่ง <strong>"{{ $positionName }}"</strong> เรียบร้อยแล้ว จึงขอเรียนเชิญเข้าร่วมการสัมภาษณ์ตามกำหนดการดังต่อไปนี้:
            </p>
        @endif

        <div class="highlight-box" style="background-color: #fef2f2; border: 1.5px solid #fecaca; border-left: 5px solid #dc2626; padding: 18px 20px; border-radius: 8px; margin: 20px 0;">
            <span style="display: inline-block; background-color: #fee2e2; color: #b91c1c; font-weight: 700; font-size: 12px; padding: 3px 10px; border-radius: 20px; margin-bottom: 12px;">
                {{ !empty($isUpdate) ? '🔄 กำหนดการนัดสัมภาษณ์ใหม่' : '📋 รายละเอียดการนัดสัมภาษณ์' }} (รอบที่ {{ $interview->interview_round }})
            </span>

            <table class="info-table" style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
                <tr>
                    <td style="padding: 6px 0; width: 140px; color: #64748b; font-weight: 600; vertical-align: top;">ผู้สมัคร:</td>
                    <td style="padding: 6px 0; vertical-align: top;"><strong style="color: #0f172a; font-size: 14.5px;">คุณ{{ $applicantName }}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #64748b; font-weight: 600; vertical-align: top;">ตำแหน่งที่สมัคร:</td>
                    <td style="padding: 6px 0; vertical-align: top; color: #1e293b;">{{ $positionName }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #64748b; font-weight: 600; vertical-align: top;">ฝ่าย/แผนก:</td>
                    <td style="padding: 6px 0; vertical-align: top; color: #1e293b;">
                        {{ $interview->application?->jobPost?->department?->department_fullname ?? $interview->application?->jobPost?->department?->department_name ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #64748b; font-weight: 600; vertical-align: top;">วันที่สัมภาษณ์:</td>
                    <td style="padding: 6px 0; vertical-align: top; color: #dc2626; font-weight: 700;">
                        วัน{{ \Carbon\Carbon::parse($interview->interview_date)->locale('th')->dayName }}ที่ {{ \Carbon\Carbon::parse($interview->interview_date)->format('d') }} {{ \Carbon\Carbon::parse($interview->interview_date)->locale('th')->monthName }} {{ \Carbon\Carbon::parse($interview->interview_date)->format('Y') + 543 }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #64748b; font-weight: 600; vertical-align: top;">เวลาสัมภาษณ์:</td>
                    <td style="padding: 6px 0; vertical-align: top; color: #dc2626; font-weight: 700;">
                        {{ \Carbon\Carbon::parse($interview->interview_time)->format('H:i') }} น.
                    </td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #64748b; font-weight: 600; vertical-align: top;">รูปแบบ:</td>
                    <td style="padding: 6px 0; vertical-align: top; color: #1e293b;">
                        <strong>{{ $interview->interview_type == 'online' ? 'ออนไลน์ (Online Meeting)' : 'On-site / สำนักงานบริษัท' }}</strong>
                    </td>
                </tr>
                @if($interview->location)
                <tr>
                    <td style="padding: 6px 0; color: #64748b; font-weight: 600; vertical-align: top;">สถานที่ / ห้อง:</td>
                    <td style="padding: 6px 0; vertical-align: top; color: #1e293b;">{{ $interview->location }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 6px 0; color: #64748b; font-weight: 600; vertical-align: top;">คณะกรรมการ:</td>
                    <td style="padding: 6px 0; vertical-align: top; color: #1e293b;">
                        @if($interview->interviewers && $interview->interviewers->count() > 0)
                            @foreach($interview->interviewers as $interviewer)
                                <span style="display: inline-block; background-color: #f1f5f9; color: #334155; padding: 2px 8px; border-radius: 4px; margin-right: 4px; margin-bottom: 4px; font-size: 12.5px; border: 1px solid #e2e8f0;">👤 {{ $interviewer->fullname ?? $interviewer->name }}</span>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        @if($interview->meeting_link)
            <div class="meeting-box" style="background-color: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 8px; padding: 18px 20px; margin: 22px 0; text-align: center;">
                <div style="color: #15803d; font-weight: 700; font-size: 15px; margin-bottom: 6px;">
                    📹 ลิ้งค์เข้าห้องประชุมสัมภาษณ์ออนไลน์
                </div>
                <div style="font-size: 13px; color: #4b5563; margin-bottom: 10px;">
                    ท่านสามารถคลิกปุ่มด้านล่างเพื่อเข้าร่วมห้องประชุมสัมภาษณ์ตามวันและเวลาดังกล่าว
                </div>
                <a href="{{ $interview->meeting_link }}" target="_blank" class="btn-meeting"
                    style="display: inline-block; background-color: #16a34a; color: #ffffff !important; font-weight: 700; padding: 12px 26px; border-radius: 8px; text-decoration: none; font-size: 14px; margin-top: 10px;">
                    คลิกเข้าร่วมการประชุม (Join Meeting)
                </a>
                <div style="margin-top: 10px; font-size: 12px; color: #64748b; word-break: break-all;">
                    URL: <a href="{{ $interview->meeting_link }}" target="_blank" style="color: #16a34a;">{{ $interview->meeting_link }}</a>
                </div>
            </div>
        @endif

        @if($interview->note)
            <div class="note-card" style="background-color: #eff6ff; border: 1px solid #dbeafe; border-left: 4px solid #3b82f6; border-radius: 8px; padding: 14px 18px; margin: 20px 0; font-size: 13.5px; color: #1e40af;">
                <strong style="color: #1d4ed8;">📌 ข้อความ / รายละเอียดเพิ่มเติมจากฝ่ายทรัพยากรบุคคล:</strong>
                <div style="margin-top: 6px; color: #334155; white-space: pre-line;">{{ $interview->note }}</div>
            </div>
        @endif

        <div style="text-align: center; margin: 28px 0 16px 0;">
            <a href="{{ route('backend.recruitment.applications.show', $interview->application_id) }}" target="_blank" class="btn-action"
                style="display: inline-block; background-color: #dc2626; color: #ffffff !important; text-decoration: none; font-weight: 700; font-size: 13.5px; padding: 12px 26px; border-radius: 8px;">
                📄 ดูรายละเอียดใบสมัครและประเมินผลในระบบ Kumwell HR &rarr;
            </a>
            <div style="margin-top: 8px; font-size: 12px; color: #94a3b8;">
                (เข้าสู่ระบบเพื่อดูเรซูเม่ ผลคะแนน และบันทึกผลการประเมินการสัมภาษณ์)
            </div>
        </div>
    </div>
@endsection

@section('signature')
    <div class="signature">
        <p style="margin: 0; font-weight: 600; color: #1f2937;">ขอแสดงความนับถือ,</p>
        <p style="margin: 5px 0 0 0; color: #4b5563;">
            <strong>{{ $senderName ?? 'ฝ่ายทรัพยากรบุคคล (Human Resources)' }}</strong><br>
            <span style="font-size: 13px; color: #6b7280;">{{ $senderPosition ?? 'ฝ่ายทรัพยากรบุคคลและบริหารงานกลาง' }}</span><br>
            บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)<br>
            <span style="font-size: 13px; color: #6b7280;">โทร: 02-954-3455</span>
            @if(!empty($senderEmail))
                <br><span style="font-size: 13px; color: #6b7280;">อีเมล: <a href="mailto:{{ $senderEmail }}" style="color: #dc2626; text-decoration: none;">{{ $senderEmail }}</a></span>
            @endif
        </p>
    </div>
@endsection
