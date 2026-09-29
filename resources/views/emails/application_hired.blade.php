@extends('emails.layouts.master', [
    'themeColor' => '#10b981',
    'titleColor' => '#047857',
    'emailTitle' => 'แจ้งผลการพิจารณารับเข้าทำงาน'
])

@section('subject', 'แจ้งผลการพิจารณารับเข้าทำงาน - Kumwell Corporation')

@section('content')
    <p class="greeting">เรียน คุณ{{ $applicantName }}</p>

    <div class="message-body">
        <p>บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน) ขอแสดงความยินดีและมีความยินดีเป็นอย่างยิ่งที่จะแจ้งให้ท่านทราบว่า <strong>ท่านได้รับการคัดเลือกให้เข้าปฏิบัติงาน</strong> กับทางบริษัทฯ ตามรายละเอียดดังต่อไปนี้:</p>

        <div class="highlight-box" style="background-color: #f0fdf4; border: 1.5px solid #a7f3d0; border-left: 5px solid #10b981; padding: 16px 20px; border-radius: 8px; margin: 20px 0;">
            <span style="display: inline-block; background-color: #d1fae5; color: #047857; font-weight: 700; font-size: 12px; padding: 3px 10px; border-radius: 20px; margin-bottom: 8px;">
                ✓ ผ่านการคัดเลือกและได้รับการบรรจุเข้าทำงาน (Hired)
            </span>
            <p style="margin: 4px 0;"><strong>ตำแหน่งงาน:</strong> <span style="color: #047857; font-weight: 700;">{{ $positionName }}</span></p>
            @if(!empty($application->jobPost?->department?->department_name))
                <p style="margin: 4px 0;"><strong>สังกัด/แผนก:</strong> {{ $application->jobPost->department->department_name }}</p>
            @endif
            <p style="margin: 4px 0;"><strong>เลขที่ใบสมัคร:</strong> {{ $application->application_no }}</p>
            @if($application->onboarding_date)
                <p style="margin-top: 8px; padding-top: 8px; border-top: 1px dashed #bbf7d0;">
                    <strong>กำหนดวันเริ่มงาน (Onboarding Date):</strong> 
                    <span style="color: #047857; font-weight: 700; font-size: 15px;">{{ $application->onboarding_date->format('d/m/Y') }}</span>
                </p>
            @endif
        </div>

        <p>ทางบริษัทฯ ขอแสดงความยินดีและต้อนรับท่านเข้าเป็นส่วนหนึ่งของครอบครัวคัมเวล ทั้งนี้ เจ้าหน้าที่ฝ่ายทรัพยากรบุคคลจะติดต่อกลับไปยังท่านอีกครั้ง เพื่อแจ้งรายละเอียดเกี่ยวกับการนัดหมายวันเริ่มต้นทำงานตลอดจนเอกสารต่างๆ ที่ต้องจัดเตรียม</p>

        <div class="notice-card" style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-left: 4px solid #10b981; border-radius: 8px; padding: 14px 18px; margin: 20px 0; font-size: 13px; color: #065f46;">
            <strong style="color: #047857;">📌 การเตรียมตัวสำหรับการเริ่มงาน:</strong>
            <div style="margin-top: 4px;">
                • กรุณาจัดเตรียมเอกสารส่วนตัว เช่น สำเนาบัตรประชาชน, สำเนาทะเบียนบ้าน, วุฒิการศึกษา, และรูปถ่าย<br>
                • รายงานตัว ณ ฝ่ายทรัพยากรบุคคล อาคารสำนักงานใหญ่ ตามวันและเวลาที่กำหนด<br>
                • หากต้องการสอบถามหรือมีข้อติดขัดเรื่องวันเริ่มงาน โปรดแจ้งฝ่ายทรัพยากรบุคคลล่วงหน้า
            </div>
        </div>

        <p style="margin-top: 20px;">หากท่านมีข้อสงสัยหรือต้องการสอบถามข้อมูลเพิ่มเติม สามารถติดต่อเราได้ตามช่องทางด้านล่างนี้</p>
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
                <span style="font-size: 13px; color: #6b7280;">อีเมลติดต่อ: <a href="mailto:{{ $senderEmail }}" style="color: #047857; text-decoration: none;">{{ $senderEmail }}</a> | โทร: 02-954-3455</span>
            @endif
        </p>
    </div>
@endsection