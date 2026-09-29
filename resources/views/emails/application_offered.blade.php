@extends('emails.layouts.master', [
    'themeColor' => '#0d9488',
    'titleColor' => '#0f766e',
    'emailTitle' => 'แจ้งผลผ่านการคัดเลือกและยื่นข้อเสนอการจ้างงาน'
])

@section('subject', 'แจ้งผลผ่านการคัดเลือกและยื่นข้อเสนอการจ้างงาน')

@section('content')
    <p class="greeting">เรียน คุณ{{ $applicantName }}</p>

    <div class="message-body">
        <p>บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน) ขอแสดงความยินดีที่จะแจ้งให้ท่านทราบว่า <strong>ท่านได้ผ่านการคัดเลือกจากการสัมภาษณ์งาน</strong> สำหรับตำแหน่งงานดังต่อไปนี้:</p>

        <div class="highlight-box" style="background-color: #f0fdfa; border: 1.5px solid #99f6e4; border-left: 5px solid #0d9488; padding: 16px 20px; border-radius: 8px; margin: 20px 0;">
            <span style="display: inline-block; background-color: #ccfbf1; color: #0f766e; font-weight: 700; font-size: 12px; padding: 3px 10px; border-radius: 20px; margin-bottom: 8px;">
                ✓ ผ่านการคัดเลือก (Selection Passed)
            </span>
            <p style="margin: 4px 0;"><strong>ตำแหน่งงาน:</strong> <span style="color: #0f766e; font-weight: 700;">{{ $positionName }}</span></p>
            <p style="margin: 4px 0;"><strong>สังกัด/แผนก:</strong> {{ $departmentName }}</p>
            <p style="margin: 4px 0;"><strong>เลขที่ใบสมัคร:</strong> {{ $application->application_no }}</p>
        </div>

        <p>ทางบริษัทฯ มีความประสงค์จะ<strong>ยื่นข้อเสนอการจ้างงาน (Job Offer)</strong> ให้แก่ท่าน โดยเจ้าหน้าที่ฝ่ายทรัพยากรบุคคล (HA) จะติดต่อประสานงานกับท่านโดยตรง เพื่อชี้แจงรายละเอียดเกี่ยวกับอัตราผลตอบแทน สวัสดิการ ข้อตกลง และการนัดหมายกำหนดวันเริ่มต้นทำงาน</p>

        <div class="notice-card" style="background-color: #fff7ed; border: 1px solid #ffedd5; border-left: 4px solid #0d9488; border-radius: 8px; padding: 14px 18px; margin: 20px 0; font-size: 13px; color: #9a3412;">
            <strong style="color: #0f766e;">📌 ขั้นตอนถัดไป:</strong>
            <div style="margin-top: 4px;">
                1. เจ้าหน้าที่ฝ่ายทรัพยากรบุคคลจะติดต่อท่านผ่านทางโทรศัพท์หรืออีเมลเพื่อยืนยันข้อเสนอ<br>
                2. กำหนดวันเริ่มต้นทำงานและเตรียมเอกสารสัญญาจ้างงาน<br>
                3. ท่านจะได้รับอีเมลยืนยันวันเริ่มงานอย่างเป็นทางการอีกครั้ง
            </div>
        </div>

        @if(Route::has('frontend.recruitment.track'))
            <div style="text-align: center; margin: 24px 0;">
                <a href="{{ route('frontend.recruitment.track') }}" class="track-btn" target="_blank"
                    style="display: inline-block; background-color: #0d9488; color: #ffffff !important; text-decoration: none; font-weight: 600; font-size: 13px; padding: 10px 22px; border-radius: 8px;">
                    🔍 ตรวจสอบสถานะใบสมัครงานออนไลน์
                </a>
            </div>
        @endif

        <p style="margin-top: 20px;">หากท่านมีข้อสงสัยหรือต้องการสอบถามข้อมูลเพิ่มเติม สามารถติดต่อฝ่ายทรัพยากรบุคคลได้ตามช่องทางด้านล่างนี้</p>
    </div>
@endsection

@section('signature')
    <div class="signature">
        <p style="margin: 0; font-weight: 600; color: #1f2937;">ด้วยความเคารพอย่างสูง,</p>
        <p style="margin: 5px 0 0 0; color: #4b5563;">
            <strong>{{ $senderName ?? 'ฝ่ายทรัพยากรบุคคล' }}</strong><br>
            <span style="font-size: 13px; color: #6b7280;">{{ $senderPosition ?? 'ฝ่ายทรัพยากรบุคคลและบริหารงานกลาง' }}</span><br>
            บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)<br>
            @if(!empty($senderEmail))
                <span style="font-size: 13px; color: #6b7280;">อีเมล: <a href="mailto:{{ $senderEmail }}" style="color: #0d9488; text-decoration: none;">{{ $senderEmail }}</a> | โทร: 02-954-3455</span>
            @endif
        </p>
    </div>
@endsection
