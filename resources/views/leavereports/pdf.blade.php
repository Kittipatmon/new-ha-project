<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 24px;
        }

        @font-face {
            font-family: 'THSarabun';
            font-style: normal;
            font-weight: 400;
            src: url('{{ public_path('fonts/THSarabun.ttf') }}') format('truetype');
        }

        @font-face {
            font-family: 'THSarabun';
            font-style: italic;
            font-weight: 400;
            src: url("{{ public_path('fonts/THSarabun Italic.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: 'THSarabun';
            font-style: normal;
            font-weight: 700;
            src: url("{{ public_path('fonts/THSarabun Bold.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: 'THSarabun';
            font-style: italic;
            font-weight: 700;
            src: url("{{ public_path('fonts/THSarabun BoldItalic.ttf') }}") format('truetype');
        }

        body {
            font-family: 'THSarabun', sans-serif;
            font-size: 14px;
            color: #111;
        }

        h1 {
            font-size: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 16px;
            margin: 15px 0 5px;
            background-color: #f3f4f6;
            padding: 5px;
            border-left: 4px solid #3b82f6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-row-group;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: right;
        }

        th {
            background: #e5e7eb;
            text-align: center;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .summary-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            background-color: #f9fafb;
        }

        .summary-text {
            font-size: 16px;
            font-weight: bold;
            margin: 0 15px;
        }

        header {
            position: fixed;
            top: -10px;
            right: 0px;
            font-size: 12px;
            color: #555;
            text-align: right;
        }

        .pagenum:before {
            content: "หน้า " counter(page);
        }
    </style>
</head>

<body>
    <header>
        <span class="pagenum"></span>
    </header>
    <table style="width: 100%; border: none; margin-bottom: 20px;">
        <tr>
            <td style="width: 20%; text-align: left; border: none; padding: 0;">
                @php
                    $logoPath = public_path('images/logos/th-kumwell-logo.png');
                    $logoData = base64_encode(file_get_contents($logoPath));
                    $logoSrc = 'data:image/png;base64,' . $logoData;
                @endphp
                <img src="{{ $logoSrc }}" alt="Logo" style="height: 30px;">
            </td>
            <td style="width: 60%; text-align: center; border: none; padding: 0; vertical-align: middle;">
                <h1 style="margin: 0; font-size: 20px;">{{ $title }}</h1>
            </td>
            <td style="width: 20%; border: none; padding: 0;"></td>
        </tr>
    </table>

    <div class="summary-box">
        <span class="summary-text">พนักงานทั้งหมด: {{ number_format($total_company_employees) }} คน</span>
        <span class="summary-text">วันทำงานทั้งหมด: {{ number_format($total_company_working_days) }} วัน</span>
        <span class="summary-text" style="color: red;">รวมวันลาทั้งหมด: {{ number_format($total_company_leave_days) }}
            วัน</span>
    </div>

    @foreach($summary as $div => $data)
        <div style="page-break-inside: avoid;">
            <h2>สายงาน: {{ $div }} | พนักงาน: {{ number_format($data['total_employees']) }} คน | สรุปวันลา: <span
                    style="color: red;">{{ number_format($data['total_leave_days'], 1) }} วัน</span></h2>

            <table>
                <thead>
                    <tr>
                        <th rowspan="2" class="text-left" style="width: 12%">เดือนที่รายงาน</th>
                        <th rowspan="2" style="width: 10%">วันทำงาน<br>(คน/วัน)</th>
                        <th rowspan="2" style="width: 10%">วันทำงานทั้งหมด</th>
                        <th rowspan="2" style="width: 10%">รวมวันลา</th>
                        <th colspan="5" style="width: 58%">แยกตามประเภท (วัน/ครั้ง)</th>
                    </tr>
                    <tr>
                        <th>ลาป่วย</th>
                        <th>ลากิจ</th>
                        <th>ลาพักร้อน</th>
                        <th>ลาคลอด</th>
                        <th>อื่นๆ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['months'] as $m)
                        <tr>
                            <td class="text-left">
                                @php
                                    $monthNames = [
                                        '01' => 'มกราคม',
                                        '02' => 'กุมภาพันธ์',
                                        '03' => 'มีนาคม',
                                        '04' => 'เมษายน',
                                        '05' => 'พฤษภาคม',
                                        '06' => 'มิถุนายน',
                                        '07' => 'กรกฎาคม',
                                        '08' => 'สิงหาคม',
                                        '09' => 'กันยายน',
                                        '10' => 'ตุลาคม',
                                        '11' => 'พฤศจิกายน',
                                        '12' => 'ธันวาคม'
                                    ];
                                    $y = substr($m->report_month, 0, 4);
                                    $mon = substr($m->report_month, 5, 2);
                                    $thYear = intval($y) + 543;
                                    $strMonthThai = isset($monthNames[$mon]) ? $monthNames[$mon] : $mon;
                                    echo $strMonthThai . ' ' . $thYear;
                                @endphp
                            </td>
                            <td>{{ number_format($m->working_days) }}</td>
                            <td>{{ number_format($m->total_working_days) }}</td>
                            <td style="color: red; font-weight:bold;">{{ number_format($m->total_leave_days, 1) }}</td>

                            <td>{{ number_format($m->sick_days, 1) }}<br><span
                                    style="font-size:10px; color:#555;">{{ $m->sick_times }} ครั้ง</span></td>
                            <td>{{ number_format($m->personal_days, 1) }}<br><span
                                    style="font-size:10px; color:#555;">{{ $m->personal_times }} ครั้ง</span></td>
                            <td>{{ number_format($m->annual_days, 1) }}<br><span
                                    style="font-size:10px; color:#555;">{{ $m->annual_times }} ครั้ง</span></td>
                            <td>{{ number_format($m->maternity_days, 1) }}<br><span
                                    style="font-size:10px; color:#555;">{{ $m->maternity_times }} ครั้ง</span></td>
                            <td>{{ number_format($m->other_days, 1) }}<br><span
                                    style="font-size:10px; color:#555;">{{ $m->other_times }} ครั้ง</span></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #fdfbfb; font-weight:bold;">
                        <td class="text-left">รวม ({{ $div }})</td>
                        <td>-</td>
                        <td>{{ number_format($data['total_working_days']) }}</td>
                        <td style="color: red;">{{ number_format($data['total_leave_days'], 1) }}</td>
                        <td>{{ number_format($data['sick_days'], 1) }}</td>
                        <td>{{ number_format($data['personal_days'], 1) }}</td>
                        <td>{{ number_format($data['annual_days'], 1) }}</td>
                        <td>{{ number_format($data['maternity_days'], 1) }}</td>
                        <td>{{ number_format($data['other_days'], 1) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endforeach

    <div style="text-align: right; margin-top: 30px; font-size: 12px; color: #555;">
        @php
            $printedDate = \Carbon\Carbon::now()->timezone('Asia/Bangkok');
            $printYear = $printedDate->format('Y') + 543;
            $printPatt = $printedDate->format('d/m/') . $printYear . ' เวลา ' . $printedDate->format('H:i');
        @endphp
        พิมพ์ข้อมูลเมื่อวันที่: {{ $printPatt }} น.
    </div>
</body>

</html>