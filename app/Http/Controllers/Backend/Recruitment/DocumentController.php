<?php

namespace App\Http\Controllers\Backend\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Recruitment\ApplicantDocument;
use App\Models\Recruitment\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller
{
    /**
     * Securely serve an applicant document (Resume, Portfolio, etc.)
     *
     * @param int $id
     * @return BinaryFileResponse
     */
    public function show($id)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'กรุณาเข้าสู่ระบบก่อนเปิดดูเอกสาร');
        }

        $document = ApplicantDocument::with('application.jobPost')->findOrFail($id);
        $application = $document->application;

        if (!$this->canUserAccessApplication($user, $application)) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงเอกสารนี้ (Access Denied: Restricted to HR, Assigned Department Managers, or Interviewers)');
        }

        $resolvedPath = $this->resolveFilePath($document->file_path);
        if (!$resolvedPath) {
            abort(404, 'ไม่พบไฟล์เอกสารในระบบ');
        }

        $mime = mime_content_type($resolvedPath) ?: 'application/octet-stream';
        $downloadName = $document->file_name ?: basename($resolvedPath);

        return response()->file($resolvedPath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . rawurlencode($downloadName) . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * Securely serve an applicant photo
     *
     * @param int $applicationId
     * @return BinaryFileResponse
     */
    public function photo($applicationId)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403, 'กรุณาเข้าสู่ระบบ');
        }

        $application = Application::with(['applicant', 'documents', 'jobPost'])->findOrFail($applicationId);

        if (!$this->canUserAccessApplication($user, $application)) {
            abort(403, 'ไม่มีสิทธิ์เข้าถึงข้อมูล');
        }

        // Try to find photo from documents first
        $photoDoc = $application->documents->where('document_type', 'photo')->first();
        $rawPath = $photoDoc?->file_path ?? $application->applicant?->photo_file;

        if (!$rawPath) {
            // Return a default SVG avatar or 404
            abort(404, 'ไม่พบรูปถ่าย');
        }

        $resolvedPath = $this->resolveFilePath($rawPath);
        if (!$resolvedPath) {
            abort(404, 'ไม่พบไฟล์รูปถ่าย');
        }

        $mime = mime_content_type($resolvedPath) ?: 'image/jpeg';

        return response()->file($resolvedPath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * Check if user is authorized to access the application documents
     */
    private function canUserAccessApplication($user, ?Application $application): bool
    {
        if (!$application) {
            return false;
        }

        // 1. Central HR / Admin
        if (
            (method_exists($user, 'isCentralHr') && $user->isCentralHr()) ||
            (method_exists($user, 'isHrOrAdmin') && $user->isHrOrAdmin()) ||
            ($user->role === 'admin')
        ) {
            return true;
        }

        // 2. Department Manager for the job position
        if ($application->jobPost && method_exists($user, 'getManagedDepartmentIds')) {
            $managedDeptIds = $user->getManagedDepartmentIds();
            if (in_array($application->jobPost->department_id, $managedDeptIds)) {
                return true;
            }
        }

        // 3. Assigned Interviewer for this application
        $isAssignedInterviewer = $application->interviews()->where(function ($q) use ($user) {
            $q->where('interviewer_id', $user->id)
              ->orWhereExists(function ($sub) use ($user) {
                  $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                      ->from('recruitment_interview_interviewer')
                      ->whereColumn('recruitment_interview_interviewer.interview_id', 'recruitment_interviews.id')
                      ->where('recruitment_interview_interviewer.user_id', $user->id);
              });
        })->exists();

        if ($isAssignedInterviewer) {
            return true;
        }

        return false;
    }

    /**
     * Safely resolve the absolute file path on disk
     */
    private function resolveFilePath(?string $filePath): ?string
    {
        if (empty($filePath)) {
            return null;
        }

        $filename = basename($filePath);
        $candidates = [
            storage_path('app/recruitment_documents/' . $filename),
            public_path('files/recruitment_applicant_documents/' . $filename),
            public_path('storage/' . $filePath),
            public_path($filePath),
        ];

        foreach ($candidates as $candidate) {
            if (file_exists($candidate) && is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
