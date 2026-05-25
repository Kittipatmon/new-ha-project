<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <title>รายงานคำร้องขอ (รอรับทราบ/อนุมัติ)</title>
    <style>
    @page {
        margin: 24px;
    }

    /* Embed TH Sarabun fonts from public/fonts */
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
        font-family: 'THSarabun', 'DejaVu Sans', sans-serif;
        font-size: 12px;
        color: #111;
    }

    h1 {
        font-size: 16px;
        margin: 0 0 8px;
    }

    .meta {
        font-size: 11px;
        color: #444;
        margin-bottom: 12px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #999;
        padding: 6px 8px;
    }

    th {
        background: #f0f0f0;
        text-align: left;
        font-weight: bold;
    }

    .text-center {
        text-align: center;
    }

    .small {
        font-size: 11px;
    }

    /* Dompdf-friendly four-column header row (Flex isn't supported) */
    .info-row {
        margin: 8px 0 12px;
        width: 100%;
    }

    .info-cell {
        display: inline-block;
        width: 22.5%;
        vertical-align: top;
        padding: 4px 6px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        font-size: 11px;
    }

    .info-label {
        color: #666;
        margin-right: 4px;
        font-weight: bold;
    }

    .colorred {
        color: red;
    }

    /* Improved Table Styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th {
        background-color: #f8fafc;
        color: #334155;
        text-align: left;
        font-weight: bold;
        border-top: 1px solid #cbd5e1;
        border-bottom: 2px solid #cbd5e1;
        border-left: none;
        border-right: none;
        padding: 8px 10px;
        font-size: 13px;
    }

    td {
        border-bottom: 1px solid #e2e8f0;
        border-top: none;
        border-left: none;
        border-right: none;
        padding: 8px 10px;
        color: #334155;
        vertical-align: middle;
    }

    tr:nth-child(even) {
        background-color: #fcfcfc;
    }

    .status-badge {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: bold;
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
    </style>
</head>

<body>
    <h1>รายงานคำร้องขอ (รอรับทราบ/อนุมัติ)</h1>
    <div class="info-row">
        <div class="info-cell"><span class="info-label">รายการคำขอทั้งหมด:</span> {{ $totalCount ?? $hrrequests->count() }}</div>
        <div class="info-cell"><span class="info-label">รอตรวจสอบโดยผู้จัดการ:</span> {{ $statusPending }}</div>
        <div class="info-cell"><span class="info-label">รอตรวจสอบโดยฝ่ายบุคคล:</span> {{ $statusAPPROVEDHR }}</div>
        <div class="info-cell"><span class="info-label">ยกเลิก/ปฏิเสธ:</span> {{ $statusCancelled }}</div>
    </div>

    <!-- <div class="meta">พิมพ์เมื่อ: {{ $generated_at->format('d/m/Y H:i') }}</div> -->

    <table>
        <thead>
            <tr>
                <th style="width: 12%">เลขที่รายการ</th>
                <th style="width: 12%">รหัสพนักงาน</th>
                <th style="width: 18%">ชื่อ-สกุล</th>
                <!-- <th style="width: 15%">สายงาน</th> -->
                <th style="width: 13%">ฝ่าย</th>
                <th style="width: 13%">แผนก</th>
                <th style="width: 13%">หมวดหมู่คำร้อง</th>
                <!-- <th style="width: 16%">ประเภทคำร้อง</th> -->
                <!-- <th style="width: 16%">ประเภทย่อย</th> -->
                <th style="width: 9%" class="text-center">วันที่ส่ง</th>
                <th style="width: 10%">สถานะ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($hrrequests as $req)
            <tr>
                <td class="small">{{ $req->request_code }}</td>
                <td class="small">{{ optional($req->user)->employee_code }}</td>
                <td class="small">{{ optional($req->user)->fullname }}</td>
                <!-- <td class="small">{{ optional($req->user->department->section)->section_code ?? '-' }}</td> -->
                <td class="small">{{ optional($req->user->department->division)->division_name ?? '-' }}</td>
                <td class="small">{{ optional($req->user->department)->department_name ?? '-' }}</td>
                <td class="small">{{ optional($req->category)->name_th ?? '-' }}</td>
                <!-- <td class="small">{{ optional($req->type)->name_th ?? '-' }}</td> -->
                <!-- <td class="small">{{ optional($req->subtype)->name_th ?? '-' }}</td> -->
                <td class="text-center small">{{ optional($req->created_at)->format('d/m/Y') }}</td>
                <td class="small">
                    <span class="status-badge">{{ $req->status_label ?? $req->status }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 20px; color: #64748b;">ไม่พบข้อมูล</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- <div class="meta" style="margin-top: 10px;">
        หมายเหตุ: เพื่อการแสดงผลภาษาไทยที่สมบูรณ์ ควรติดตั้งฟอนต์ Sarabun ให้กับ Dompdf
    </div> -->
</body>

</html>