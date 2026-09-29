@extends('emails.layouts.master', [
    'themeColor' => '#ea580c',
    'titleColor' => '#c2410c',
    'emailTitle' => !empty($isUpdate) ? 'แจ้งเปลี่ยนแปลงวันและเวลานัดสัมภาษณ์งาน' : 'หนังสือเชิญเข้าร่วมสัมภาษณ์งาน'
])

@section('subject', (!empty($isUpdate) ? 'แจ้งเปลี่ยนแปลงวันและเวลานัดสัมภาษณ์งาน' : 'หนังสือเชิญเข้าร่วมสัมภาษณ์งาน') . ' - Kumwell Corporation')

@section('content')
    @if(!empty($isUpdate))
        <div class="alert-update" style="background-color: #fff7ed; border: 1.5px solid #fdba74; border-left: 5px solid #ea580c; border-radius: 8px; padding: 14px 18px; margin-bottom: 22px;">
            <div style="color: #c2410c; font-weight: 700; font-size: 15px; margin-bottom: 4px;">
                ⚠️ แจ้งเปลี่ยนแปลงกำหนดการสัมภาษณ์งานใหม่
            </div>
            <div style="color: #9a3412; font-size: 13px; line-height: 1.5;">
                บริษัทฯ ขอแจ้งเปลี่ยนแปลงกำหนดการสัมภาษณ์งานจากเดิม โปรดยึดวัน เวลา และรูปแบบตามกำหนดการใหม่ด้านล่างนี้
            </div>
        </div>
    @endif

    <p class="greeting">เรียน คุณ{{ $interview->application->applicant->first_name }} {{ $interview->application->applicant->last_name }}</p>

    <div class="message-body">
        <p>
            ตามที่ท่านได้สมัครงานกับ บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน) ซึ่งบริษัทฯ เป็นผู้นำด้านระบบสายล่อฟ้าและสายดินสำหรับอุปกรณ์ไฟฟ้า (Grounding & Lightning Protection) ระดับมาตรฐานสากล
        </p>

        @if(!empty($isUpdate))
            <p>
                ตามที่ท่านได้รับการนัดหมายสัมภาษณ์งานในตำแหน่ง <strong>“{{ $interview->application->jobPost->position_name }}”</strong> บริษัทฯ ขอแจ้ง<strong>เปลี่ยนแปลงวันและเวลานัดหมายสัมภาษณ์งานใหม่</strong>เป็นดังนี้:
            </p>
        @else
            <p>
                บริษัทฯ ได้พิจารณาคุณสมบัติเบื้องต้นของท่านแล้วเห็นว่าตรงตามความต้องการของบริษัท ในตำแหน่ง <strong>“{{ $interview->application->jobPost->position_name }}”</strong> จึงขอเรียนเชิญท่านเข้าร่วมสัมภาษณ์งานตามกำหนดการดังต่อไปนี้:
            </p>
        @endif

        <div class="highlight-box" style="background-color: #fff7ed; border: 1.5px solid #ffedd5; border-left: 5px solid #ea580c; padding: 18px 20px; border-radius: 8px; margin: 20px 0;">
            <span style="display: inline-block; background-color: #ffedd5; color: #c2410c; font-weight: 700; font-size: 12px; padding: 3px 10px; border-radius: 20px; margin-bottom: 8px;">
                {{ !empty($isUpdate) ? '🔄 กำหนดการนัดสัมภาษณ์ใหม่' : '📅 กำหนดการสัมภาษณ์งาน' }} (รอบที่ {{ $interview->interview_round }})
            </span>
            <p style="margin: 5px 0;"><strong>ตำแหน่งงาน:</strong> <span style="color: #c2410c; font-weight: 700;">{{ $interview->application->jobPost->position_name }}</span></p>
            <p style="margin: 5px 0;"><strong>วันที่สัมภาษณ์:</strong> วัน{{ \Carbon\Carbon::parse($interview->interview_date)->locale('th')->dayName }}ที่ {{ \Carbon\Carbon::parse($interview->interview_date)->format('d') }} {{ \Carbon\Carbon::parse($interview->interview_date)->locale('th')->monthName }} {{ \Carbon\Carbon::parse($interview->interview_date)->format('Y') + 543 }}</p>
            <p style="margin: 5px 0;"><strong>เวลา:</strong> <span style="font-weight: 700; color: #111827;">{{ \Carbon\Carbon::parse($interview->interview_time)->format('H:i') }} น.</span></p>
            <p style="margin: 5px 0;"><strong>รูปแบบการสัมภาษณ์:</strong> {{ $interview->interview_type == 'online' ? 'ออนไลน์ (Online Meeting)' : 'On-site / เข้ามาสัมภาษณ์ที่สำนักงานบริษัท' }}</p>
            @if($interview->location)
                <p style="margin: 5px 0;"><strong>สถานที่ / ห้อง:</strong> {{ $interview->location }}</p>
            @endif
        </div>

        @if($interview->meeting_link)
            <div class="meeting-box" style="background-color: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 8px; padding: 18px 20px; margin: 22px 0; text-align: center;">
                <div style="color: #15803d; font-weight: 700; font-size: 15px; margin-bottom: 6px;">
                    📹 ลิ้งค์เข้าห้องประชุมสัมภาษณ์ออนไลน์
                </div>
                <div style="font-size: 13px; color: #4b5563; margin-bottom: 10px;">
                    ท่านสามารถคลิกปุ่มด้านล่างนี้เพื่อเข้าร่วมการสัมภาษณ์เมื่อถึงกำหนดเวลา
                </div>
                <a href="{{ $interview->meeting_link }}" target="_blank" class="btn-meeting"
                    style="display: inline-block; background-color: #16a34a; color: #ffffff !important; font-weight: 700; padding: 12px 26px; border-radius: 8px; text-decoration: none; font-size: 14px; margin-top: 10px;">
                    คลิกเข้าร่วมการประชุม (Join Meeting)
                </a>
                <div style="margin-top: 10px; font-size: 12px; color: #64748b; word-break: break-all;">
                    หรือคัดลอกลิ้งค์: <a href="{{ $interview->meeting_link }}" target="_blank" style="color: #16a34a;">{{ $interview->meeting_link }}</a>
                </div>
            </div>
        @endif

        @if($interview->note)
            <div class="note-card" style="background-color: #eff6ff; border: 1px solid #dbeafe; border-left: 4px solid #3b82f6; border-radius: 8px; padding: 14px 18px; margin: 20px 0; font-size: 13.5px; color: #1e40af;">
                <strong style="color: #1d4ed8;">📌 รายละเอียด / ข้อความเพิ่มเติมจากการนัดหมาย:</strong>
                <div style="margin-top: 6px; color: #334155; white-space: pre-line;">{{ $interview->note }}</div>
            </div>
        @endif

        <p style="margin-top: 20px;">
            <strong>⚠️ หากสะดวกในวันเวลาดังกล่าว รบกวนตอบกลับเพื่อคอนเฟิร์มการนัดหมายทางอีเมลฉบับนี้ครับ</strong>
        </p>

        <div class="test-box" style="background-color: #f8fafc; border: 1.5px dashed #cbd5e1; padding: 16px 18px; margin: 20px 0; border-radius: 8px; font-size: 13.5px;">
            <strong style="color: #1e293b;">📝 แบบทดสอบบุคลิกภาพ (16 Personalities):</strong>
            <p style="margin: 6px 0; color: #475569;">
                รบกวนทำแบบทดสอบบุคลิกภาพ เพื่อประกอบการพิจารณาสัมภาษณ์งานตามลิ้งค์แนบ:<br>
                👉 <a href="https://shorturl.asia/3BcrT" target="_blank" style="font-weight: 600; color: #ea580c;">https://shorturl.asia/3BcrT</a>
            </p>
            <div style="font-size: 12.5px; color: #64748b; margin-top: 4px;">
                * หลังจากทำแบบทดสอบเสร็จเรียบร้อยแล้ว รบกวนแคปเจอร์หน้าจอผลการทดสอบและตอบกลับทางอีเมลฉบับนี้
            </div>
        </div>

        @if($interview->interview_type != 'online' || empty($interview->meeting_link))
            <div style="margin: 20px 0;">
                <p style="margin-bottom: 6px;">
                    <strong>📍 สถานที่สัมภาษณ์งาน:</strong> {{ $interview->location ?: 'สำนักงานใหญ่ บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)' }}
                </p>
                <a href="https://maps.app.goo.gl/Xsu7hgYL4moRC6Hf9" class="btn-maps" target="_blank"
                    style="display: inline-block; background-color: #ea580c; color: #ffffff !important; text-decoration: none; font-weight: 600; font-size: 13px; padding: 9px 20px; border-radius: 6px; margin-top: 8px;">
                    🗺️ ดูแผนที่สำนักงานใหญ่ (Google Maps)
                </a>
            </div>
        @endif

        <div class="doc-section" style="background-color: #f9fafb; border: 1px solid #f3f4f6; border-radius: 8px; padding: 16px 20px; margin: 20px 0; font-size: 13.5px;">
            <strong style="color: #111827;">📄 เอกสารที่ต้องเตรียมมาในวันสัมภาษณ์งาน:</strong>
            <ul style="margin: 8px 0; padding-left: 22px; color: #4b5563;">
                <li>สำเนาบัตรประชาชน 1 ฉบับ</li>
                <li>สำเนาทะเบียนบ้าน 1 ฉบับ</li>
                <li>สำเนาหลักฐานการศึกษา (Transcript / ใบปริญญาบัตร) 1 ฉบับ</li>
                <li>รูปถ่ายหน้าตรง 1 รูป</li>
                <li>เอกสารอื่นๆ (เช่น ผลงาน/Portfolio, สลิปเงินเดือนล่าสุด, หนังสือรับรองการทำงาน)</li>
            </ul>
            <div style="color: #ea580c; font-weight: 600; font-size: 12.5px;">
                ** รบกวนจัดเตรียมเอกสารข้างต้นให้ครบถ้วนในวันสัมภาษณ์งาน **
            </div>
        </div>

        <p style="margin-top: 20px;">
            หากท่านมีข้อสงสัยหรือติดขัดประการใด สามารถสอบถามได้ทางโทรศัพท์ <strong>02-954-3455</strong> ติดต่อ <strong>คุณ{{ $senderName ?? 'เจ้าหน้าที่ฝ่ายทรัพยากรบุคคล' }}</strong>@if(!empty($senderEmail)) (อีเมล: <a href="mailto:{{ $senderEmail }}">{{ $senderEmail }}</a>)@endif
        </p>

        <p>
            จึงเรียนมาเพื่อขอเชิญท่านเข้าร่วมสัมภาษณ์งานในครั้งนี้ และหวังเป็นอย่างยิ่งว่าจะได้มีโอกาสร่วมงานกับท่าน
        </p>

        <div class="welfare-section" style="font-size: 12.5px; color: #6b7280; background: #fafafa; padding: 14px 18px; border-radius: 6px; border: 1px solid #f0f0f0; margin-top: 24px; line-height: 1.6;">
            <strong>⭐ สวัสดิการพนักงาน Kumwell:</strong> กองทุนสำรองเลี้ยงชีพ, กองทุนสวัสดิการกู้ยืม, ประกันอุบัติเหตุกลุ่ม, ตรวจสุขภาพประจำปี, เงินช่วยเหลือในโอกาสต่างๆ, ทุนการศึกษาบุตร, ท่องเที่ยวประจำปี, การปรับเงินเดือนประจำปี, โบนัส ฯลฯ
        </div>
    </div>
@endsection

@section('signature')
    <div class="signature">
        <p style="margin: 0; font-weight: 600; color: #1f2937;">Best Regards,</p>
        <p style="margin: 5px 0 0 0; color: #4b5563;">
            <strong>{{ $senderName ?? 'ฝ่ายทรัพยากรบุคคล' }}</strong><br>
            <span style="font-size: 13px; color: #6b7280;">{{ $senderPosition ?? 'ฝ่ายทรัพยากรบุคคลและบริหารงานกลาง' }}</span><br>
            บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)<br>
            <span style="font-size: 13px; color: #6b7280;">โทร: +662 954 3455 | เว็บไซต์: <a href="http://www.kumwell.com" target="_blank" style="color: #ea580c;">www.kumwell.com</a></span>
            @if(!empty($senderEmail))
                <br><span style="font-size: 13px; color: #6b7280;">อีเมล: <a href="mailto:{{ $senderEmail }}" style="color: #ea580c;">{{ $senderEmail }}</a></span>
            @endif
        </p>
    </div>
@endsection
