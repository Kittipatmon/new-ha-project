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
            font-style: normal;
            font-weight: 700;
            src: url("{{ public_path('fonts/THSarabun Bold.ttf') }}") format('truetype');
        }

        body {
            font-family: 'THSarabun', sans-serif;
            font-size: 16px;
            color: #111;
            line-height: 1.4;
        }

        h1 {
            font-size: 24px;
            text-align: center;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
            font-weight: bold;
            text-align: center;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            padding: 5px;
            background: #e5e7eb;
            border-left: 4px solid #3b82f6;
        }

        .grid {
            display: block;
            width: 100%;
        }

        .col {
            display: inline-block;
            width: 48%;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: right;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>

<body>
    <div style="text-align: center; margin-bottom: 10px;">
        @php
            $logoPath = public_path('images/logos/th-kumwell-logo.png');
            if (file_exists($logoPath)) {
                $logoData = base64_encode(file_get_contents($logoPath));
                $logoSrc = 'data:image/png;base64,' . $logoData;
                echo '<img src="' . $logoSrc . '" height="40">';
            }
        @endphp
    </div>

    <h1>{{ $title }}</h1>
    <div class="subtitle">ช่วงเวลา: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</div>

    <div class="section-title">สรุปภาพรวม (Key Metrics)</div>
    <table>
        <tr>
            <th style="width: 70%;">หัวข้อ</th>
            <th>จำนวน / อัตรา</th>
        </tr>
        <tr>
            <td>พนักงานทั้งหมด (Active)</td>
            <td class="text-right">{{ number_format($totalEmployees) }} คน</td>
        </tr>
        <tr>
            <td>พนักงานใหม่ (ในรอบที่เลือก)</td>
            <td class="text-right">{{ number_format($newHiresCount) }} คน</td>
        </tr>
        <tr>
            <td>พนักงานลาออก (ในรอบที่เลือก)</td>
            <td class="text-right">{{ number_format($resignationsCount) }} คน</td>
        </tr>
        <tr>
            <td>อัตราการลาออก (Turnover Rate)</td>
            <td class="text-right">{{ number_format($turnoverRate, 1) }}%</td>
        </tr>
        <tr>
            <td>อายุงานเฉลี่ย (Average Tenure)</td>
            <td class="text-right">{{ number_format($avgTenureYears, 1) }} ปี</td>
        </tr>
    </table>

    <div style="width: 100%;">
        <div style="float: left; width: 48%;">
            <div class="section-title">สัดส่วนเพศ (Gender)</div>
            <table>
                <tr>
                    <th>เพศ</th>
                    <th>จำนวน</th>
                </tr>
                <tr>
                    <td>ชาย (Male)</td>
                    <td class="text-right">{{ number_format($maleCount) }} คน</td>
                </tr>
                <tr>
                    <td>หญิง (Female)</td>
                    <td class="text-right">{{ number_format($femaleCount) }} คน</td>
                </tr>
            </table>
        </div>
        <div style="float: right; width: 48%;">
            <div class="section-title">สถานที่ทำงาน (Workplace)</div>
            <table>
                <tr>
                    <th>สถานที่</th>
                    <th>จำนวน</th>
                </tr>
                @foreach($workplaceStats as $wp)
                    <tr>
                        <td>{{ $wp->workplace }}</td>
                        <td class="text-right">{{ number_format($wp->count) }} คน</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
    <div style="clear: both;"></div>

    <div class="section-title">ระดับพนักงาน (Level Hierarchy)</div>
    <table>
        <tr>
            <th>ระดับ</th>
            <th>จำนวน (คน)</th>
        </tr>
        @foreach($levelStats as $stat)
            <tr>
                <td>{{ $stat->label }}</td>
                <td class="text-right">{{ number_format($stat->count) }}</td>
            </tr>
        @endforeach
    </table>

    <div class="section-title">จำนวนพนักงานแยกตามฝ่าย (Top 5 Division)</div>
    <table>
        <tr>
            <th>ฝ่าย</th>
            <th>จำนวน (คน)</th>
        </tr>
        @foreach($divisionStats as $div)
            <tr>
                <td>{{ $div->division_name }}</td>
                <td class="text-right">{{ number_format($div->count) }}</td>
            </tr>
        @endforeach
    </table>

    <div class="footer">
        พิมพ์ข้อมูลเมื่อวันที่: {{ now()->timezone('Asia/Bangkok')->format('d/m/Y H:i') }} น.
    </div>
</body>

</html>