<!DOCTYPE html>
<html lang="th">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>แบบประเมินผลการสัมภาษณ์ผู้สมัครงาน PDF</title>
    <style>
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url("{{ public_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url("{{ public_path('fonts/THSarabunNew-Bold.ttf') }}") format('truetype');
        }
        body {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 16pt;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .header-title {
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .table-matrix {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .table-matrix th, .table-matrix td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            font-size: 14pt;
        }
        .text-left {
            text-align: left !important;
        }
        .font-bold {
            font-weight: bold;
        }
        .border-bottom-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            padding-left: 5px;
            padding-right: 5px;
        }
        .footer-doc {
            text-align: right;
            font-size: 12pt;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header-title">แบบประเมินผลการสัมภาษณ์ผู้สมัครงาน</div>

    <div style="text-align: right; margin-bottom: 10px;">
        วันที่ <span class="border-bottom-line" style="width: 120px; text-align: center;">{{ $evaluation->evaluation_date ? $evaluation->evaluation_date->format('d/m/Y') : '-' }}</span>
    </div>

    <table style="width: 100%; margin-bottom: 10px;">
        <tr>
            <td style="width: 55%;">
                (นาย/นาง/นางสาว) <span class="border-bottom-line" style="width: 250px;">{{ $evaluation->full_candidate_name }}</span>
            </td>
            <td style="width: 45%;">
                ตำแหน่งที่สมัคร <span class="border-bottom-line" style="width: 180px;">{{ $evaluation->position_applied ?? '-' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                แผนก <span class="border-bottom-line" style="width: 120px;">{{ $evaluation->department ?? '-' }}</span>
                ฝ่าย <span class="border-bottom-line" style="width: 120px;">{{ $evaluation->division ?? '-' }}</span>
            </td>
            <td>
                สัมภาษณ์ครั้งที่ <span class="border-bottom-line" style="width: 180px; text-align: center;">{{ $evaluation->interview_times }}</span>
            </td>
        </tr>
    </table>

    <table class="table-matrix">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th rowspan="2" style="width: 6%;">ลำดับ</th>
                <th rowspan="2" style="width: 54%;">หัวข้อในการพิจารณา</th>
                <th colspan="4" style="width: 20%;">❶ ฝ่ายบุคคล</th>
                <th colspan="4" style="width: 20%;">❷ ต้นสังกัด</th>
            </tr>
            <tr style="background-color: #f2f2f2; font-size: 12pt;">
                <th>1</th><th>2</th><th>3</th><th>4</th>
                <th>1</th><th>2</th><th>3</th><th>4</th>
            </tr>
        </thead>
        <tbody>
            @php
                $topics = \App\Http\Controllers\InterviewEvaluationController::$topics;
            @endphp
            @foreach($topics as $index => $topicText)
                @php
                    $scoreRecord = $scoresByItem[$index] ?? null;
                    $hrScore = $scoreRecord ? $scoreRecord->hr_score : null;
                    $deptScore = $scoreRecord ? $scoreRecord->dept_score : null;
                @endphp
                <tr>
                    <td>{{ $index }}</td>
                    <td class="text-left" style="font-size: 13pt;">{{ $topicText }}</td>
                    
                    <td>{{ $hrScore == 1 ? '✓' : '' }}</td>
                    <td>{{ $hrScore == 2 ? '✓' : '' }}</td>
                    <td>{{ $hrScore == 3 ? '✓' : '' }}</td>
                    <td>{{ $hrScore == 4 ? '✓' : '' }}</td>

                    <td>{{ $deptScore == 1 ? '✓' : '' }}</td>
                    <td>{{ $deptScore == 2 ? '✓' : '' }}</td>
                    <td>{{ $deptScore == 3 ? '✓' : '' }}</td>
                    <td>{{ $deptScore == 4 ? '✓' : '' }}</td>
                </tr>
            @endforeach
            <tr class="font-bold" style="background-color: #f9f9f9;">
                <td colspan="2">รวมคะแนนแต่ละหัวข้อ</td>
                <td colspan="4">{{ $evaluation->total_hr_score }}</td>
                <td colspan="4">{{ $evaluation->total_dept_score }}</td>
            </tr>
            <tr class="font-bold" style="background-color: #f9f9f9;">
                <td colspan="2">รวมคะแนนทั้งหมด</td>
                <td colspan="8" class="text-left" style="padding-left: 10px;">{{ $evaluation->grand_total_score }} คะแนน</td>
            </tr>
            <tr class="font-bold" style="background-color: #f9f9f9;">
                <td colspan="2">คะแนนรวม (คะแนน 1+2 หารสอง)</td>
                <td colspan="8" class="text-left" style="padding-left: 10px;">{{ number_format($evaluation->average_score, 1) }} คะแนน</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 10px;">
        <strong>หมายเหตุ :</strong> {{ $evaluation->remarks ?? '-' }}
    </div>

    <div style="margin-top: 10px; border: 1px solid #000; padding: 8px;">
        <strong>สรุปผลการสัมภาษณ์ :</strong><br>
        <div>[{{ $evaluation->summary_result == 'hire' ? '✓' : '  ' }}] ควรว่าจ้างในตำแหน่งที่สมัคร (30 – 40 คะแนน)</div>
        <div>[{{ $evaluation->summary_result == 'reserve' ? '✓' : '  ' }}] ควรสำรองไว้กรณีมีการร้องขอพนักงาน (20 – 29 คะแนน)</div>
        <div>[{{ $evaluation->summary_result == 'reject' ? '✓' : '  ' }}] ปฏิเสธการว่าจ้างเป็นพนักงาน (ต่ำกว่า 20 คะแนน)</div>
    </div>

    <table style="width: 100%; margin-top: 25px;">
        <tr>
            <td style="width: 50%; text-align: center;">
                ลงชื่อ ........................................................... ฝ่ายบุคคล<br>
                ( {{ $evaluation->hr_evaluator_name ?? '...........................................................' }} )<br>
                ตำแหน่ง {{ $evaluation->hr_position ?? '...........................................................' }}<br>
                วัน/เดือน/ปี {{ $evaluation->hr_signed_date ? $evaluation->hr_signed_date->format('d/m/Y') : '.......................................' }}
            </td>
            <td style="width: 50%; text-align: center;">
                ลงชื่อ ........................................................... ต้นสังกัด<br>
                ( {{ $evaluation->dept_evaluator_name ?? '...........................................................' }} )<br>
                ตำแหน่ง {{ $evaluation->dept_position ?? '...........................................................' }}<br>
                วัน/เดือน/ปี {{ $evaluation->dept_signed_date ? $evaluation->dept_signed_date->format('d/m/Y') : '.......................................' }}
            </td>
        </tr>
    </table>

    <div class="footer-doc">
        QF-HR-25 Rev.04 : 31-07-26
    </div>
</body>
</html>
