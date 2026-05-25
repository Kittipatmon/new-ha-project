<?php

namespace App\Http\Controllers\Backend\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Recruitment\RecruitmentRequest;
use App\Models\Recruitment\JobPost;
use App\Models\Recruitment\Application;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            abort(403);
        }
        $stats = [
            'pending_requests' => RecruitmentRequest::where('status', 'pending')->count(),
            'active_posts' => JobPost::where('publish_status', 'published')->count(),
            'total_applications' => Application::count(),
            'new_applications' => Application::where('status', 'new')->count(),
            'interview_scheduled' => Application::where('status', 'interview')->count(),
        ];

        $recent_applications = Application::with(['applicant', 'jobPost'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recent_requests = RecruitmentRequest::with(['department', 'jobPosition'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('backend.recruitment.dashboard', compact('stats', 'recent_applications', 'recent_requests'));
    }
}
