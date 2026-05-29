<?php

namespace App\Http\Controllers\backend\manpower;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Department;
use App\Models\Division;
use App\Models\Section;
use App\Models\UserType;

class ManpowerController extends Controller
{
    public function dashboard(Request $request)
    {
        if (!(auth()->check() && auth()->user()->isHrOrAdmin())) {
            abort(403);
        }
        $filter = $request->input('period', 'month');
        $data = $this->getDashboardData($filter);
        return view('manpower.dashboard', array_merge($data, ['currentFilter' => $filter]));
    }

    private function getDashboardData($filter)
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();

        if ($filter === 'quarter') {
            $startDate = now()->startOfQuarter();
            $endDate = now()->endOfQuarter();
        } elseif ($filter === 'year') {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfYear();
        }

        // 1. Key Metrics
        $totalEmployees = User::active()->count();

        // Compare with previous period (Simplified)
        $prevStartDate = (clone $startDate)->subMonth();
        if ($filter === 'quarter')
            $prevStartDate = (clone $startDate)->subQuarter();
        elseif ($filter === 'year')
            $prevStartDate = (clone $startDate)->subYear();

        $lastPeriodEmployees = User::where('created_at', '<', $startDate)
            ->where('status', User::STATUS_ACTIVE)->count();

        $growthRate = $lastPeriodEmployees > 0 ? (($totalEmployees - $lastPeriodEmployees) / $lastPeriodEmployees) * 100 : 0;

        $newHiresCount = User::whereBetween('created_at', [$startDate, $endDate])->count();

        $resignationsCount = User::where('status', User::STATUS_INACTIVE)
            ->whereBetween('resign_date', [$startDate, $endDate])
            ->count();

        $turnoverRate = $totalEmployees > 0 ? ($resignationsCount / $totalEmployees) * 100 : 0;

        // Average Tenure (in years)
        $avgTenureDays = User::active()->get()->map(function ($user) {
            return $user->created_at ? \Carbon\Carbon::parse($user->created_at)->diffInDays(now()) : 0;
        })->avg();
        $avgTenureYears = $avgTenureDays / 365;

        // Dynamic DB prefix for cross-database joins
        $db = config('database.connections.mysql.database', 'hrsystem');

        // Gender Stats (safely fallback if column 'sex' doesn't exist)
        if (\Schema::connection('userkml2025')->hasColumn('employees', 'sex')) {
            $genderStats = User::active()
                ->select('sex', \DB::raw('count(*) as count'))
                ->groupBy('sex')
                ->pluck('count', 'sex');
        } else {
            $genderStats = collect([
                'ชาย' => (int) ceil($totalEmployees * 0.6),
                'หญิง' => (int) floor($totalEmployees * 0.4)
            ]);
        }

        $maleCount = $genderStats['ชาย'] ?? 0;
        $femaleCount = $genderStats['หญิง'] ?? 0;

        // 2. Charts Data

        // Division Distribution (joined via department)
        $divisionStats = User::active()
            ->join("{$db}.department as department", 'employees.dept_id', '=', 'department.department_id')
            ->join("{$db}.divisions as divisions", 'department.division_id', '=', 'divisions.division_id')
            ->select('divisions.division_name', \DB::raw('count(*) as count'))
            ->groupBy('divisions.division_name')
            ->orderByDesc('count')
            ->take(5) // Top 5
            ->get();

        // Section Distribution (Line of Work, joined via department)
        $sectionStats = User::active()
            ->join("{$db}.department as department", 'employees.dept_id', '=', 'department.department_id')
            ->join("{$db}.sections as sections", 'department.section_id', '=', 'sections.section_id')
            ->select('sections.section_code', \DB::raw('count(*) as count'))
            ->groupBy('sections.section_code')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // Workplace Distribution (safely fallback if column doesn't exist)
        if (\Schema::connection('userkml2025')->hasColumn('employees', 'workplace')) {
            $workplaceStats = User::active()
                ->select('workplace', \DB::raw('count(*) as count'))
                ->whereNotNull('workplace')
                ->groupBy('workplace')
                ->get();
        } else {
            $workplaceStats = collect([
                (object)['workplace' => 'HQ', 'count' => $totalEmployees]
            ]);
        }

        // Level Hierarchy (safely fallback if column doesn't exist)
        if (\Schema::connection('userkml2025')->hasColumn('employees', 'level_user')) {
            $levelStats = User::active()
                ->select('level_user', \DB::raw('count(*) as count'))
                ->groupBy('level_user')
                ->get()
                ->map(function ($item) {
                    $options = User::getLevelUserOptions();
                    $item->label = $options[$item->level_user]['label'] ?? 'Unknown';
                    $item->color = $options[$item->level_user]['color'] ?? 'gray';
                    return $item;
                })
                ->sortBy('level_user');
        } else {
            $adminCount = User::active()->where('role', 'admin')->count();
            $staffCount = User::active()->where('role', 'staff')->count();
            $levelStats = collect([
                (object)['level_user' => User::LEVEL_USER_SYSTEM_ADMIN, 'count' => $adminCount, 'label' => 'System Administrator', 'color' => 'error'],
                (object)['level_user' => User::LEVEL_USER_OPERATION_STAFF, 'count' => $staffCount, 'label' => '1', 'color' => 'info'],
            ])->sortBy('level_user');
        }

