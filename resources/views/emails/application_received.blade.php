@extends('emails.layouts.master', [
    'themeColor' => '#dc2626',
    'titleColor' => '#b91c1c',
    'emailTitle' => 'ยืนยันการรับสมัครงาน'
])

@section('subject', 'ยืนยันการรับสมัครงาน - ' . ($application->jobPost->position_name ?? 'Kumwell'))

@section('content')
    <p class="greeting">เรียน คุณ{{ $application->applicant->first_name }} {{ $application->applicant->last_name }}</p>

    <div class="message-body">
        <p>บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน) ได้รับใบสมัครงานของคุณในตำแหน่ง <strong style="color: #0284c7;">{{ $application->jobPost->position_name }}</strong> เรียบร้อยแล้ว</p>

        <!-- Blue Highlight Card requested by user -->
        <div class="highlight-box" style="background-color: #f0f9ff; border: 1.5px solid #bae6fd; border-left: 5px solid #0284c7; padding: 16px 20px; border-radius: 8px; margin: 20px 0;">
            <span style="display: inline-block; background-color: #e0f2fe; color: #0369a1; font-weight: 700; font-size: 12px; padding: 3px 10px; border-radius: 20px; margin-bottom: 8px;">
                ✓ ได้รับข้อมูลใบสมัครเรียบร้อยแล้ว
            </span>
            <p style="margin: 4px 0;"><strong>ตำแหน่งงานที่สมัคร:</strong> <span style="color: #0284c7; font-weight: 700;">{{ $application->jobPost->position_name }}</span></p>
            @if(!empty($application->jobPost->department?->department_name))
                <p style="margin: 4px 0;"><strong>สังกัด/แผนก:</strong> {{ $application->jobPost->department->department_name }}</p>
            @endif
            <p style="margin: 4px 0;"><strong>เลขที่ใบสมัคร:</strong> {{ $application->application_no }}</p>
            <p style="margin: 4px 0;"><strong>วันที่สมัคร:</strong> {{ $application->applied_at ? $application->applied_at->format('d/m/Y H:i') : date('d/m/Y H:i') }} น.</p>
        </div>

        <p>ขณะนี้ฝ่ายทรัพยากรบุคคล (HA) กำลังดำเนินการตรวจสอบคุณสมบัติของผู้สมัคร หากประวัติและคุณสมบัติของท่านผ่านการพิจารณาเบื้องต้น เจ้าหน้าที่จะติดต่อกลับเพื่อประสานงานนัดหมายสัมภาษณ์งานในลำดับถัดไป</p>

        <div class="notice-card" style="background-color: #fff7ed; border: 1px solid #ffedd5; border-left: 4px solid #dc2626; border-radius: 8px; padding: 14px 18px; margin: 20px 0; font-size: 13px; color: #9a3412;">
            <strong style="color: #b91c1c;">📌 คำแนะนำสำหรับผู้สมัคร:</strong>
            <div style="margin-top: 4px;">
                • ท่านสามารถนำเลขที่ใบสมัคร <strong>{{ $application->application_no }}</strong> ตรวจสอบความคืบหน้าได้ตลอดเวลา<br>
                • โปรดเตรียมความพร้อมในการรับสายโทรศัพท์หรือตรวจสอบอีเมลสำหรับการติดต่อจากฝ่ายทรัพยากรบุคคล
            </div>
        </div>

        @if(Route::has('frontend.recruitment.track'))
            <div style="text-align: center; margin: 24px 0;">
                <a href="{{ route('frontend.recruitment.track') }}" class="track-btn" target="_blank"
                    style="display: inline-block; background-color: #dc2626; color: #ffffff !important; text-decoration: none; font-weight: 600; font-size: 13px; padding: 10px 22px; border-radius: 8px;">
                    🔍 ตรวจสอบสถานะใบสมัครงานออนไลน์
                </a>
            </div>
        @endif

        <p style="margin-top: 20px;">ขอขอบพระคุณที่ให้ความสนใจร่วมงานกับครอบครัว Kumwell</p>
    </div>
@endsection