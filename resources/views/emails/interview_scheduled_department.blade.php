<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>แจ้งกำหนดการสัมภาษณ์งาน - Kumwell Recruitment</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Sarabun', sans-serif;
            line-height: 1.6;
            color: #334155;
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
        }
        .container {
            max-width: 650px;
            margin: 24px auto;
            background: #ffffff;
            border-top: 6px solid #dc2626;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
            border-radius: 4px;
            overflow: hidden;
        }
        .header {
            padding: 28px 30px;
            text-align: center;
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
        }
        .header img {
            max-width: 170px;
        }
        .tagline {
            color: #dc2626;
            font-size: 13px;
            font-weight: 600;
            margin-top: 6px;
            letter-spacing: 1px;
        }
        .content {
            padding: 35px 40px;
            background-color: #ffffff;
        }
        .greeting {
            font-size: 17px;
            margin-bottom: 18px;
            color: #0f172a;
            font-weight: 600;
        }
        .main-text {
            margin-bottom: 20px;
            color: #475569;
            font-size: 15px;
        }
        .highlight-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #dc2626;
            padding: 22px;
            margin: 22px 0;
            border-radius: 6px;
        }
        .highlight-title {
            color: #dc2626;
            font-weight: 700;
            margin-bottom: 14px;
            font-size: 16px;
            border-bottom: 1px dashed #e2e8f0;
            padding-bottom: 8px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .info-label {
            width: 160px;
            font-weight: 600;
            color: #64748b;
            flex-shrink: 0;
        }
        .info-value {
            color: #1e293b;
            flex-grow: 1;
        }
        .meeting-box {
            background-color: #f0fdf4;
            border: 1.5px solid #22c55e;
            border-radius: 8px;
            padding: 20px;
            margin: 22px 0;
            text-align: center;
        }
        .btn-meeting {
            display: inline-block;
            background-color: #16a34a;
            color: #ffffff !important;
            font-weight: 600;
            padding: 12px 28px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 15px;
            margin: 10px 0;
            box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.3);
        }
        .btn-view-app {
            display: inline-block;
            background-color: #dc2626;
            color: #ffffff !important;
            font-weight: 600;
            padding: 11px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.25);
        }
        .note-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 14px 18px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
        }
        .interviewers-badge {
            display: inline-block;
            background-color: #f1f5f9;
            color: #334155;
            padding: 2px 8px;
            border-radius: 4px;
            margin-right: 4px;
            margin-bottom: 4px;
            font-size: 13px;
            border: 1px solid #e2e8f0;
        }
        .footer {
            padding: 24px 30px;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            font-size: 13px;
            color: #64748b;
            text-align: center;
        }
        .signature {
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
            font-size: 13px;
            color: #64748b;
        }
        a { color: #dc2626; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(file_exists(public_path('images/logos/th-kumwell-logo.png')))
                <img src="{{ $message->embed(public_path('images/logos/th-kumwell-logo.png')) }}" alt="Kumwell Logo">
            @else
                <h2 style="margin:0; color:#dc2626;">Kumwell Recruitment</h2>
            @endif
            <div class="tagline">POWER OF INNOVATION</div>
        </div>
        
        <div class="content">
            @if(!empty($isUpdate))
                <div style="background-color: #fff7ed; border: 1.5px solid #fdba74; border-left: 5px solid #ea580c; border-radius: 6px; padding: 16px 20px; margin-bottom: 24px;">
                    <div style="color: #c2410c; font-weight: 700; font-size: 16px; margin-bottom: 4px;">
                        ⚠️ แจ้งเปลี่ยนแปลงกำหนดการสัมภาษณ์งาน (รอบที่ {{ $interview->interview_round }})
                    </div>
                    <div style="color: #9a3412; font-size: 13px; line-height: 1.5;">
                        ฝ่ายทรัพยากรบุคคล (HA) ได้ทำการปรับปรุงวันและเวลานัดสัมภาษณ์ใหม่ โปรดตรวจสอบและยึดกำหนดการใหม่ตามรายละเอียดด้านล่างนี้
                    </div>
                </div>
            @endif

            <p class="greeting">
                เรียน {{ $recipient ? ($recipient->fullname ?? $recipient->name) : 'หัวหน้าแผนก / คณะกรรมการสัมภาษณ์' }}
                @if(!empty($recipient?->position))
                    <span style="font-size: 13px; font-weight: normal; color: #64748b;">({{ $recipient->position }})</span>
                @endif
            </p>

            @if(!empty($isUpdate))
                <p class="main-text">
                    ฝ่ายทรัพยากรบุคคล (HA) ขอแจ้ง<strong>เปลี่ยนแปลงวันและเวลานัดสัมภาษณ์งาน</strong> สำหรับผู้สมัครในตำแหน่ง <strong>"{{ $positionName }}"</strong> จึงขอเรียนแจ้งรายละเอียดกำหนดการนัดหมายสัมภาษณ์งานฉบับปรับปรุง เพื่อโปรดเข้าร่วมการสัมภาษณ์ตามกำหนดการดังต่อไปนี้:
                </p>
            @else
                <p class="main-text">
                    ฝ่ายทรัพยากรบุคคล (HA) ได้ทำการกำหนดวันและเวลานัดสัมภาษณ์งาน สำหรับผู้สมัครในตำแหน่ง <strong>"{{ $positionName }}"</strong> เรียบร้อยแล้ว จึงขอเรียนแจ้งรายละเอียดการนัดหมายสัมภาษณ์งาน เพื่อโปรดเข้าร่วมการสัมภาษณ์ตามกำหนดการดังต่อไปนี้:
                </p>
            @endif

            <!-- กล่องสรุปกำหนดการสัมภาษณ์ -->
            <div class="highlight-box">
                <div class="highlight-title">
                    @if(!empty($isUpdate))
                        🔄 รายละเอียดกำหนดการนัดสัมภาษณ์งานใหม่ (รอบที่ {{ $interview->interview_round }})
                    @else
                        📋 รายละเอียดการนัดสัมภาษณ์งาน (รอบที่ {{ $interview->interview_round }})
                    @endif
                </div>
                
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <tr>
                        <td style="padding: 6px 0; width: 140px; color: #64748b; font-weight: 600;">ชื่อ-นามสกุล ผู้สมัคร:</td>
                        <td style="padding: 6px 0; color: #0f172a; font-weight: 700;">คุณ{{ $applicantName }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; font-weight: 600;">ตำแหน่งที่สมัคร:</td>
                        <td style="padding: 6px 0; color: #1e293b;">{{ $positionName }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; font-weight: 600;">แผนก/ฝ่าย:</td>
                        <td style="padding: 6px 0; color: #1e293b;">
                            {{ $interview->application?->jobPost?->department?->department_fullname ?? $interview->application?->jobPost?->department?->department_name ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; font-weight: 600;">วันที่สัมภาษณ์:</td>
                        <td style="padding: 6px 0; color: #dc2626; font-weight: 700;">
                            วัน{{ \Carbon\Carbon::parse($interview->interview_date)->locale('th')->dayName }}ที่ {{ $interview->interview_date->format('d') }} {{ \Carbon\Carbon::parse($interview->interview_date)->locale('th')->monthName }} {{ $interview->interview_date->format('Y') + 543 }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; font-weight: 600;">เวลาสัมภาษณ์:</td>
                        <td style="padding: 6px 0; color: #dc2626; font-weight: 700;">
                            {{ \Carbon\Carbon::parse($interview->interview_time)->format('H:i') }} น.
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; font-weight: 600;">รูปแบบการสัมภาษณ์:</td>
                        <td style="padding: 6px 0; color: #1e293b;">
                            <strong>{{ $interview->interview_type == 'online' ? 'ออนไลน์ (Online Meeting)' : 'On-site / สำนักงาน' }}</strong>
                        </td>
                    </tr>
                    @if($interview->location)
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; font-weight: 600;">สถานที่ / ห้อง:</td>
                        <td style="padding: 6px 0; color: #1e293b;">{{ $interview->location }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 6px 0; color: #64748b; font-weight: 600; vertical-align: top;">คณะกรรมการสัมภาษณ์:</td>
                        <td style="padding: 6px 0; color: #1e293b;">
                            @if($interview->interviewers && $interview->interviewers->count() > 0)
                                @foreach($interview->interviewers as $interviewer)
                                    <span class="interviewers-badge">👤 {{ $interviewer->fullname ?? $interviewer->name }}</span>
                                @endforeach
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <!-- กล่องลิ้งค์ประชุมออนไลน์ (ถ้ามี) -->
            @if($interview->meeting_link)
                <div class="meeting-box">
                    <div style="color: #15803d; font-weight: 700; font-size: 16px; margin-bottom: 6px;">
                        📹 ลิ้งค์เข้าห้องประชุมสัมภาษณ์ออนไลน์
                    </div>
                    <div style="font-size: 13px; color: #475569; margin-bottom: 12px;">
                        สามารถคลิกปุ่มด้านล่างเพื่อเข้าร่วมห้องประชุมสัมภาษณ์ตามวันและเวลาดังกล่าว
                    </div>
                    <a href="{{ $interview->meeting_link }}" target="_blank" class="btn-meeting">
                        คลิกเข้าร่วมการประชุม (Join Meeting)
                    </a>
                    <div style="margin-top: 10px; font-size: 12px; color: #64748b; word-break: break-all;">
                        URL ห้องประชุม: <a href="{{ $interview->meeting_link }}" target="_blank" style="color: #16a34a;">{{ $interview->meeting_link }}</a>
                    </div>
                </div>
            @endif

            <!-- บันทึกหรือข้อมูลเพิ่มเติม -->
            @if($interview->note)
                <div class="note-box">
                    <strong style="color: #1d4ed8;">📌 ข้อความ / รายละเอียดเพิ่มเติมจากฝ่ายสรรหา:</strong>
                    <div style="margin-top: 6px; color: #334155; white-space: pre-line;">{{ $interview->note }}</div>
                </div>
            @endif

            <!-- ปุ่มดูข้อมูลผู้สมัครและประเมินผลในระบบ -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('backend.recruitment.applications.show', $interview->application_id) }}" target="_blank" class="btn-view-app">
                    📄 เปิดดูรายละเอียดใบสมัครและประวัติในระบบ Kumwell HR
                </a>
                <div style="margin-top: 8px; font-size: 12px; color: #94a3b8;">
                    (สามารถเข้าสู่ระบบเพื่อดูเรซูเม่ ผลคะแนน และบันทึกผลการประเมินการสัมภาษณ์)
                </div>
            </div>

            <div class="signature">
                <p style="margin-bottom: 4px;">ขอแสดงความนับถือ,</p>
                <p style="margin: 0; font-weight: 600; color: #0f172a;">{{ $senderName ?? 'ฝ่ายทรัพยากรบุคคล (Human Resources)' }}</p>
                <p style="margin: 0; color: #64748b;">{{ $senderPosition ?? 'ฝ่ายทรัพยากรบุคคล' }}</p>
                <p style="margin: 4px 0 0 0; color: #64748b;">บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)</p>
                <p style="margin: 2px 0 0 0; color: #64748b;">โทร: 02-954-3455 | อีเมล: <a href="mailto:{{ $senderEmail }}">{{ $senderEmail }}</a></p>
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') + 543 }} Kumwell Corporation Public Company Limited. All rights reserved.
        </div>
    </div>
</body>
</html>
