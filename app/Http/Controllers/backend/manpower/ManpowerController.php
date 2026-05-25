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
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
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

        $newHiresCount = User::whereBetween('startwork_date', [$startDate, $endDate])->count();

        $resignationsCount = User::where('status', User::STATUS_INACTIVE)
            ->whereBetween('endwork_date', [$startDate, $endDate])
            ->count();

        $turnoverRate = $totalEmployees > 0 ? ($resignationsCount / $totalEmployees) * 100 : 0;

        // Average Tenure (in years)
        $avgTenureDays = User::active()->get()->map(function ($user) {
            return $user->startwork_date ? \Carbon\Carbon::parse($user->startwork_date)->diffInDays(now()) : 0;
        })->avg();
        $avgTenureYears = $avgTenureDays / 365;

        // Gender Stats
        $genderStats = User::active()
            ->select('sex', \DB::raw('count(*) as count'))
            ->groupBy('sex')
            ->pluck('count', 'sex');

        $maleCount = $genderStats['ชาย'] ?? 0;
        $femaleCount = $genderStats['หญิง'] ?? 0;

        // 2. Charts Data

        // Division Distribution
        $divisionStats = User::active()
            ->join('divisions', 'userskml.division_id', '=', 'divisions.division_id')
            ->select('divisions.division_name', \DB::raw('count(*) as count'))
            ->groupBy('divisions.division_name')
            ->orderByDesc('count')
            ->take(5) // Top 5
            ->get();

        // Section Distribution (Line of Work)
        $sectionStats = User::active()
            ->join('sections', 'userskml.section_id', '=', 'sections.section_id')
            ->select('sections.section_code', \DB::raw('count(*) as count'))
            ->groupBy('sections.section_code')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        // Workplace Distribution
        $workplaceStats = User::active()
            ->select('workplace', \DB::raw('count(*) as count'))
            ->whereNotNull('workplace')
            ->groupBy('workplace')
            ->get();

        // Level Hierarchy
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

        // 3. Tables
        $recentHires = User::active()
            ->with(['department', 'division'])
            ->orderBy('employee_code', 'desc')
            ->take(5)
            ->get();

        $probationUpcoming = User::active()
            ->get()
            ->filter(function ($user) {
                if (!$user->startwork_date)
                    return false;
                $probationDate = \Carbon\Carbon::parse($user->startwork_date)->addDays(119);
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
        $hiringRaw = User::selectRaw('YEAR(startwork_date) as year, MONTH(startwork_date) as month, COUNT(*) as total')
            ->whereNotNull('startwork_date')
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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('manpower.exports.pdf', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('manpower-report.pdf');
    }

    public function index()
    {
        return view('manpower.index');
    }
}
