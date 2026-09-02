<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แบบประเมินผลการปฏิบัติงานระหว่างทดลองงาน</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@400;600;700&display=swap');
        
        @page {
            size: A4 portrait;
            margin: 8mm 10mm 8mm 10mm;
        }

        body {
            font-family: 'Sarabun', sans-serif;
            font-size: 8.5pt;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .dotted-span {
            border-bottom: 1px dotted #000;
            display: inline-block;
            line-height: 1.1;
        }

        .checkbox-box {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 1px solid #000;
            vertical-align: middle;
            margin-right: 3px;
            text-align: center;
            line-height: 15px;
            font-size: 16pt;
        }

        .nowrap {
            white-space: nowrap;
        }

        .page-break {
            page-break-after: always;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        /* Bordered tables for absences & exams */
        .bordered-table {
            border: 1px solid #000;
        }
        .bordered-table th, .bordered-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            font-size: 8pt;
            vertical-align: middle;
        }
        .bordered-table th {
            font-weight: normal;
        }

        /* Signatures Grid */
        .sig-table td {
            padding: 10px;
            vertical-align: bottom;
        }
        .sig-line {
            display: inline-block;
            width: 140px;
            text-align: center;
            border-bottom: 1px dotted #000;
            padding-bottom: 2px;
            vertical-align: bottom;
            margin: 0 5px;
            position: relative;
            top: -4px;
        }
        .sig-line img {
            height: 35px;
            margin-bottom: -8px;
        }

        /* Textarea underline replacement */
        .lined-box {
            min-height: 54px;
            width: 100%;
            line-height: 18px;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="4" height="18"><rect x="0" y="17" width="1.5" height="1" fill="%23888"/></svg>');
            padding: 2px 5px;
            font-size: 8.5pt;
        }
    </style>
</head>
<body>

    <!-- ==================== PAGE 1 / 3 ==================== -->
    <div class="page-header">
        <table style="margin-bottom: 5px;">
            <tr>
                <td>แบบประเมินเลขที่ <span class="dotted-span" style="width: 150px; text-align: center;">{{ 'PE-' . date('Y', strtotime($probationEvaluation->created_at)) . '-' . str_pad($probationEvaluation->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                <td class="text-right text-gray-500">1 / 3</td>
            </tr>
        </table>
    </div>

    <div class="text-center font-bold" style="font-size: 13pt; margin-bottom: 30px; margin-top:20px;">
        แบบประเมินผลการปฏิบัติงานระหว่างทดลองงาน
    </div>

    <!-- Employee Info Section -->
    <table style="width: 100%; margin-bottom: 8px; font-size: 8.5pt; border-collapse: collapse;">
        <tr>
            <td colspan="2" style="width: 50%;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="white-space: nowrap; width: 1%;">ชื่อ-นามสกุล (&nbsp;&nbsp;{{ $probationEvaluation->prefix ?? '' }}&nbsp;&nbsp;)&nbsp;</td>
                        <td style="border-bottom: 1px dotted #000; text-align: center; vertical-align: bottom;">{{ $probationEvaluation->employee_name }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 8%; white-space: nowrap; padding-left: 8px;">ตำแหน่ง&nbsp;</td>
            <td style="width: 20%;"><span class="dotted-span" style="width: 100%; text-align: center;">{{ $probationEvaluation->position }}</span></td>
            <td style="width: 10%; white-space: nowrap; padding-left: 8px;">รหัสพนักงาน&nbsp;</td>
            <td style="width: 12%;"><span class="dotted-span" style="width: 100%; text-align: center;">{{ $probationEvaluation->emp_code }}</span></td>
        </tr>
    </table>
    <table style="width: 100%; margin-bottom: 12px; font-size: 8.5pt; border-collapse: collapse;">
        <tr>
            <td style="white-space: nowrap;">แผนก/ ฝ่าย&nbsp;</td>
            <td style="width: 35%;"><span class="dotted-span" style="width: 100%; text-align: center;">{{ $probationEvaluation->department }}</span></td>
            <td style="white-space: nowrap; padding-left: 12px;">วันที่เริ่มงาน&nbsp;</td>
            <td style="width: 15%;"><span class="dotted-span" style="width: 100%; text-align: center;">{{ $probationEvaluation->start_date ? \Carbon\Carbon::parse($probationEvaluation->start_date)->format('d/m/Y') : '-' }}</span></td>
            <td style="white-space: nowrap; padding-left: 12px;">วันที่ครบทดลองงาน&nbsp;</td>
            <td style="width: 15%;"><span class="dotted-span" style="width: 100%; text-align: center;">{{ $probationEvaluation->probation_due_date ? \Carbon\Carbon::parse($probationEvaluation->probation_due_date)->format('d/m/Y') : '-' }}</span></td>
        </tr>
    </table>

    <!-- 1.1 Absence Record -->
    <div class="font-bold" style="margin-bottom: 2px;">1.1 สถิติการหยุดงาน</div>
    <table class="bordered-table text-center" style="margin-bottom: 10px;">
        <thead>
            <tr>
                <th style="width: 45%;" rowspan="2">รอบการประเมิน</th>
                <th style="width: 10%;" rowspan="2">ลากิจ</th>
                <th style="width: 10%;" rowspan="2">ลาป่วย</th>
                <th style="width: 10%;" rowspan="2">ขาดงาน</th>
                <th colspan="2">สาย</th>
            </tr>
            <tr>
                <th style="width: 12%;">ครั้ง</th>
                <th style="width: 13%;">นาที</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 1; $i <= 3; $i++)
            <tr>
                <td style="text-align: left; padding-left: 8px;">
                    ครั้งที่ {{ $i }} จาก <span class="dotted-span" style="width: 80px; text-align: center;">{{ isset($absences[$i]) ? $absences[$i]->start_date : '' }}</span> ถึง <span class="dotted-span" style="width: 80px; text-align: center;">{{ isset($absences[$i]) ? $absences[$i]->end_date : '' }}</span>
                </td>
                <td>{{ isset($absences[$i]) ? $absences[$i]->business_leave : '' }}</td>
                <td>{{ isset($absences[$i]) ? $absences[$i]->sick_leave : '' }}</td>
                <td>{{ isset($absences[$i]) ? $absences[$i]->absent : '' }}</td>
                <td>{{ isset($absences[$i]) ? $absences[$i]->late_count : '' }}</td>
                <td>{{ isset($absences[$i]) ? $absences[$i]->late_mins : '' }}</td>
            </tr>
            @endfor
        </tbody>
    </table>

    <!-- 1.2 Knowledge Exam -->
    <div class="font-bold" style="margin-bottom: 2px;">1.2 การสอบวัดความรู้</div>
    <table class="bordered-table" style="margin-bottom: 10px;">
        <thead>
            <tr>
                <th style="width: 45%; text-align: left; padding-left: 8px;" rowspan="2">หัวข้อการทดสอบ</th>
                <th colspan="3" style="width: 15%;">สอบผ่านในครั้งที่</th>
                <th style="width: 15%;" rowspan="2">วันที่สอบผ่าน</th>
                <th style="width: 25%;" rowspan="2">ผู้ทดสอบ<br>(ฝ่ายทรัพยากรบุคคล)</th>
            </tr>
            <tr>
                <th style="width: 5%;">1</th>
                <th style="width: 5%;">2</th>
                <th style="width: 5%;">3</th>
            </tr>
        </thead>
        <tbody>
            @php
            $topics = [
                '1. การใช้งานคอมพิวเตอร์และ IT เบื้องต้น',
                '2. กฎระเบียบของบริษัท',
                '3. ทิศทางการบริหาร นโยบาย วิสัยทัศน์ พันธกิจ ของบริษัท',
                '4. ผลิตภัณฑ์ของบริษัท',
                '5. การทำ Creating Shared Value (CSV) ของบริษัท',
                '6. การทำ 5 ส',
                '7. อื่นๆ ' . ($probationEvaluation->exam_other_topic ?? '')
            ];
            @endphp
            @foreach($topics as $index => $topic)
            <tr>
                <td style="padding-left: 8px;">{{ $topic }}</td>
                <td class="text-center">@if(isset($exams[$index]) && $exams[$index]->passed_round == '1') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</td>
                <td class="text-center">@if(isset($exams[$index]) && $exams[$index]->passed_round == '2') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</td>
                <td class="text-center">@if(isset($exams[$index]) && $exams[$index]->passed_round == '3') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</td>
                <td class="text-center">{{ (isset($exams[$index]) && $exams[$index]->exam_date) ? \Carbon\Carbon::parse($exams[$index]->exam_date)->format('d/m/Y') : '' }}</td>
                <td class="text-center">{{ isset($exams[$index]) ? $exams[$index]->exam_tester : '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- 1.3 Performance Evaluation (Round 1) -->
    <div class="font-bold" style="margin-bottom: 2px;">1.3 การปฏิบัติงาน</div>
    <div class="bordered-table" style="border: 0px solid #000; margin-bottom: 5px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 5px 8px; vertical-align: top;">
                    <div class="font-bold">ครั้งที่ 1 : งานที่มอบหมาย (30 วัน)</div>
                    <div class="lined-box">{{ $probationEvaluation->tasks_assigned }}</div>
                </td>
                <td style="width: 50%; border-bottom: 1px solid #000; padding: 5px 8px; vertical-align: top;">
                    <div class="font-bold">ครั้งที่ 1 : ผลการปฏิบัติงาน</div>
                    <div class="lined-box">{{ $probationEvaluation->performance_result }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom: 1px solid #000; padding: 5px 8px;">
                    <span class="font-bold">ระดับผลการดำเนินงานที่มอบหมาย : </span> &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="checkbox-box">@if(($probationEvaluation->performance_level) == 'ต้องปรับปรุง') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</span> ต้องปรับปรุง &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="checkbox-box">@if(($probationEvaluation->performance_level) == 'สำเร็จตามที่คาดหมาย') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</span> สำเร็จตามที่คาดหมาย &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="checkbox-box">@if(($probationEvaluation->performance_level) == 'เกินความคาดหมาย') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</span> เกินความคาดหมาย
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom: 1px solid #000; padding: 5px 8px;">
                    <span class="font-bold">ผลการประเมินครั้งที่ 1 : </span> &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="checkbox-box">@if(($probationEvaluation->evaluation_result) == 'ทดลองงานต่อ') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</span> ทดลองงานต่อ &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="checkbox-box">@if(($probationEvaluation->evaluation_result) == 'ผ่านการทดลองงาน') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</span> ผ่านการทดลองงาน &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="checkbox-box">@if(($probationEvaluation->evaluation_result) == 'ไม่ผ่านการทดลองงาน') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</span> ไม่ผ่านการทดลองงาน เนื่องจาก
                    <span class="dotted-span" style="width: 100px;">{{ $probationEvaluation->evaluation_result_reason ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom: 1px solid #000; padding: 5px 8px;">
                    <div class="font-bold">สรุปความเห็นผู้ประเมิน/ข้อเสนอแนะเพื่อปรับปรุง</div>
                    <div class="lined-box" style="min-height: 28px; padding-top: 3px;">{{ $probationEvaluation->evaluator_comment }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 5px 8px;">
                    <div class="font-bold">สรุปความเห็นฝ่ายทรัพยากรบุคคล</div>
                    <div class="lined-box" style="min-height: 28px; padding-top: 3px;">{{ $probationEvaluation->hr_comment }}</div>
                </td>
            </tr>
        </table>
    </div>



    <table style="width: 100%; margin-top: 50px; font-size: 7.5pt; color: #444;">
        <tr>
            <td>บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)</td>
            <td class="text-right">QF-HR-18 Rev.09 : 02-05-25</td>
        </tr>
    </table>

    <!-- Page break to Page 2 -->
    <div class="page-break"></div>

    <!-- ==================== PAGE 2 / 3 ==================== -->
    <div class="page-header">
        <table style="margin-bottom: 10px;">
            <tr>
                <td>แบบประเมินเลขที่ <span class="dotted-span" style="width: 150px; text-align: center;">{{ 'PE-' . date('Y', strtotime($probationEvaluation->created_at)) . '-' . str_pad($probationEvaluation->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                <td class="text-right text-gray-500">2 / 3</td>
            </tr>
        </table>
    </div>

    <div class="text-center font-bold" style="font-size: 15pt; margin-bottom: 20px;">
        แบบประเมินผลการปฏิบัติงานระหว่างทดลองงาน
    </div>

    <!-- Page 1 Signatures (Moved to top of Page 2) -->
    <div style="border: 1px solid #000; padding: 15px; margin-bottom: 20px;">
        <table class="sig-table text-center" style="width: 100%; font-size: 8.5pt;">
            <tr>
                <td style="width: 50%; padding-bottom: 35px;">
                    ลงชื่อ 
                    <span class="sig-line" style="font-family: 'Sarabun', sans-serif; font-size: 10pt; color: #333;">
                        {{ $probationEvaluation->evaluatee_name ?? '' }}
                    </span>
                     ผู้รับการประเมิน
                    <div style="margin-top: 10px;">
                        ( <span style="display: inline-block; width: 180px; text-align: center;">{{ $probationEvaluation->evaluatee_name ?? '' }}</span> )
                    </div>
                </td>
                <td style="width: 50%; padding-bottom: 35px;">
                    ลงชื่อ 
                    <span class="sig-line" style="font-family: 'Sarabun', sans-serif; font-size: 10pt; color: #333;">
                        {{ $probationEvaluation->evaluator_name ?? '' }}
                    </span>
                     ผู้ประเมิน
                    <div style="margin-top: 10px;">
                        ( <span style="display: inline-block; width: 180px; text-align: center;">{{ $probationEvaluation->evaluator_name ?? '' }}</span> )
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;">
                    ลงชื่อ 
                    <span class="sig-line" style="font-family: 'Sarabun', sans-serif; font-size: 10pt; color: #333;">
                        {{ $probationEvaluation->manager_name ?? '' }}
                    </span>
                     ผู้จัดการฝ่าย/แผนก
                    <div style="margin-top: 10px;">
                        ( <span style="display: inline-block; width: 180px; text-align: center;">{{ $probationEvaluation->manager_name ?? '' }}</span> )
                    </div>
                </td>
                <td style="width: 50%;">
                    ลงชื่อ 
                    <span class="sig-line" style="font-family: 'Sarabun', sans-serif; font-size: 10pt; color: #333;">
                        {{ $probationEvaluation->hr_name ?? '' }}
                    </span>
                     ฝ่ายทรัพยากรบุคคล
                    <div style="margin-top: 10px;">
                        ( <span style="display: inline-block; width: 180px; text-align: center;">{{ $probationEvaluation->hr_name ?? '' }}</span> )
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- 1.4 Performance Evaluation (Round 2) -->
    <div class="bordered-table" style="border: 0px solid #000; margin-bottom: 20px; margin-top: 10px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 8px; vertical-align: top;">
                    <div class="font-bold">ครั้งที่ 2 : งานที่มอบหมาย (60 วัน)</div>
                    <div class="lined-box">{{ $probationEvaluation->tasks_assigned_2 ?? '' }}</div>
                </td>
                <td style="width: 50%; border-bottom: 1px solid #000; padding: 8px; vertical-align: top;">
                    <div class="font-bold">ครั้งที่ 2 : ผลการปฏิบัติงาน</div>
                    <div class="lined-box">{{ $probationEvaluation->performance_result_2 ?? '' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom: 1px solid #000; padding: 8px;">
                    <span class="font-bold">ระดับผลการดำเนินงานที่มอบหมาย : </span> &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="checkbox-box">@if(($probationEvaluation->performance_level_2 ?? '') == 'ต้องปรับปรุง') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</span> ต้องปรับปรุง &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="checkbox-box">@if(($probationEvaluation->performance_level_2 ?? '') == 'สำเร็จตามที่คาดหมาย') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</span> สำเร็จตามที่คาดหมาย &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="checkbox-box">@if(($probationEvaluation->performance_level_2 ?? '') == 'เกินความคาดหมาย') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</span> เกินความคาดหมาย
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom: 1px solid #000; padding: 8px;">
                    <span class="font-bold">ผลการประเมินครั้งที่ 2 : </span> &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="checkbox-box">@if(($probationEvaluation->evaluation_result_2 ?? '') == 'ทดลองงานต่อ') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</span> ทดลองงานต่อ &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="checkbox-box">@if(($probationEvaluation->evaluation_result_2 ?? '') == 'ผ่านการทดลองงาน') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</span> ผ่านการทดลองงาน &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="checkbox-box">@if(($probationEvaluation->evaluation_result_2 ?? '') == 'ไม่ผ่านการทดลองงาน') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</span> ไม่ผ่านการทดลองงาน เนื่องจาก
                    <span class="dotted-span" style="width: 100px;">{{ $probationEvaluation->evaluation_result_reason_2 ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom: 1px solid #000; padding: 8px;">
                    <div class="font-bold">สรุปความเห็นผู้ประเมิน/ข้อเสนอแนะเพื่อปรับปรุง</div>
                    <div class="lined-box" style="min-height: 50px; padding-top: 5px;">{{ $probationEvaluation->evaluator_comment_2 ?? '' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 8px;">
                    <div class="font-bold">สรุปความเห็นฝ่ายทรัพยากรบุคคล</div>
                    <div class="lined-box" style="min-height: 50px; padding-top: 5px;">{{ $probationEvaluation->hr_comment_2 ?? '' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Page 2 Signatures -->
    <div style="border: 1px solid #000; padding: 15px; margin-top: 15px;">
        <table class="sig-table text-center" style="width: 100%; font-size: 8.5pt;">
            <tr>
                <td style="width: 50%; padding-bottom: 35px;">
                    ลงชื่อ 
                    <span class="sig-line" style="font-family: 'Sarabun', sans-serif; font-size: 10pt; color: #333;">
                        {{ $probationEvaluation->evaluatee_name ?? '' }}
                    </span>
                     ผู้รับการประเมิน
                    <div style="margin-top: 10px;">
                        ( <span style="display: inline-block; width: 180px; text-align: center;">{{ $probationEvaluation->evaluatee_name ?? '' }}</span> )
                    </div>
                </td>
                <td style="width: 50%; padding-bottom: 35px;">
                    ลงชื่อ 
                    <span class="sig-line" style="font-family: 'Sarabun', sans-serif; font-size: 10pt; color: #333;">
                        {{ $probationEvaluation->evaluator_name ?? '' }}
                    </span>
                     ผู้ประเมิน
                    <div style="margin-top: 10px;">
                        ( <span style="display: inline-block; width: 180px; text-align: center;">{{ $probationEvaluation->evaluator_name ?? '' }}</span> )
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;">
                    ลงชื่อ 
                    <span class="sig-line" style="font-family: 'Sarabun', sans-serif; font-size: 10pt; color: #333;">
                        {{ $probationEvaluation->manager_name ?? '' }}
                    </span>
                     ผู้จัดการฝ่าย/แผนก
                    <div style="margin-top: 10px;">
                        ( <span style="display: inline-block; width: 180px; text-align: center;">{{ $probationEvaluation->manager_name ?? '' }}</span> )
                    </div>
                </td>
                <td style="width: 50%;">
                    ลงชื่อ 
                    <span class="sig-line" style="font-family: 'Sarabun', sans-serif; font-size: 10pt; color: #333;">
                        {{ $probationEvaluation->hr_name ?? '' }}
                    </span>
                     ฝ่ายทรัพยากรบุคคล
                    <div style="margin-top: 10px;">
                        ( <span style="display: inline-block; width: 180px; text-align: center;">{{ $probationEvaluation->hr_name ?? '' }}</span> )
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table style="width: 100%; margin-top: 50px; font-size: 7.5pt; color: #444;">
        <tr>
            <td>บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)</td>
            <td class="text-right">QF-HR-18 Rev.09 : 02-05-25</td>
        </tr>
    </table>

    <!-- Page break to Page 3 -->
    <div class="page-break"></div>

    <!-- ==================== PAGE 3 / 3 ==================== -->
    <div class="page-header">
        <table style="margin-bottom: 10px;">
            <tr>
                <td>แบบประเมินเลขที่ <span class="dotted-span" style="width: 150px; text-align: center;">{{ 'PE-' . date('Y', strtotime($probationEvaluation->created_at)) . '-' . str_pad($probationEvaluation->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                <td class="text-right text-gray-500">3 / 3</td>
            </tr>
        </table>
    </div>

    <div class="text-center font-bold" style="font-size: 15pt; margin-bottom: 20px;">
        แบบประเมินผลการปฏิบัติงานระหว่างทดลองงาน
    </div>

    <!-- 1.5 Performance Evaluation (Round 3) -->
    <div class="bordered-table" style="border: 0px solid #000; margin-bottom: 20px; margin-top: 10px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; border-right: 1px solid #000; border-bottom: 1px solid #000; padding: 8px; vertical-align: top;">
                    <div class="font-bold">ครั้งที่ 3 : งานที่มอบหมาย (90 วัน)</div>
                    <div class="lined-box">{{ $probationEvaluation->tasks_assigned_3 ?? '' }}</div>
                </td>
                <td style="width: 50%; border-bottom: 1px solid #000; padding: 8px; vertical-align: top;">
                    <div class="font-bold">ครั้งที่ 3 : ผลการปฏิบัติงาน</div>
                    <div class="lined-box">{{ $probationEvaluation->performance_result_3 ?? '' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom: 1px solid #000; padding: 8px;">
                    <span class="font-bold">ระดับผลการดำเนินงานที่มอบหมาย : </span> &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="checkbox-box">@if(($probationEvaluation->performance_level_3 ?? '') == 'ต้องปรับปรุง') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</span> ต้องปรับปรุง &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="checkbox-box">@if(($probationEvaluation->performance_level_3 ?? '') == 'สำเร็จตามที่คาดหมาย') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</span> สำเร็จตามที่คาดหมาย &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="checkbox-box">@if(($probationEvaluation->performance_level_3 ?? '') == 'เกินความคาดหมาย') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</span> เกินความคาดหมาย
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom: 1px solid #000; padding: 8px;">
                    <span class="font-bold">ผลการประเมินครั้งที่ 3 : </span> &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="checkbox-box">@if(($probationEvaluation->evaluation_result_3 ?? '') == 'ทดลองงานต่อ') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</span> ทดลองงานต่อ &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="checkbox-box">@if(($probationEvaluation->evaluation_result_3 ?? '') == 'ผ่านการทดลองงาน') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</span> ผ่านการทดลองงาน &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="checkbox-box">@if(($probationEvaluation->evaluation_result_3 ?? '') == 'ไม่ผ่านการทดลองงาน') <span style="font-family: DejaVu Sans, sans-serif; font-size: 14pt; font-weight: bold;">&#10003;</span> @endif</span> ไม่ผ่านการทดลองงาน เนื่องจาก
                    <span class="dotted-span" style="width: 100px;">{{ $probationEvaluation->evaluation_result_reason_3 ?? '' }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom: 1px solid #000; padding: 8px;">
                    <div class="font-bold">สรุปความเห็นผู้ประเมิน/ข้อเสนอแนะเพื่อปรับปรุง</div>
                    <div class="lined-box" style="min-height: 50px; padding-top: 5px;">{{ $probationEvaluation->evaluator_comment_3 ?? '' }}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 8px;">
                    <div class="font-bold">สรุปความเห็นฝ่ายทรัพยากรบุคคล</div>
                    <div class="lined-box" style="min-height: 50px; padding-top: 5px;">{{ $probationEvaluation->hr_comment_3 ?? '' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Page 3 Signatures -->
    <div style="border: 1px solid #000; padding: 15px; margin-top: 15px;">
                <table class="sig-table text-center" style="width: 100%; font-size: 8.5pt;">
            <tr>
                <td style="width: 50%; padding-bottom: 35px;">
                    ลงชื่อ 
                    <span class="sig-line" style="font-family: 'Sarabun', sans-serif; font-size: 10pt; color: #333;">
                        {{ $probationEvaluation->evaluatee_name ?? '' }}
                    </span>
                     ผู้รับการประเมิน
                    <div style="margin-top: 10px;">
                        ( <span style="display: inline-block; width: 180px; text-align: center;">{{ $probationEvaluation->evaluatee_name ?? '' }}</span> )
                    </div>
                </td>
                <td style="width: 50%; padding-bottom: 35px;">
                    ลงชื่อ 
                    <span class="sig-line" style="font-family: 'Sarabun', sans-serif; font-size: 10pt; color: #333;">
                        {{ $probationEvaluation->evaluator_name ?? '' }}
                    </span>
                     ผู้ประเมิน
                    <div style="margin-top: 10px;">
                        ( <span style="display: inline-block; width: 180px; text-align: center;">{{ $probationEvaluation->evaluator_name ?? '' }}</span> )
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 50%;">
                    ลงชื่อ 
                    <span class="sig-line" style="font-family: 'Sarabun', sans-serif; font-size: 10pt; color: #333;">
                        {{ $probationEvaluation->manager_name ?? '' }}
                    </span>
                     ผู้จัดการฝ่าย/แผนก
                    <div style="margin-top: 10px;">
                        ( <span style="display: inline-block; width: 180px; text-align: center;">{{ $probationEvaluation->manager_name ?? '' }}</span> )
                    </div>
                </td>
                <td style="width: 50%;">
                    ลงชื่อ 
                    <span class="sig-line" style="font-family: 'Sarabun', sans-serif; font-size: 10pt; color: #333;">
                        {{ $probationEvaluation->hr_name ?? '' }}
                    </span>
                     ฝ่ายทรัพยากรบุคคล
                    <div style="margin-top: 10px;">
                        ( <span style="display: inline-block; width: 180px; text-align: center;">{{ $probationEvaluation->hr_name ?? '' }}</span> )
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Note at bottom -->
    <div style="font-size: 8.5pt; font-weight: bold; margin-top: 15px;">
        * หมายเหตุ : เงื่อนไขการผ่านทดลองงาน ต้องผ่านการสอบวัดความรู้จากฝ่ายทรัพยากรบุคคล ในข้อ 1.2 และผ่านการประเมินผลการปฏิบัติงาน<br>&nbsp;&nbsp;จากหัวหน้างาน/ผู้ประเมิน ในข้อ 1.3
    </div>

    <table style="width: 100%; margin-top: 30px; font-size: 7.5pt; color: #444;">
        <tr>
            <td>บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)</td>
            <td class="text-right">QF-HR-18 Rev.09 : 02-05-25</td>
        </tr>
    </table>

</body>
</html>