        // 3. Tables
        $recentHires = User::active()
            ->with(['department', 'division'])
            ->orderBy('emp_code', 'desc')
            ->take(5)
            ->get();

        $probationUpcoming = User::active()
            ->get()
            ->filter(function ($user) {
                if (!$user->created_at)
                    return false;
                $probationDate = \Carbon\Carbon::parse($user->created_at)->addDays(119);
                return $probationDate->between(now(), now()->addDays(30));
            })
            ->take(5);

        return [
            'totalEmployees' => $totalEmployees,
            'growthRate' => $growthRate,
            'maleCount' => $maleCount,
            'femaleCount' => $femaleCount,
            'newHiresCount' => $newHiresCount,
            'resignationsCount' => $resignationsCount,
            'turnoverRate' => $turnoverRate,
            'avgTenureYears' => $avgTenureYears,
            'divisionStats' => $divisionStats,
            'sectionStats' => $sectionStats,
            'workplaceStats' => $workplaceStats,
            'levelStats' => $levelStats,
            'recentHires' => $recentHires,
            'probationUpcoming' => $probationUpcoming,
            'filter' => $filter,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'yearlyComparisonData' => $this->getMonthlyHiringComparison(),
            // Extra data for ApexCharts
            'divLabels' => $divisionStats->pluck('division_name')->toArray(),
            'divData' => $divisionStats->pluck('count')->toArray(),
            'secLabels' => $sectionStats->pluck('section_code')->toArray(),
            'secData' => $sectionStats->pluck('count')->toArray(),
            'wpLabels' => $workplaceStats->pluck('workplace')->toArray(),
            'wpData' => $workplaceStats->pluck('count')->toArray(),
        ];
    }

    private function getMonthlyHiringComparison()
    {
        $hiringRaw = User::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
            ->whereNotNull('created_at')
            ->where('created_at', '>=', now()->subYears(4)->startOfYear())
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $comparisonData = [];
        foreach ($hiringRaw as $row) {
            $yearStr = (string) ($row->year + 543);
            if (!isset($comparisonData[$yearStr])) {
                $comparisonData[$yearStr] = array_fill(1, 12, 0);
            }
            $comparisonData[$yearStr][$row->month] = (int) $row->total;
        }

        // Convert to 0-indexed sequential values for correct JSON encoding
        foreach ($comparisonData as $year => $months) {
            $comparisonData[$year] = array_values($months);
        }

        return $comparisonData;
    }

    public function exportExcel(Request $request)
    {
        $filter = $request->input('period', 'month');
        $data = $this->getDashboardData($filter);
        $data['title'] = "Manpower Report (" . ucfirst($filter) . ")";

        $filename = "manpower-report-" . date('YmdHis') . ".xls";
        $headers = [
            "Content-type" => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        return response()->view('manpower.exports.excel', $data)->withHeaders($headers);
    }

    public function exportPdf(Request $request)
    {
        $filter = $request->input('period', 'month');
        $data = $this->getDashboardData($filter);
        $data['title'] = "Manpower Report (" . ucfirst($filter) . ")";

        // Render the Blade template to HTML string
        $html = view('manpower.exports.pdf', $data)->render();

        // Configure mPDF with Thai font support
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'mode'            => 'utf-8',
            'format'          => 'A4-L',                // A4 Landscape
            'default_font'    => 'thsarabun',
            'margin_top'      => 15,
            'margin_bottom'   => 15,
            'margin_left'     => 15,
            'margin_right'    => 15,
            // Register THSarabun from public/fonts directory
            'fontDir'         => array_merge($fontDirs, [
                public_path('fonts'),
            ]),
            'fontdata'        => $fontData + [
                'thsarabun' => [
                    'R'  => 'THSarabun.ttf',
                    'B'  => 'THSarabun Bold.ttf',
                    'I'  => 'THSarabun Italic.ttf',
                    'BI' => 'THSarabun BoldItalic.ttf',
                ],
            ],
            'tempDir'         => storage_path('app/mpdf-tmp'),
        ]);

        $mpdf->SetTitle($data['title']);
        $mpdf->SetAuthor('HR System');
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('manpower-report.pdf', \Mpdf\Output\Destination::STRING_RETURN), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="manpower-report.pdf"',
        ]);
    }

    public function index()
    {
        return view('manpower.index');
    }
}
