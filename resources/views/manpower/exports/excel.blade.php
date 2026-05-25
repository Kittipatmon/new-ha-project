<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body>
    <table>
        <tr>
            <td colspan="4" style="text-align: center; font-size: 16px; font-weight: bold;">
                {{ $title }}
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <b>ช่วงเวลา:</b> {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    <br>

    {{-- Key Metrics --}}
    <table>
        <thead>
            <tr>
                <th colspan="2" style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">สรุปภาพรวม
                    (Key Metrics)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #000;">พนักงานทั้งหมด</td>
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($totalEmployees) }} คน</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;">พนักงานใหม่ (ในรอบที่เลือก)</td>
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($newHiresCount) }} คน</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;">พนักงานลาออก (ในรอบที่เลือก)</td>
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($resignationsCount) }} คน</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;">อัตราการลาออก</td>
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($turnoverRate, 1) }}%</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;">อายุงานเฉลี่ย</td>
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($avgTenureYears, 1) }} ปี</td>
            </tr>
        </tbody>
    </table>

    <br>

    {{-- Gender Stats --}}
    <table>
        <thead>
            <tr>
                <th colspan="2" style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">สัดส่วนเพศ
                    (Gender)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #000;">ชาย (Male)</td>
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($maleCount) }} คน</td>
            </tr>
            <tr>
                <td style="border: 1px solid #000;">หญิง (Female)</td>
                <td style="border: 1px solid #000; text-align: right;">{{ number_format($femaleCount) }} คน</td>
            </tr>
        </tbody>
    </table>

    <br>

    {{-- Level Stats --}}
    <table>
        <thead>
            <tr>
                <th colspan="2" style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">
                    ระดับพนักงาน (Level Hierarchy)</th>
            </tr>
            <tr>
                <th style="background-color: #e5e7eb; border: 1px solid #000;">ระดับ</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000;">จำนวน (คน)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($levelStats as $stat)
                <tr>
                    <td style="border: 1px solid #000;">{{ $stat->label }}</td>
                    <td style="border: 1px solid #000; text-align: right;">{{ number_format($stat->count) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>

    {{-- Division Stats --}}
    <table>
        <thead>
            <tr>
                <th colspan="2" style="background-color: #d1d5db; font-weight: bold; border: 1px solid #000;">
                    จำนวนพนักงานแยกตามฝ่าย (Top 5)</th>
            </tr>
            <tr>
                <th style="background-color: #e5e7eb; border: 1px solid #000;">ฝ่าย</th>
                <th style="background-color: #e5e7eb; border: 1px solid #000;">จำนวน (คน)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($divisionStats as $div)
                <tr>
                    <td style="border: 1px solid #000;">{{ $div->division_name }}</td>
                    <td style="border: 1px solid #000; text-align: right;">{{ number_format($div->count) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>

    <table>
        <tr>
            <td colspan="2" style="text-align: right; color: #555;">
                พิมพ์ข้อมูลเมื่อวันที่: {{ now()->timezone('Asia/Bangkok')->format('d/m/Y H:i') }} น.
            </td>
        </tr>
    </table>
</body>

</html>