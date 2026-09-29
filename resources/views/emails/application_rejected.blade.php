@extends('emails.layouts.master', [
    'themeColor' => '#1e3a8a',
    'titleColor' => '#1e3a8a',
    'emailTitle' => 'แจ้งผลการพิจารณาใบสมัครงาน'
])

@section('subject', 'แจ้งผลการพิจารณาใบสมัครงาน - Kumwell Corporation')

@section('content')
    <p class="greeting">เรียน คุณ{{ $applicantName }}</p>

    <div class="message-body">
        <p>ตามที่ท่านได้ให้ความสนใจสมัครงานกับบริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน) ในตำแหน่งงานดังนี้:</p>

        <div class="highlight-box" style="background-color: #f8fafc; border: 1.5px solid #cbd5e1; border-left: 5px solid #1e3a8a; padding: 16px 20px; border-radius: 8px; margin: 20px 0;">
            <span style="display: inline-block; background-color: #e0e7ff; color: #1e3a8a; font-weight: 700; font-size: 12px; padding: 3px 10px; border-radius: 20px; margin-bottom: 8px;">
                ผลการพิจารณาใบสมัคร
            </span>
            <p style="margin: 4px 0;"><strong>ตำแหน่งที่สมัคร:</strong> <span style="color: #1e3a8a; font-weight: 700;">{{ $positionName }}</span></p>
            @if(!empty($application->jobPost?->department?->department_name))
                <p style="margin: 4px 0;"><strong>สังกัด/แผนก:</strong> {{ $application->jobPost->department->department_name }}</p>
            @endif
        </div>

        <p>บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน) ขอขอบพระคุณเป็นอย่างยิ่งที่ท่านได้ให้ความสนใจและสละเวลาในการเข้าร่วมขั้นตอนการคัดเลือกบุคลากรของบริษัทฯ</p>
        <p>ทางบริษัทฯ ได้พิจารณาคุณสมบัติและประสบการณ์ของท่านอย่างรอบคอบ แต่ต้องขออภัยที่ต้องแจ้งให้ทราบว่า ในขณะนี้บริษัทฯ ยังไม่สามารถรับท่านเข้าร่วมงานในตำแหน่งดังกล่าวได้ เนื่องจากมีผู้สมัครท่านอื่นที่มีคุณสมบัติตรงกับความต้องการเฉพาะทางของตำแหน่งงานในปัจจุบันมากกว่า</p>

        <div class="notice-card" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #3b82f6; border-radius: 8px; padding: 14px 18px; margin: 20px 0; font-size: 13px; color: #334155;">
            <strong style="color: #1e40af;">📌 การจัดเก็บประวัติ (Talent Pool):</strong>
            <div style="margin-top: 4px;">
                ทั้งนี้ บริษัทฯ ขออนุญาตบันทึกและจัดเก็บประวัติการทำงานของท่านไว้ในระบบฐานข้อมูลผู้สมัคร หากในอนาคตมีตำแหน่งงานใหม่ที่สอดคล้องกับทักษะและประสบการณ์ของท่าน ฝ่ายทรัพยากรบุคคลจะติดต่อกลับเพื่อเชิญท่านเข้าร่วมงานต่อไป
            </div>
        </div>

        <p style="margin-top: 20px;">ขอขอบพระคุณอีกครั้งที่ให้ความไว้วางใจและสนใจร่วมงานกับครอบครัว Kumwell และขออวยพรให้ท่านประสบความสำเร็จในหน้าที่การงานและเป้าหมายในชีวิตต่อไป</p>
    </div>
@endsection

@section('signature')
    <div class="signature">
        <p style="margin: 0; font-weight: 600; color: #1f2937;">ด้วยความเคารพอย่างสูง,</p>
        <p style="margin: 5px 0 0 0; color: #4b5563;">
            <strong>{{ $senderName ?? 'ฝ่ายทรัพยากรบุคคล' }}</strong><br>
            <span style="font-size: 13px; color: #6b7280;">{{ $senderPosition ?? 'เจ้าหน้าที่ฝ่ายทรัพยากรบุคคล' }}</span><br>
            บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)<br>
            @if(!empty($senderEmail))
                <span style="font-size: 13px; color: #6b7280;">อีเมลติดต่อ: <a href="mailto:{{ $senderEmail }}" style="color: #1e3a8a; text-decoration: none;">{{ $senderEmail }}</a> | โทร: 02-954-3455</span>
            @endif
        </p>
    </div>
@endsection