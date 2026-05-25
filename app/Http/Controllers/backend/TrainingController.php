<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Training;

class TrainingController extends Controller
{
    public function index()
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            abort(403);
        }
        $trainings = Training::orderBy('created_at', 'desc')->paginate(10);
        return view('backend.training.index', compact('trainings'));
    }

    public function create()
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            abort(403);
        }
        return view('backend.training.create');
    }

    public function store(Request $request)
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            abort(403);
        }
        $validated = $request->validate([
            'branch' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'hours' => 'required|numeric|min:1',
            'format' => 'required|string|max:255',
            'start_date' => 'required|string|max:255',
            'end_date' => 'required|string|max:255',
            'status' => 'required|in:available,full',
            'details' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB PDF
            'document_link' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB Image
        ]);

        if ($request->hasFile('document')) {
            $validated['document'] = $request->file('document')->store('training_documents', 'public');
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $targetPath = public_path('images/training');

            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0755, true);
            }

            $image->move($targetPath, $imageName);
            $validated['image'] = $imageName;
        }

        Training::create($validated);

        return redirect()->route('backend.training.index')->with('success', 'เพิ่มข้อมูลการฝึกอบรมสำเร็จ');
    }

    public function show($id)
    {
        $training = Training::findOrFail($id);
        return view('backend.training.show', compact('training'));
    }

    public function edit($id)
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            abort(403);
        }
        $training = Training::findOrFail($id);
        return view('backend.training.edit', compact('training'));
    }

    public function update(Request $request, $id)
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            abort(403);
        }
        $validated = $request->validate([
            'branch' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'hours' => 'required|numeric|min:1',
            'format' => 'required|string|max:255',
            'start_date' => 'required|string|max:255',
            'end_date' => 'required|string|max:255',
            'status' => 'required|in:available,full',
            'details' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB PDF
            'document_link' => 'nullable|url|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB Image
        ]);

        $training = Training::findOrFail($id);

        if ($request->hasFile('document')) {
            // Delete old document if it exists
            if ($training->document) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($training->document);
            }
            $validated['document'] = $request->file('document')->store('training_documents', 'public');
        }

        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($training->image) {
                $oldPath = public_path('images/training/' . $training->image);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $targetPath = public_path('images/training');

            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0755, true);
            }

            $image->move($targetPath, $imageName);
            $validated['image'] = $imageName;
        }

        $training->update($validated);

        return redirect()->route('backend.training.index')->with('success', 'แก้ไขข้อมูลการฝึกอบรมสำเร็จ');
    }

    public function destroy($id)
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            abort(403);
        }
        $training = Training::findOrFail($id);
        $training->delete();

        return redirect()->route('backend.training.index')->with('success', 'ลบข้อมูลการฝึกอบรมสำเร็จ');
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

        $trainings = $query->get();

        // Calculate summary metrics based on filtered data
        $totalCourses = $trainings->count();
        $totalApplies = $trainings->sum('applies_count');
        $avgApplies = $totalCourses > 0 ? round($totalApplies / $totalCourses, 1) : 0;

        $popularCourse = $trainings->sortByDesc('applies_count')->first();
        $popularCourseName = ($popularCourse && $popularCourse->applies_count > 0) ? $popularCourse->branch : '-';
        $popularCourseCount = $popularCourse ? $popularCourse->applies_count : 0;

        // Prepare data for the chart
        $labels = [];
        $data = [];

        foreach ($trainings as $training) {
            if ($training->applies_count > 0) {
                $labels[] = trim($training->branch);
                $data[] = (int) $training->applies_count;
            }
        }

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
        $byFormat = $trainings->groupBy('format');
        foreach ($byFormat as $format => $items) {
            $formatLabels[] = $format;
            $formatData[] = $items->sum('applies_count');
        }

        // 3. Yearly Trend Logic
        $yearlyTrendData = \App\Models\TrainingApply::selectRaw('YEAR(created_at) as year, COUNT(*) as count')
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
            $previousYearCount = \App\Models\TrainingApply::whereYear('created_at', $previousYear)->count();

            if ($previousYearCount > 0) {
                $growth = round((($currentYearCount - $previousYearCount) / $previousYearCount) * 100, 1);
            }
        }

        // Get available years for the filter dropdown
        $years = \App\Models\TrainingApply::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Fetch Recent Registrations
        $recentApplies = \App\Models\TrainingApply::with(['training', 'user'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('backend.training.dashboard', [
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
        ]);
    }

    public function applicants(Request $request)
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            abort(403);
        }
        $applyQuery = \App\Models\TrainingApply::query();

        // 1. Filter by Training ID
        if ($request->filled('training_id')) {
            $applyQuery->where('training_id', $request->training_id);
        }

        // 2. Filter by Search (User name/code or Course name)
        if ($request->filled('search')) {
            $search = $request->search;

            // Get employee codes matching user search from User model (separate query)
            $userCodes = \App\Models\User::where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('employee_code', 'like', "%{$search}%")
                ->pluck('employee_code');

            $applyQuery->where(function ($q) use ($search, $userCodes) {
                $q->whereIn('employee_code', $userCodes)
                    ->orWhereHas('training', function ($sq) use ($search) {
                        $sq->where('branch', 'like', "%{$search}%");
                    });
            });
        }

        // 3. Get unique employee codes that match the filters
        $uniqueEmployeeCodes = $applyQuery->distinct()->pluck('employee_code');

        // 4. Query Users based on these codes
        $applicants = \App\Models\User::whereIn('employee_code', $uniqueEmployeeCodes)
            ->orderBy('employee_code', 'asc')
            ->paginate(20);

        // 5. MANUALLY Eager Load trainingApplies across connections
        $employeeIdsInPage = $applicants->pluck('employee_code');
        $allApplies = \App\Models\TrainingApply::whereIn('employee_code', $employeeIdsInPage)
            ->with('training')
            ->get()
            ->groupBy('employee_code');

        foreach ($applicants as $user) {
            $user->setRelation('trainingApplies', $allApplies->get($user->employee_code, collect()));
        }

        $trainings = Training::orderBy('branch')->get();

        return view('backend.training.applicants', compact('applicants', 'trainings'));
    }

    public function courseApplicants($id)
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            abort(403);
        }
        $training = Training::findOrFail($id);

        // Get unique employee codes for this training
        $uniqueEmployeeCodes = \App\Models\TrainingApply::where('training_id', $id)
            ->distinct()
            ->pluck('employee_code');

        $applicants = \App\Models\User::whereIn('employee_code', $uniqueEmployeeCodes)
            ->orderBy('employee_code', 'asc')
            ->paginate(20);

        // MANUALLY Eager Load trainingApplies
        $employeeIdsInPage = $applicants->pluck('employee_code');
        $allApplies = \App\Models\TrainingApply::whereIn('employee_code', $employeeIdsInPage)
            ->with('training')
            ->get()
            ->groupBy('employee_code');

        foreach ($applicants as $user) {
            $user->setRelation('trainingApplies', $allApplies->get($user->employee_code, collect()));
        }

        $trainings = Training::orderBy('branch')->get();

        return view('backend.training.applicants', [
            'applicants' => $applicants,
            'trainings' => $trainings,
            'selected_training_id' => $id,
            'course_name' => $training->branch
        ]);
    }
}
