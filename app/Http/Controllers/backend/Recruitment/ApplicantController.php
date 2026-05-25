<?php

namespace App\Http\Controllers\Backend\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Recruitment\Application;
use App\Models\Recruitment\StatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicantController extends Controller
{
    public function index(Request $request)
    {
        // Get only the latest application ID for each applicant + job post combination
        $latestIds = Application::selectRaw('MAX(id) as id')
            ->groupBy('applicant_id', 'job_post_id')
            ->pluck('id');

        $query = Application::select('recruitment_applications.*')
            ->with(['applicant', 'jobPost.jobPosition', 'jobPost.department'])
            ->addSelect([
                'total_applications' => Application::from('recruitment_applications as app_count')
                    ->selectRaw('count(*)')
                    ->whereColumn('app_count.applicant_id', 'recruitment_applications.applicant_id')
            ])
            ->whereIn('id', $latestIds);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('applicant', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('job_post_id')) {
            $query->where('job_post_id', $request->job_post_id);
        }

        $applications = $query->orderBy('created_at', 'desc')->paginate(15);

        if ($request->ajax()) {
            return view('backend.recruitment.applications.table', compact('applications'))->render();
        }

        return view('backend.recruitment.applications.index', compact('applications'));
    }

    public function show($id)
    {
        $application = Application::with([
            'applicant.education',
            'applicant.experience',
            'education',
            'experience',
            'jobPost.department',
            'jobPost.jobPosition',
            'documents',
            'statusLogs.user',
            'interviews.interviewers',
            'interviews.scores',
        ])->findOrFail($id);

        // อัปเดตสถานะอัตโนมัติ ถ้ามีการนัดสัมภาษณ์แล้ว แต่สถานะยังเป็น new/screening
        if ($application->interviews->count() > 0 && in_array($application->status, ['new', 'screening'])) {
            $oldStatus = $application->status;
            $application->update([
                'status' => 'interview',
                'screened_by' => Auth::id(),
                'screened_at' => now(),
            ]);

            StatusLog::create([
                'application_id' => $application->id,
                'old_status' => $oldStatus,
                'new_status' => 'interview',
                'changed_by' => Auth::id(),
                'remark' => 'เปลี่ยนสถานะอัตโนมัติ — ตรวจพบว่ามีการนัดสัมภาษณ์แล้ว',
            ]);

            // Refresh เพื่อให้ view แสดงสถานะใหม่
            $application->refresh();
            $application->load('statusLogs.user');
        }

        $interviewers = \App\Models\User::orderBy('first_name')->get();

        // Fetch application history (excluding current)
        $history = Application::where('applicant_id', $application->applicant_id)
            ->where('id', '!=', $application->id)
            ->with(['jobPost.jobPosition'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('backend.recruitment.applications.show', compact('application', 'interviewers', 'history'));
    }

    public function updateStatus(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        $oldStatus = $application->status;

        $validated = $request->validate([
            'status' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $application->update([
            'status' => $validated['status'],
            'screened_by' => Auth::id(),
            'screened_at' => now(),
        ]);

        StatusLog::create([
            'application_id' => $application->id,
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'changed_by' => Auth::id(),
            'remark' => $validated['note'],
        ]);

        if ($validated['status'] === 'hired' && $application->applicant && $application->applicant->email) {
            try {
                \Illuminate\Support\Facades\Mail::to($application->applicant->email)
                    ->send(new \App\Mail\ApplicationHired($application));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send hired email: ' . $e->getMessage());
                return back()->with('success', 'อัปเดตสถานะผู้สมัครเรียบร้อยแล้ว แต่อีเมลแจ้งเตือนอาจขัดข้อง');
            }
        } elseif ($validated['status'] === 'rejected' && $application->applicant && $application->applicant->email) {
            try {
                \Illuminate\Support\Facades\Mail::to($application->applicant->email)
                    ->send(new \App\Mail\ApplicationRejected($application));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send rejected email: ' . $e->getMessage());
                return back()->with('success', 'อัปเดตสถานะผู้สมัครเรียบร้อยแล้ว แต่อีเมลแจ้งเตือนอาจขัดข้อง');
            }
        }

        return back()->with('success', 'อัปเดตสถานะผู้สมัครเรียบร้อยแล้ว');
    }
}
