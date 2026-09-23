<?php

namespace App\Http\Controllers\Backend\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Recruitment\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\DirectEmail;

class EmailController extends Controller
{
    /**
     * Send a direct email to the applicant.
     */
    public function sendDirectEmail(Request $request, Application $application)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        if (!$application->applicant || !$application->applicant->email) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบอีเมลของผู้สมัคร'
            ], 422);
        }

        try {
            $attachments = $request->file('attachments') ?? [];
            $mailable = new DirectEmail(
                $validated['subject'],
                $validated['content'],
                $application->applicant->full_name,
                $attachments,
                Auth::user()
            );

            \App\Services\RecruitmentMailService::queueMailable(
                $mailable,
                $application->applicant->email,
                $application->applicant->full_name,
                'direct_email',
                ['application_id' => $application->id],
                Auth::user()
            );

            return response()->json([
                'success' => true,
                'message' => 'ส่งอีเมลเข้าคิวเรียบร้อยแล้ว ระบบกำลังดำเนินการส่งออก'
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Direct email failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการส่งอีเมล: ' . $e->getMessage()
            ], 500);
        }
    }
}
