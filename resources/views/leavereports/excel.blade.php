<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body>
    <table>
        <tr>
            <td colspan="7" style="text-align: center; font-size: 16px; font-weight: bold;">
                {{ $title }}
            </td>
        </tr>
        <tr>
            <td colspan="7">
                <b>พนักงานทั้งหมด:</b> {{ number_format($total_company_employees) }} คน |
                <b>วันทำงานทั้งหมด:</b> {{ number_format($total_company_working_days) }} วัน |
                <b>รวมวันลาทั้งหมด:</b> <span style="color: red;">{{ number_format($total_company_leave_days) }}
                    วัน</span>
            </td>
        </tr>
    </table>

    <br>

    @foreach($summary as $div => $data)
        <table>
            <tr>
                <td colspan="7" style="background-color: #d1d5db; font-weight: bold;">
                    สายงาน: {{ $div }} | พนักงาน: {{ number_format($data['total_employees']) }} คน | สรุปวันลา:
                    {{ number_format($data['total_leave_days'], 1) }} วัน
                </td>
            </tr>
            <tr>
                <th style="background-color: #e5e7eb; border: 1px solid #000; text-align: left;">เดือนที่รายงาน</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000;">วันทำงาน (คน/วัน)</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000;">วันทำงานทั้งหมด</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000; color: red;">รวมวันลา</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000;">ลาป่วย</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000;">ลากิจ</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000;">ลาพักร้อน</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000;">ลาคลอด</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000;">อื่นๆ</th>
            </tr>
            @foreach($data['months'] as $m)
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
                    $strMonthThai = (isset($monthNames[$mon]) ? $monthNames[$mon] : $mon) . ' ' . $thYear;
                @endphp
                <tr>
                    <td style="border: 1px solid #000; text-align: left;">{{ $strMonthThai }}</td>
                    <td style="border: 1px solid #000;">{{ number_format($m->working_days) }}</td>
                    <td style="border: 1px solid #000;">{{ number_format($m->total_working_days) }}</td>
                    <td style="border: 1px solid #000; font-weight: bold; color: red;">
                        {{ number_format($m->total_leave_days, 1) }}</td>

                    <td style="border: 1px solid #000;">{{ number_format($m->sick_days, 1) }} ({{ $m->sick_times }} ครั้ง)</td>
                    <td style="border: 1px solid #000;">{{ number_format($m->personal_days, 1) }} ({{ $m->personal_times }}
                        ครั้ง)</td>
                    <td style="border: 1px solid #000;">{{ number_format($m->annual_days, 1) }} ({{ $m->annual_times }} ครั้ง)
                    </td>
                    <td style="border: 1px solid #000;">{{ number_format($m->maternity_days, 1) }} ({{ $m->maternity_times }}
                        ครั้ง)</td>
                    <td style="border: 1px solid #000;">{{ number_format($m->other_days, 1) }} ({{ $m->other_times }} ครั้ง)
                    </td>
                </tr>
            @endforeach
            <tr style="background-color: #f3f4f6; font-weight: bold;">
                <td style="border: 1px solid #000; text-align: left;">รวม ({{ $div }})</td>
                <td style="border: 1px solid #000;">-</td>
                <td style="border: 1px solid #000;">{{ number_format($data['total_working_days']) }}</td>
                <td style="border: 1px solid #000; color: red;">{{ number_format($data['total_leave_days'], 1) }}</td>
                <td style="border: 1px solid #000;">{{ number_format($data['sick_days'], 1) }}</td>
                <td style="border: 1px solid #000;">{{ number_format($data['personal_days'], 1) }}</td>
                <td style="border: 1px solid #000;">{{ number_format($data['annual_days'], 1) }}</td>
                <td style="border: 1px solid #000;">{{ number_format($data['maternity_days'], 1) }}</td>
                <td style="border: 1px solid #000;">{{ number_format($data['other_days'], 1) }}</td>
            </tr>
        </table>
        <br>
    @endforeach

    <table>
        <tr>
            <td colspan="7" style="text-align: right; color: #555;">
                @php
                    $printedDate = \Carbon\Carbon::now()->timezone('Asia/Bangkok');
                    $printYear = $printedDate->format('Y') + 543;
                    $printPatt = $printedDate->format('d/m/') . $printYear . ' เวลา ' . $printedDate->format('H:i');
                @endphp
                พิมพ์ข้อมูลเมื่อวันที่: {{ $printPatt }} น.
            </td>
        </tr>
    </table>
</body>

</html>