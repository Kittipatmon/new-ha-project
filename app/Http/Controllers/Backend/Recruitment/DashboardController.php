<?php

namespace App\Http\Controllers\Backend\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Recruitment\RecruitmentRequest;
use App\Models\Recruitment\JobPost;
use App\Models\Recruitment\JobPostView;
use App\Models\Recruitment\Application;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (!(auth()->check() && auth()->user()->isHrOrAdmin())) {
            abort(403);
        }
        $stats = [
            'pending_requests' => RecruitmentRequest::whereIn('status', ['pending', 'pending_manager', 'pending_executive'])->count(),
            'active_posts' => JobPost::where('publish_status', 'published')->count(),
            'total_views' => JobPost::sum('views'),
            'total_clicks' => JobPost::sum('clicks'),
            'total_applications' => Application::count(),
            'new_applications' => Application::whereIn('status', ['new', 'submitted'])->count(),
            'interview_scheduled' => Application::whereIn('status', ['interview', 'interview_scheduled'])->count(),
        ];

        $recent_applications = Application::with(['applicant', 'jobPost'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recent_requests = RecruitmentRequest::with(['department', 'jobPosition', 'jobPosts'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 1. ความสนใจของผู้สมัครแยกตามตำแหน่งงาน (Applicant interest by Job Position)
        $positionInterest = JobPost::withCount('applications')
            ->orderBy('applications_count', 'desc')
            ->take(6)
            ->get();

        $positionLabels = $positionInterest->pluck('position_name')->toArray();
        $positionCounts = $positionInterest->pluck('applications_count')->toArray();
        $positionViews = $positionInterest->pluck('views')->toArray();

        // 2. ความสนใจของผู้สมัครแยกตามสายงาน/แผนก (Interest by Department)
        $departmentInterest = \App\Models\Recruitment\Department::withCount('jobPosts')
            ->with(['jobPosts' => function ($q) {
                $q->withCount('applications');
            }])
            ->get()
            ->map(function ($dept) {
                $appCount = $dept->jobPosts->sum('applications_count');
                return [
                    'name' => $dept->department_name ?: $dept->department_fullname,
                    'count' => $appCount
                ];
            })
            ->filter(fn($item) => $item['count'] > 0)
            ->sortByDesc('count')
            ->values();

        if ($departmentInterest->isEmpty()) {
            $departmentLabels = ['ICT', 'Engineering', 'Accounting', 'HR', 'Marketing'];
            $departmentCounts = [7, 3, 2, 1, 1];
        } else {
            $departmentLabels = $departmentInterest->pluck('name')->toArray();
            $departmentCounts = $departmentInterest->pluck('count')->toArray();
        }

        // 3. แนวโน้มจำนวนผู้สมัครในแต่ละเดือน (Application Monthly Trend)
        $monthlyData = Application::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->take(6)
            ->pluck('count', 'month');

        $positionIds = $positionInterest->pluck('id')->toArray();

        // Get all job posts for the analytics dropdown
        $allJobPosts = JobPost::select('id', 'title', 'position_name')->orderBy('title')->get();

        $chartData = [
            'positions' => [
                'labels' => !empty($positionLabels) ? $positionLabels : ['test developer', 'ช่างซ่อมคอม', 'testระบบ'],
                'counts' => !empty($positionCounts) ? $positionCounts : [7, 1, 0],
                'views' => !empty($positionViews) ? $positionViews : [0, 0, 0],
                'ids' => !empty($positionIds) ? $positionIds : [2, 3, 4]
            ],
            'departments' => [
                'labels' => $departmentLabels,
                'counts' => $departmentCounts
            ],
            'monthly' => [
                'labels' => $monthlyData->keys()->toArray(),
                'counts' => $monthlyData->values()->toArray()
            ]
        ];

        return view('backend.recruitment.dashboard', compact('stats', 'recent_applications', 'recent_requests', 'chartData', 'positionInterest', 'allJobPosts'));
    }

    /**
     * API endpoint: Return daily/hourly analytics data for recruitment job posts
     */
    public function analyticsData(Request $request)
    {
        $jobPostId = $request->query('job_post_id');
        $days = (int) $request->query('days', 7);

        $query = JobPostView::query();
        if ($jobPostId) {
            $query->where('job_post_id', $jobPostId);
        }

        $startDate = now()->subDays($days - 1)->toDateString();
        $logs = (clone $query)->where('view_date', '>=', $startDate)
            ->selectRaw('view_date, event_type, count(*) as count')
            ->groupBy('view_date', 'event_type')
            ->orderBy('view_date', 'asc')
            ->get();

        // Hourly statistics for peak hours
        $hourlyLogs = (clone $query)->where('view_date', '>=', $startDate)
            ->selectRaw('HOUR(created_at) as hour, event_type, count(*) as count')
            ->groupBy('hour', 'event_type')
            ->orderBy('hour', 'asc')
            ->get();

        $jobPostsList = JobPost::select('id', 'title', 'views', 'clicks', 'publish_status')->get();

        return response()->json([
            'logs' => $logs,
            'hourly_logs' => $hourlyLogs,
            'total_views' => JobPost::sum('views'),
            'total_clicks' => JobPost::sum('clicks'),
            'job_posts' => $jobPostsList,
        ]);
    }
}
