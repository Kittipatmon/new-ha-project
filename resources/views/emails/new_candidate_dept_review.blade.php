@php
    $applicant = $application->applicant;
    $post = $application->jobPost;
    $dept = $post?->department;
    $applicantName = $applicant 
        ? trim(($applicant->prefix ?? '') . ' ' . $applicant->first_name . ' ' . $applicant->last_name) 
        : 'ผู้สมัคร';
    $positionName = $post?->position_name 
        ?? ($post?->jobPosition?->position_name ?? ($post?->title ?? 'ไม่ระบุตำแหน่ง'));
    $deptName = $dept?->department_name ?: ($dept?->department_fullname ?: 'Kumwell Corporation');
    $headName = $departmentHead?->fullname ?: ($departmentHead?->name ?: 'หัวหน้าแผนก / ผู้จัดการแผนก');
@endphp

@extends('emails.layouts.master', [
    'themeColor' => '#2563eb',
    'titleColor' => '#1d4ed8',
    'emailTitle' => 'แจ้งเตือนผู้สมัครงานรอพิจารณา'
])

@section('subject', "[รอหัวหน้าแผนกพิจารณา] ตำแหน่ง {$positionName} - คุณ{$applicantName} ({$application->application_no})")

@section('content')
    <div class="alert-banner" style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-left: 5px solid #2563eb; border-radius: 8px; padding: 14px 18px; margin-bottom: 22px;">
        <div style="color: #1e40af; font-weight: 700; font-size: 15px; margin-bottom: 4px;">
            🔔 แจ้งเตือน: มีผู้สมัครงานส่งต่อให้หัวหน้าแผนกพิจารณา
        </div>
        <div style="color: #1e3a8a; font-size: 13px; line-height: 1.5;">
            เรียน <strong>{{ $headName }}</strong>, ฝ่ายทรัพยากรบุคคล (HA) ได้ตรวจสอบคุณสมบัติของผู้สมัครงานเบื้องต้นแล้ว ขณะนี้สถานะอยู่ในขั้นตอน <strong>"2. รอหัวหน้าแผนกพิจารณา"</strong> กรุณาเข้าสู่ระบบเพื่อตรวจสอบคุณสมบัติและร่วมพิจารณา
        </div>
    </div>

    <div class="message-body">
        <!-- Card 1: ข้อมูลการสมัครงาน -->
        <div class="section-card" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px 20px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #cbd5e1; padding-bottom: 10px; margin-bottom: 12px;">
                <span style="color: #0f172a; font-weight: 700; font-size: 14.5px;">1. ข้อมูลการสมัครงาน (Job Application)</span>
                <span style="display: inline-block; font-size: 11.5px; font-weight: 700; padding: 2px 10px; border-radius: 20px; background-color: #ffedd5; color: #c2410c;">รอพิจารณา</span>
            </div>

            <table class="info-table" style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
                <tr>
                    <td style="width: 140px; color: #64748b; font-weight: 600; padding: 5px 0; vertical-align: top;">เลขที่ใบสมัคร:</td>
                    <td style="padding: 5px 0; vertical-align: top;"><strong style="color: #0f172a;">{{ $application->application_no }}</strong></td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 600; padding: 5px 0; vertical-align: top;">ตำแหน่งที่สมัคร:</td>
                    <td style="padding: 5px 0; vertical-align: top; color: #2563eb; font-weight: 700;">{{ $positionName }}</td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 600; padding: 5px 0; vertical-align: top;">ฝ่าย / แผนก:</td>
                    <td style="padding: 5px 0; vertical-align: top; color: #1e293b;">{{ $deptName }}</td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 600; padding: 5px 0; vertical-align: top;">วันที่ส่งใบสมัคร:</td>
                    <td style="padding: 5px 0; vertical-align: top; color: #1e293b;">
                        {{ $application->applied_at ? $application->applied_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }} น.
                    </td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 600; padding: 5px 0; vertical-align: top;">ช่องทางที่ทราบข่าว:</td>
                    <td style="padding: 5px 0; vertical-align: top; color: #1e293b;">{{ $application->source ?: 'ระบบสมัครงาน Kumwell Career Portal' }}</td>
                </tr>
            </table>
        </div>

        <!-- Card 2: ข้อมูลผู้สมัครเบื้องต้น -->
        <div class="section-card" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px 20px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #cbd5e1; padding-bottom: 10px; margin-bottom: 12px;">
                <span style="color: #0f172a; font-weight: 700; font-size: 14.5px;">2. ข้อมูลผู้สมัครเบื้องต้น (Candidate Profile)</span>
                <span style="display: inline-block; font-size: 11.5px; font-weight: 700; padding: 2px 10px; border-radius: 20px; background-color: #dbeafe; color: #1e40af;">
                    {{ $applicant?->gender == 'female' ? 'หญิง' : ($applicant?->gender == 'male' ? 'ชาย' : 'ทั่วไป') }}
                </span>
            </div>

            <table class="info-table" style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
                <tr>
                    <td style="width: 140px; color: #64748b; font-weight: 600; padding: 5px 0; vertical-align: top;">ชื่อ-นามสกุล:</td>
                    <td style="padding: 5px 0; vertical-align: top;">
                        <strong style="color: #0f172a;">{{ $applicantName }}</strong>
                        @if(!empty($applicant?->nickname))
                            <span style="color: #64748b;">({{ $applicant->nickname }})</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 600; padding: 5px 0; vertical-align: top;">เบอร์โทรศัพท์:</td>
                    <td style="padding: 5px 0; vertical-align: top;">
                        <a href="tel:{{ $applicant?->phone }}" style="color: #0284c7; font-weight: 600;">
                            {{ $applicant?->phone ?: '-' }}
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 600; padding: 5px 0; vertical-align: top;">อีเมล:</td>
                    <td style="padding: 5px 0; vertical-align: top;">
                        <a href="mailto:{{ $applicant?->email }}" style="color: #0284c7;">
                            {{ $applicant?->email ?: '-' }}
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 600; padding: 5px 0; vertical-align: top;">การศึกษาสูงสุด:</td>
                    <td style="padding: 5px 0; vertical-align: top; color: #0f172a;">
                        @if(!empty($applicant?->education_level))
                            <strong>{{ $applicant->education_level }}</strong>
                            @if(!empty($applicant?->major)) ({{ $applicant->major }}) @endif
                            @if(!empty($applicant?->institution)) - {{ $applicant->institution }} @endif
                        @else
                            <span style="color: #94a3b8;">ระบุในเอกสารแนบ</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 600; padding: 5px 0; vertical-align: top;">ตำแหน่งงานล่าสุด:</td>
                    <td style="padding: 5px 0; vertical-align: top; color: #0f172a;">
                        {{ $applicant?->current_position ?: '-' }}
                        @if(!empty($applicant?->current_company))
                            <span style="color: #64748b;">({{ $applicant->current_company }})</span>
                        @endif
                        @if(!empty($applicant?->years_of_experience))
                            <span style="color: #475569;"> - ประสบการณ์ {{ $applicant->years_of_experience }} ปี</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="color: #64748b; font-weight: 600; padding: 5px 0; vertical-align: top;">เงินเดือนที่คาดหวัง:</td>
                    <td style="padding: 5px 0; vertical-align: top; color: #16a34a; font-weight: 700;">
                        @if(!empty($applicant?->expected_salary))
                            ฿{{ number_format((float) $applicant->expected_salary) }} บาท/เดือน
                        @else
                            ตามโครงสร้างบริษัท
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- Action Button -->
        <div style="text-align: center; margin: 28px 0 16px 0;">
            <a href="{{ route('backend.recruitment.applications.show', $application->id) }}" target="_blank" class="btn-action"
                style="display: inline-block; background-color: #2563eb; color: #ffffff !important; text-decoration: none; font-weight: 700; font-size: 14px; padding: 12px 28px; border-radius: 8px;">
                🔍 ดูรายละเอียดและร่วมพิจารณาใบสมัคร &rarr;
            </a>
            <div style="margin-top: 8px; font-size: 12px; color: #94a3b8;">
                (เข้าสู่ระบบ Kumwell HR เพื่อตรวจสอบเรซูเม่และบันทึกผลการพิจารณา)
            </div>
        </div>

        <div class="note-card" style="background-color: #f8fafc; border-radius: 6px; padding: 12px 16px; margin-top: 22px; font-size: 12.5px; color: #475569; border: 1px solid #e2e8f0;">
            ℹ️ <strong>หมายเหตุ:</strong> ฝ่ายทรัพยากรบุคคล (HA) ได้ทำการตรวจสอบคุณสมบัติเบื้องต้นแล้ว และส่งต่อให้หัวหน้าแผนกร่วมพิจารณาคุณสมบัติเพื่อเตรียมนัดสัมภาษณ์งานต่อไป
        </div>
    </div>
@endsection

@section('signature')
    <div class="signature">
        <p style="margin: 0; font-weight: 600; color: #1f2937;">ฝ่ายทรัพยากรบุคคลและบริหารงานกลาง (HA)</p>
        <p style="margin: 4px 0 0 0; color: #4b5563; font-size: 13px;">
            บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)<br>
            <span style="font-size: 12.5px; color: #6b7280;">โทร: 02-954-3455 | Kumwell Career Platform</span>
        </p>
    </div>
@endsection
