<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ใบขออนุมัติกำลังคน</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap');
        
        @page {
            size: A4 portrait;
            margin: 14mm 10mm 5mm 10mm;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            font-size: 10pt;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 0;
        }

        table {
            border-collapse: collapse;
        }

        .dotted-span {
            border-bottom: 1px dotted #000;
            display: inline-block;
        }

        .circle-box {
            display: inline-block;
            width: 11px;
            height: 11px;
            border: 1px solid #000;
            border-radius: 50%;
            vertical-align: -1px;
            margin-right: 2px;
            text-align: center;
            line-height: 11px;
            position: relative;
        }

        .circle-checked::after {
            content: "\2713";
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 24px;
            font-weight: bold;
            color: #000;
            position: absolute;
            top: -1px;
            left: -2px;
            line-height: 11px;
        }
        
        .nowrap {
            white-space: nowrap;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div style="text-align: center; font-size: 15pt; font-weight: bold; margin-bottom: 2px;">
        ใบขออนุมัติกำลังคน
    </div>

    <!-- Date -->
    <table style="width: 100%;">
        <tr>
            <td style="text-align: right;" class="nowrap">
                วันที่ <span class="dotted-span" style="width: 130px; text-align: center;">{{ \Carbon\Carbon::parse($manpowerRequest->date)->addYears(543)->format('d/m/Y') }}</span>
            </td>
        </tr>
    </table>

    <!-- Line 1: Dept / Section -->
    <table style="width: 100%; margin-top: 2px;">
        <tr>
            <td style="width: 30px;" class="nowrap">ฝ่าย</td>
            <td style="width: 45%;"><span class="dotted-span" style="width: 95%; padding-left: 10px;">{{ $manpowerRequest->department }}</span></td>
            <td style="width: 40px;" class="nowrap">แผนก</td>
            <td style="width: 45%;"><span class="dotted-span" style="width: 98%; padding-left: 10px;">{{ $manpowerRequest->section }}</span></td>
        </tr>
    </table>

    <!-- Line 2: Position Th / En / Headcount -->
    <table style="width: 100%; margin-top: 3px;">
        <tr>
            <td style="width: 24%;" class="nowrap">ขออนุมัติตำแหน่ง (ชื่อไทย)</td>
            <td style="width: 24%;"><span class="dotted-span" style="width: 100%; padding-left: 5px;">{{ $manpowerRequest->job_title_th }}</span></td>
            <td style="width: 12%; text-align: center;" class="nowrap">(ชื่ออังกฤษ)</td>
            <td style="width: 24%;"><span class="dotted-span" style="width: 100%; padding-left: 5px;">{{ $manpowerRequest->job_title_en ?? '-' }}</span></td>
            <td style="width: 8%; text-align: center;" class="nowrap">จำนวน</td>
            <td style="width: 4%; text-align: center;"><span class="dotted-span" style="width: 100%;">{{ $manpowerRequest->headcount }}</span></td>
            <td style="width: 4%; text-align: right;" class="nowrap">อัตรา</td>
        </tr>
    </table>

    <!-- Line 3: Current Headcount / Expected Date -->
    <table style="width: 100%; margin-top: 3px;">
        <tr>
            <td style="width: 200px;" class="nowrap">ขณะนี้ แผนก/ฝ่าย มีพนักงานทั้งหมด</td>
            <td style="width: 45px; text-align: center;"><span class="dotted-span" style="width: 95%;">{{ $manpowerRequest->current_headcount ?? '-' }}</span></td>
            <td style="width: 195px;" class="nowrap">อัตรา ต้องการรับเข้าทำงานภายในวันที่</td>
            <td><span class="dotted-span" style="width: 95%; text-align: center;">{{ \Carbon\Carbon::parse($manpowerRequest->expected_start_date)->addYears(543)->format('d/m/Y') }}</span></td>
        </tr>
    </table>

    <!-- Level Section -->
    <table style="width: 100%; margin-top: 5px;">
        <tr>
            <td style="width: 40px; vertical-align: top; font-weight: bold;" class="nowrap">ระดับ</td>
            <td style="text-align: center; vertical-align: top; margin-top: 5px;" class="nowrap">
                <span class="circle-box {{ $manpowerRequest->job_level == 'บริหาร (Lv.9-8)' ? 'circle-checked' : '' }}"></span>&nbsp;บริหาร<br>
                <span style="font-size: 8.5pt;">(Lv.9 - 8)</span>
            </td>
            <td style="text-align: center; vertical-align: top;" class="nowrap">
                <span class="circle-box {{ $manpowerRequest->job_level == 'ผู้จัดการ (Lv.7)' ? 'circle-checked' : '' }}"></span>&nbsp;ผู้จัดการ<br>
                <span style="font-size: 8.5pt;">(Lv.7)</span>
            </td>
            <td style="text-align: center; vertical-align: top;" class="nowrap">
                <span class="circle-box {{ $manpowerRequest->job_level == 'ผจก./หัวหน้าส่วนงาน (Lv.6-5)' ? 'circle-checked' : '' }}"></span>&nbsp;ผจก./หัวหน้าส่วนงาน<br>
                <span style="font-size: 8.5pt;">(Lv.6 - 5)</span>
            </td>
            <td style="text-align: center; vertical-align: top;" class="nowrap">
                <span class="circle-box {{ $manpowerRequest->job_level == 'วิศวกร/อาวุโสเจ้าหน้าที่ (Lv.4-3)' ? 'circle-checked' : '' }}"></span>&nbsp;วิศวกร/อาวุโส/เจ้าหน้าที่<br>
                <span style="font-size: 8.5pt;">(Lv.4 - 3)</span>
            </td>
            <td style="text-align: center; vertical-align: top;" class="nowrap">
                <span class="circle-box {{ $manpowerRequest->job_level == 'Sup/ปฏิบัติการ (Lv.2-1)' ? 'circle-checked' : '' }}"></span>&nbsp;Sup/ปฏิบัติการ<br>
                <span style="font-size: 8.5pt;">(Lv.2 - 1)</span>
            </td>
        </tr>
    </table>

    <!-- Hire Type Section -->
    <table style="width: 100%; margin-top: 10px; margin-bottom: 15px;">
        <tr>
            <td style="width: 100px; vertical-align: top; font-weight: bold;" class="nowrap">ลักษณะการว่าจ้าง</td>
            <td>
                <div style="margin-top: 5px; margin-bottom: 2px; margin-left: 10px;" class="nowrap">
                    <span class="circle-box {{ $manpowerRequest->hire_type == 'จ้างเพิ่มเติม' ? 'circle-checked' : '' }}"></span>&nbsp;จ้างเพิ่มเติม
                </div>
                <div style="margin-bottom: 2px; margin-left: 10px;" class="nowrap">
                    <span class="circle-box {{ $manpowerRequest->hire_type == 'จ้างทดแทน' ? 'circle-checked' : '' }}"></span>&nbsp;จ้างทดแทนคนเก่า คือ นาย / นาง / นางสาว <span class="dotted-span" style="width: 250px;">{{ $manpowerRequest->hire_replacement_name }}</span>
                </div>
                <div style="margin-bottom: 2px; margin-left: 10px;" class="nowrap">
                    <span class="circle-box {{ $manpowerRequest->hire_type == 'โอนย้าย' ? 'circle-checked' : '' }}"></span>&nbsp;โอนย้าย / ปรับเปลี่ยนตำแหน่ง คือ นาย / นาง / นางสาว <span class="dotted-span" style="width: 210px;">{{ $manpowerRequest->hire_transfer_name }}</span>
                </div>
                <div class="nowrap" style="margin-left: 10px;">
                    <span class="circle-box {{ $manpowerRequest->hire_type == 'จ้างชั่วคราว' ? 'circle-checked' : '' }}"></span>&nbsp;จ้างชั่วคราว &nbsp;&nbsp;&nbsp;&nbsp;ระยะเวลา จากวันที่ <span class="dotted-span" style="width: 90px; text-align: center;">{{ $manpowerRequest->hire_temp_start ? \Carbon\Carbon::parse($manpowerRequest->hire_temp_start)->addYears(543)->format('d/m/Y') : '' }}</span> ถึง <span class="dotted-span" style="width: 90px; text-align: center;">{{ $manpowerRequest->hire_temp_end ? \Carbon\Carbon::parse($manpowerRequest->hire_temp_end)->addYears(543)->format('d/m/Y') : '' }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Attachment Section -->
    <table style="width: 100%; margin-top: 5px;">
        <tr>
            <td style="width: 100px; font-weight: bold;" class="nowrap">เอกสารแนบ</td>
            <td class="nowrap"><span class="circle-box {{ $manpowerRequest->attachment_org_chart ? 'circle-checked' : '' }}"></span>&nbsp;Organization Chart</td>
            <td class="nowrap"><span class="circle-box {{ $manpowerRequest->attachment_jd ? 'circle-checked' : '' }}"></span>&nbsp;Job Description</td>
            <td class="nowrap"><span class="circle-box {{ $manpowerRequest->attachment_manpower_plan ? 'circle-checked' : '' }}"></span>&nbsp;แผนอัตรากำลังคน</td>
        </tr>
    </table>

    <!-- Main Content Table -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 6px; border: 1px solid #000;">
        <thead>
            <tr style="background-color: #e6e6e6;">
                <th style="width: 50%; border: 1px solid #000; padding: 3px; font-weight: bold; text-align: center;">คุณสมบัติที่ต้องการ</th>
                <th style="width: 50%; border: 1px solid #000; padding: 3px; font-weight: bold; text-align: center;">หน้าที่รับผิดชอบโดยสังเขป</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #000; padding: 5px; vertical-align: top;">
                    <div style="margin-bottom: 4px;" class="nowrap">
                        เพศ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="circle-box {{ $manpowerRequest->req_gender == 'ชาย' ? 'circle-checked' : '' }}"></span>&nbsp;ชาย &nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="circle-box {{ $manpowerRequest->req_gender == 'หญิง' ? 'circle-checked' : '' }}"></span>&nbsp;หญิง &nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="circle-box {{ $manpowerRequest->req_gender == 'ชาย/หญิง' ? 'circle-checked' : '' }}"></span>&nbsp;ชาย/หญิง
                    </div>
                    <div style="margin-bottom: 4px;" class="nowrap">อายุ : <span class="dotted-span" style="width: 80px; padding-left: 10px;">{{ $manpowerRequest->req_age }}</span> วุฒิการศึกษา : <span class="dotted-span" style="width: 120px; padding-left: 10px;">{{ $manpowerRequest->req_education }}</span></div>
                    <div style="margin-bottom: 4px;" class="nowrap">สาขาวิชา : <span class="dotted-span" style="width: 250px; padding-left: 10px;">{{ $manpowerRequest->req_major }}</span></div>
                    <div style="margin-bottom: 4px;" class="nowrap">ประสบการณ์ทำงาน : <span class="dotted-span" style="width: 210px; padding-left: 10px;">{{ $manpowerRequest->req_experience }}</span></div>
                    <div style="margin-bottom: 4px;" class="nowrap">คุณสมบัติพิเศษ : <span class="dotted-span" style="width: 220px; padding-left: 10px;">{{ $manpowerRequest->req_special }}</span></div>
                    <div class="nowrap">อื่นๆ : <span class="dotted-span" style="width: 270px; padding-left: 10px;">{{ $manpowerRequest->req_other }}</span></div>
                </td>
                <td style="border: 1px solid #000; padding: 5px; vertical-align: top; line-height: 1.5;">
                    <div class="nowrap">1 <span class="dotted-span" style="width: 310px; padding-left: 10px;">{{ $manpowerRequest->res_1 }}</span></div>
                    <div class="nowrap">2 <span class="dotted-span" style="width: 310px; padding-left: 10px;">{{ $manpowerRequest->res_2 }}</span></div>
                    <div class="nowrap">3 <span class="dotted-span" style="width: 310px; padding-left: 10px;">{{ $manpowerRequest->res_3 }}</span></div>
                    <div class="nowrap">4 <span class="dotted-span" style="width: 310px; padding-left: 10px;">{{ $manpowerRequest->res_4 }}</span></div>
                    <div class="nowrap">5 <span class="dotted-span" style="width: 310px; padding-left: 10px;">{{ $manpowerRequest->res_5 }}</span></div>
                    <div class="nowrap">6 <span class="dotted-span" style="width: 310px; padding-left: 10px;">{{ $manpowerRequest->res_6 }}</span></div>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Signatures -->
    <table style="width: 100%; margin-top: 10px; margin-bottom: 20px;">
        <tr>
            <td style="text-align: left; padding-top: 8px;" class="nowrap">ลงชื่อ <span class="dotted-span" style="width: 300px; text-align: center;">{{ $manpowerRequest->requester_name }}</span> ผู้ร้องขอ</td>
            <td style="text-align: left; padding-top: 8px;" class="nowrap">วันที่ <span class="dotted-span" style="width: 50px; text-align: center;">{{ $manpowerRequest->requester_date ? \Carbon\Carbon::parse($manpowerRequest->requester_date)->format('d') : '' }}</span> / <span class="dotted-span" style="width: 50px; text-align: center;">{{ $manpowerRequest->requester_date ? \Carbon\Carbon::parse($manpowerRequest->requester_date)->format('m') : '' }}</span> / <span class="dotted-span" style="width: 60px; text-align: center;">{{ $manpowerRequest->requester_date ? \Carbon\Carbon::parse($manpowerRequest->requester_date)->addYears(543)->format('Y') : '' }}</span></td>
        </tr>
        <tr>
            <td style="text-align: left; padding-top: 12px;" class="nowrap">ลงชื่อ <span class="dotted-span" style="width: 300px; text-align: center;">{{ $manpowerRequest->manager_approved_at && $manpowerRequest->managerApprover ? $manpowerRequest->managerApprover->firstname . ' ' . $manpowerRequest->managerApprover->lastname : $manpowerRequest->manager_name }}</span> ผู้จัดการแผนก/ฝ่าย</td>
            <td style="text-align: left; padding-top: 12px;" class="nowrap">วันที่ <span class="dotted-span" style="width: 50px; text-align: center;">{{ $manpowerRequest->manager_date ? \Carbon\Carbon::parse($manpowerRequest->manager_date)->format('d') : '' }}</span> / <span class="dotted-span" style="width: 50px; text-align: center;">{{ $manpowerRequest->manager_date ? \Carbon\Carbon::parse($manpowerRequest->manager_date)->format('m') : '' }}</span> / <span class="dotted-span" style="width: 60px; text-align: center;">{{ $manpowerRequest->manager_date ? \Carbon\Carbon::parse($manpowerRequest->manager_date)->addYears(543)->format('Y') : '' }}</span></td>
        </tr>
        <tr>
            <td style="text-align: left; padding-top: 12px;" class="nowrap">ลงชื่อ <span class="dotted-span" style="width: 300px; text-align: center;">{{ $manpowerRequest->vp_approved_at && $manpowerRequest->vpApprover ? $manpowerRequest->vpApprover->firstname . ' ' . $manpowerRequest->vpApprover->lastname : $manpowerRequest->vp_name }}</span> ประธานสายงาน</td>
            <td style="text-align: left; padding-top: 12px;" class="nowrap">วันที่ <span class="dotted-span" style="width: 50px; text-align: center;">{{ $manpowerRequest->vp_date ? \Carbon\Carbon::parse($manpowerRequest->vp_date)->format('d') : '' }}</span> / <span class="dotted-span" style="width: 50px; text-align: center;">{{ $manpowerRequest->vp_date ? \Carbon\Carbon::parse($manpowerRequest->vp_date)->format('m') : '' }}</span> / <span class="dotted-span" style="width: 60px; text-align: center;">{{ $manpowerRequest->vp_date ? \Carbon\Carbon::parse($manpowerRequest->vp_date)->addYears(543)->format('Y') : '' }}</span></td>
        </tr>
    </table>

    <!-- Approvals Table -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 6px; border: 1px solid #000;">
        <thead>
            <tr style="background-color: #e6e6e6;">
                <th style="width: 50%; border: 1px solid #000; padding: 3px; font-weight: bold; text-align: center;">ความคิดเห็นฝ่ายบุคคล</th>
                <th style="width: 50%; border: 1px solid #000; padding: 3px; font-weight: bold; text-align: center;">ความเห็นประธานเจ้าหน้าที่บริหาร</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #000; padding: 5px; vertical-align: top;">
                    <div class="nowrap">ความเห็น : <span class="dotted-span" style="width: 250px;">{{ $manpowerRequest->hr_approved_at ? 'ตรวจสอบแล้วถูกต้อง' : '' }}</span></div>
                    <div style="text-align: center; margin-top: 18px;">
                        <span class="dotted-span" style="width: 200px; text-align: center; padding-bottom: 2px;">{{ $manpowerRequest->hr_approved_at ? 'อนุมัติผ่านระบบ' : '' }}</span><br>
                        <div style="margin-top: 8px; margin-bottom: 2px;">
                            ( &nbsp;&nbsp;<span style="display: inline-block; width: 180px; text-align: center;">{{ $manpowerRequest->hr_approved_at && $manpowerRequest->hrApprover ? $manpowerRequest->hrApprover->firstname . ' ' . $manpowerRequest->hrApprover->lastname : '' }}</span>&nbsp;&nbsp; )
                        </div>
                        <span style="font-weight: bold; font-size: 9.5pt;">ผจก.แผนก/ฝ่ายทรัพยากรบุคคล</span>
                    </div>
                </td>
                <td style="border: 1px solid #000; padding: 5px; vertical-align: top;">
                    <div style="text-align: center;" class="nowrap">
                        <span class="circle-box {{ $manpowerRequest->status == 'approved' ? 'circle-checked' : '' }}"></span>&nbsp;อนุมัติตามคำขอ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="circle-box {{ $manpowerRequest->status == 'rejected' ? 'circle-checked' : '' }}"></span>&nbsp;ไม่อนุมัติตามคำขอ
                    </div>
                    <div style="text-align: center; margin-top: 18px;">
                        <span class="dotted-span" style="width: 200px; text-align: center; padding-bottom: 2px;">{{ $manpowerRequest->ceo_approved_at || $manpowerRequest->rejected_at ? 'พิจารณาผ่านระบบ' : '' }}</span><br>
                        <div style="margin-top: 8px; margin-bottom: 2px;">
                            ( <span style="display: inline-block; width: 180px; text-align: center;">{{ $manpowerRequest->ceo_approved_at && $manpowerRequest->ceoApprover ? $manpowerRequest->ceoApprover->firstname . ' ' . $manpowerRequest->ceoApprover->lastname : '' }}</span> )
                        </div>
                        <span style="font-weight: bold; font-size: 9.5pt;">ประธานเจ้าหน้าที่บริหาร</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Red Warning Note -->
    <div style="color: red; font-weight: bold; font-size: 8.5pt; margin-top: 8px; margin-bottom: 25px;">
        หมายเหตุ : ใบ Request กำลังคนต้องขออนุมัติล่วงหน้า 30 วัน และกำหนดคุณสมบัติให้ละเอียด
    </div>

    <!-- HR Record Note -->
    <div style="margin-top: 10px; font-size: 9.5pt;">
        <b>สำหรับเจ้าหน้าที่ฝ่ายบุคคลบันทึก :</b><br>
        <div style="margin-left: 15px; margin-top: 2px;" class="nowrap">
            ได้รับพนักงาน (รหัสพนักงาน) <span class="dotted-span" style="width: 90px; text-align: center;">{{ $manpowerRequest->onboard_employee_code ?? '' }}</span> 
            (ชื่อ-สกุล) <span class="dotted-span" style="width: 180px; text-align: center;">{{ $manpowerRequest->onboard_employee_name ?? '' }}</span> 
            เข้าทำงานในวันที่ <span class="dotted-span" style="width: 90px; text-align: center;">{{ $manpowerRequest->onboard_date ? \Carbon\Carbon::parse($manpowerRequest->onboard_date)->format('d/m/Y') : '' }}</span>
        </div>
    </div>

    <!-- Footer Code -->
    <div style="text-align: right; font-size: 8pt; margin-top: 32px;">
        QF-HR-13 Rev.08 : 01-06-25
    </div>

</body>
</html>
