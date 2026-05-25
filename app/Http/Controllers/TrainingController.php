<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\TrainingApply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TrainingController extends Controller
{
    public function index(Request $request)
    {
        $query = Training::query();

        // Apply filters if any
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('branch', 'like', "%{$search}%");
        }

        if ($request->filled('format')) {
            $format = $request->input('format');
            $query->where('format', $format);
        }

        if ($request->filled('department')) {
            $department = $request->input('department');
            $query->where('department', $department);
        }

        // Pagination setup
        $perPage = 6;
        $courses = $query->paginate($perPage)->appends($request->query());

        $appliedTrainings = [];
        if (auth()->check()) {
            $appliedTrainings = TrainingApply::where('employee_code', auth()->user()->employee_code)
                ->pluck('training_id')
                ->toArray();
        }

        return view('training.index', compact('courses', 'appliedTrainings'));
    }

    public function apply($id)
    {
        $training = Training::findOrFail($id);
        $users = User::orderBy('employee_code')->get();
        return view('training.apply', compact('training', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'training_id' => 'required|exists:trainings,id',
            'employee_code' => 'required|string|max:255',
        ]);

        $training = Training::findOrFail($request->training_id);

        // Map data to the model fields (assuming Training is used here just to show saving something, though normally it's a separate Enrollment model)
        // Note: Re-using Training model to create a record might be incorrect if the table is for defining courses. 
        // We will just keep the existing behavior but update branch/department dynamically.
        // The above lines are no longer needed as we are using a separate table.

        TrainingApply::create($validated);

        return redirect()->route('training.index')->with('success', 'สมัครฝึกอบรมสำเร็จ');
    }

    public function dashboard(Request $request)
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            abort(403);
        }
        $query = Training::query();

        // 1. Search Filter (Course Name)
        if ($request->filled('search')) {
            $query->where('branch', 'like', '%' . $request->search . '%');
        }

        // 2. Date Filters (Year, Month, Day)
        $query->withCount([
            'applies' => function ($q) use ($request) {
                if ($request->filled('year')) {
                    $q->whereYear('training_applies.created_at', $request->year);
                }
                if ($request->filled('month')) {
                    $q->whereMonth('training_applies.created_at', $request->month);
                }
                if ($request->filled('day')) {
                    $q->whereDay('training_applies.created_at', $request->day);
                }
            }
        ]);

        $trainings = $query->get()->sortByDesc('applies_count')->values();

        // Calculate summary metrics based on filtered data
        $totalCourses = $trainings->count();
        $totalApplies = $trainings->sum('applies_count');
        $avgApplies = $totalCourses > 0 ? round($totalApplies / $totalCourses, 1) : 0;

        $popularCourse = $trainings->sortByDesc('applies_count')->first();
        $popularCourseName = ($popularCourse && $popularCourse->applies_count > 0) ? $popularCourse->branch : '-';
        $popularCourseCount = $popularCourse ? $popularCourse->applies_count : 0;

        // Breakdowns
        $deptLabels = [];
        $deptData = [];
        $formatLabels = [];
        $formatData = [];

        // Group by Department
        $byDept = $trainings->groupBy('department');
        foreach ($byDept as $dept => $items) {
            $deptLabels[] = $dept;
            $deptData[] = $items->sum('applies_count');
        }

        // Group by Format
        foreach ($trainings->groupBy('format') as $format => $items) {
            $formatLabels[] = $format;
            $formatData[] = $items->sum('applies_count');
        }

        // 3. Yearly Trend Logic
        $yearlyTrendData = TrainingApply::selectRaw('YEAR(created_at) as year, COUNT(*) as count')
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();

        $yearlyLabels = $yearlyTrendData->pluck('year')->map(fn($y) => "ปี " . ($y + 543))->toArray();
        $yearlyCounts = $yearlyTrendData->pluck('count')->toArray();

        // 4. Growth Calculation (if year filter is active)
        $growth = null;
        if ($request->filled('year')) {
            $currentYearCount = $totalApplies;
            $previousYear = $request->year - 1;
            $previousYearCount = TrainingApply::whereYear('created_at', $previousYear)->count();

            if ($previousYearCount > 0) {
                $growth = round((($currentYearCount - $previousYearCount) / $previousYearCount) * 100, 1);
            }
        }

        // Prepare data for the main chart (Top 10 Courses by Interest)
        $labels = [];
        $data = [];
        $topCourses = $trainings->sortByDesc('applies_count')->take(10);

        foreach ($topCourses as $training) {
            if ($training->applies_count > 0) {
                $labels[] = trim($training->branch);
                $data[] = (int) $training->applies_count;
            }
        }

        \Illuminate\Support\Facades\Log::info('Dashboard Data:', [
            'labels' => $labels,
            'data' => $data,
            'totalApplies' => $totalApplies,
            'requestYear' => $request->year
        ]);

        // Get available years for the filter dropdown
        $years = TrainingApply::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Sorting the main collection for potential leaderboard use
        $trainings = $trainings->sortByDesc('applies_count')->values();

        // Fetch Recent Registrations (Connect with training_applies data)
        $recentApplies = TrainingApply::with(['training', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // 5. Monthly Comparison Logic (Multi-Year)
        $monthlyRaw = TrainingApply::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $comparisonData = [];
        foreach ($monthlyRaw as $row) {
            $yearStr = (string)($row->year + 543);
            if (!isset($comparisonData[$yearStr])) {
                $comparisonData[$yearStr] = array_fill(1, 12, 0); // Fill months 1-12 with 0
            }
            $comparisonData[$yearStr][$row->month] = (int)$row->total;
        }

        // Reformat for ApexCharts series
        $monthlySeries = [];
        foreach ($comparisonData as $year => $months) {
            $monthlySeries[] = [
                'name' => 'ปี ' . $year,
                'data' => array_values($months) // Array of 12 counts
            ];
        }

        return view('training.dashboard', [
            'labels' => $labels,
            'data' => $data,
            'years' => $years,
            'totalCourses' => $totalCourses,
            'totalApplies' => $totalApplies,
            'avgApplies' => $avgApplies,
            'popularCourseName' => $popularCourseName,
            'popularCourseCount' => $popularCourseCount,
            'deptLabels' => $deptLabels,
            'deptData' => $deptData,
            'formatLabels' => $formatLabels,
            'formatData' => $formatData,
            'trainings' => $trainings,
            'recentApplies' => $recentApplies,
            'yearlyLabels' => $yearlyLabels,
            'yearlyCounts' => $yearlyCounts,
            'growth' => $growth,
            'monthlySeries' => $monthlySeries,
        ]);
    }
}
