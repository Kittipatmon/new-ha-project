<?php

namespace App\Http\Controllers\backend;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\backend\LeaveReports;
use Barryvdh\DomPDF\Facade\Pdf;

class LeaveReportsController extends Controller
{
    public function dashboard()
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            abort(403);
        }
        $leaveReports = LeaveReports::all();
        return view('leavereports.dashboard', compact('leaveReports'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();

            // Helper to get cell value by coordinate
            $val = function (string $cell) use ($sheet) {
                return trim((string) $sheet->getCell($cell)->getCalculatedValue());
            };

            // Extract division from title e.g. "รายงานการลาประจำเดือน ... (CEO)"
            $title = $val('A1');
            $division_code = null;
            if (preg_match('/\(([^)]+)\)/u', $title, $m)) {
                $division_code = strtoupper(trim($m[1]));
            }

            // Extract month-year e.g. "เดือน สิงหาคม 2568"
            $monthText = $val('E1');
            $report_month = null;
            if ($monthText) {
                $thaiMonths = [
                    'มกราคม' => '01',
                    'กุมภาพันธ์' => '02',
                    'มีนาคม' => '03',
                    'เมษายน' => '04',
                    'พฤษภาคม' => '05',
                    'มิถุนายน' => '06',
                    'กรกฎาคม' => '07',
                    'สิงหาคม' => '08',
                    'กันยายน' => '09',
                    'ตุลาคม' => '10',
                    'พฤศจิกายน' => '11',
                    'ธันวาคม' => '12',
                ];
                foreach ($thaiMonths as $th => $mm) {
                    if (mb_strpos($monthText, $th) !== false) {
                        // Find BE year number in text
                        if (preg_match('/(\d{4})/', $monthText, $y)) {
                            $be = (int) $y[1];
                            $ce = $be - 543; // Convert Buddhist Era to Gregorian
                            $report_month = sprintf('%04d-%s', $ce, $mm);
                        }
                        break;
                    }
                }
            }

            // Numeric values by headers (from the provided template)
            // Row 2 appears to be "จำนวนครั้ง", Row 3 appears to be "จำนวนวัน"
            $total_employees = (int) ($val('F2') ?: $val('F3') ?: 0);
            $working_days = (int) ($val('G2') ?: $val('G3') ?: 0);
            $total_working_days = (int) ($val('H2') ?: $val('H3') ?: 0);

            $sick_times = (int) ($val('J2') ?: 0);
            $sick_days = (float) ($val('J3') ?: 0);
            $personal_times = (int) ($val('K2') ?: 0);
            $personal_days = (float) ($val('K3') ?: 0);
            $annual_times = (int) ($val('L2') ?: 0);
            $annual_days = (float) ($val('L3') ?: 0);
            $maternity_times = (int) ($val('M2') ?: 0);
            $maternity_days = (float) ($val('M3') ?: 0);
            $other_times = (int) ($val('N2') ?: 0);
            $other_days = (float) ($val('N3') ?: 0);

            $total_leave_times = (int) ($val('O2') ?: 0);
            $total_leave_days = (float) ($val('O3') ?: 0);

            if (!$division_code || !$report_month) {
                return back()->with('error', 'ไม่พบสายงานหรือเดือนไม่ถูกต้องในไฟล์ Excel');
            }

            // Upsert by division + report_month
            $report = LeaveReports::firstOrNew([
                'division_code' => $division_code,
                'report_month' => $report_month,
            ]);

            $report->fill([
                'total_employees' => $total_employees,
                'working_days' => $working_days,
                'total_working_days' => $total_working_days,
                'sick_times' => $sick_times,
                'sick_days' => $sick_days,
                'personal_times' => $personal_times,
                'personal_days' => $personal_days,
                'annual_times' => $annual_times,
                'annual_days' => $annual_days,
                'maternity_times' => $maternity_times,
                'maternity_days' => $maternity_days,
                'other_times' => $other_times,
                'other_days' => $other_days,
                'total_leave_times' => $total_leave_times,
                'total_leave_days' => $total_leave_days,
            ]);

            $report->save();

            return redirect()->route('leavereports.dashboard')
                ->with('success', 'นำเข้าข้อมูลสำเร็จ: ' . $division_code . ' ' . $report_month);
        } catch (\Throwable $e) {
            return back()->with('error', 'เกิดข้อผิดพลาดระหว่างนำเข้า: ' . $e->getMessage());
        }
    }

    public function exportData(Request $request)
    {
        $reportType = $request->input('report_type', 'yearly');
        $query = LeaveReports::query();

        $title = "รายงานสรุปวันลา";
        if ($reportType == 'yearly') {
            $year = $request->input('year');
            if ($year) {
                // report_month formatting is usually YYYY-MM
                $query->where('report_month', 'like', $year . '-%');
                $title .= " ประจำปี {$year}";
            }
        } else {
            $start_month = $request->input('start_month');
            $end_month = $request->input('end_month');
            if ($start_month && $end_month) {
                $query->whereBetween('report_month', [$start_month, $end_month]);
                $title .= " ระหว่างเดือน {$start_month} ถึง {$end_month}";
            } elseif ($start_month) {
                $query->where('report_month', '>=', $start_month);
                $title .= " ตั้งแต่เดือน {$start_month}";
            } elseif ($end_month) {
                $query->where('report_month', '<=', $end_month);
                $title .= " ถึงเดือน {$end_month}";
            }
        }

        $leaveReports = $query->orderBy('division_code')->orderBy('report_month')->get();

        $summary = [];
        $total_company_employees = 0;
        $total_company_working_days = 0;
        $total_company_leave_days = 0;

        foreach ($leaveReports as $r) {
            $div = $r->division_code;
            if (!isset($summary[$div])) {
                $summary[$div] = [
                    'division_code' => $div,
                    'total_employees' => 0,
                    'total_working_days' => 0,
                    'total_leave_days' => 0,
                    'sick_days' => 0,
                    'personal_days' => 0,
                    'annual_days' => 0,
                    'maternity_days' => 0,
                    'other_days' => 0,
                    'months' => [],
                ];
            }
            $summary[$div]['total_employees'] = max($summary[$div]['total_employees'], $r->total_employees);
            $summary[$div]['total_working_days'] += $r->total_working_days;
            $summary[$div]['total_leave_days'] += $r->total_leave_days;
            $summary[$div]['sick_days'] += $r->sick_days;
            $summary[$div]['personal_days'] += $r->personal_days;
            $summary[$div]['annual_days'] += $r->annual_days;
            $summary[$div]['maternity_days'] += $r->maternity_days;
            $summary[$div]['other_days'] += $r->other_days;

            $summary[$div]['months'][] = $r;
        }

        // Calculate grand totals across the company if needed
        foreach ($summary as $divData) {
            $total_company_employees += $divData['total_employees'];
            $total_company_working_days += $divData['total_working_days'];
            $total_company_leave_days += $divData['total_leave_days'];
        }

        $viewData = [
            'summary' => $summary,
            'reportType' => $reportType,
            'request' => $request,
            'title' => $title,
            'total_company_employees' => $total_company_employees,
            'total_company_working_days' => $total_company_working_days,
            'total_company_leave_days' => $total_company_leave_days
        ];

        // Ensure we know which format the user requested
        $format = $request->input('export_format', 'pdf');

        if ($format === 'excel') {
            $filename = "leave-reports-summary-" . date('YmdHis') . ".xls";
            $headers = [
                "Content-type" => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=\"$filename\"",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];

            return response()->view('leavereports.excel', $viewData)->withHeaders($headers);
        }

        // Default to PDF
        $pdf = Pdf::loadView('leavereports.pdf', $viewData);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('leave-reports-summary.pdf');
    }
}
