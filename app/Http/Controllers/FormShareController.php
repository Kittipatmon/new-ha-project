<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\FormShare;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormShareController extends Controller
{
    /**
     * Store share record(s)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'form_type' => 'required|string|in:manpower_request,probation_evaluation,interview_evaluation,hr_request',
            'form_id' => 'required|integer',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer',
            'department_ids' => 'nullable|array',
            'department_ids.*' => 'integer',
            'share_channel' => 'nullable|string|max:50',
            'note' => 'nullable|string|max:500',
        ]);

        $sender = auth()->user();
        if (!$sender) {
            return response()->json([
                'success' => false,
                'message' => 'คุณยังไม่ได้เข้าสู่ระบบ',
            ], 401);
        }

        // Check if sender is admin or editor based strictly on hr_user_roles (hrsystem)
        $isAdminOrEditor = ($sender && (
            in_array($sender->hr_role, ['admin', 'editor']) 
            || (method_exists($sender, 'isEditor') && $sender->isEditor())
        ));

        // If not admin/editor, validate that target users/departments do not cross department boundaries
        if (!$isAdminOrEditor) {
            // Cannot share to 'everyone' (public)
            if (!empty($validated['user_ids']) && in_array(0, $validated['user_ids'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'คุณไม่มีสิทธิ์แชร์ให้ทุกคนในระบบ (สิทธิ์นี้เฉพาะ Admin และ Editor เท่านั้น)',
                ], 403);
            }

            // Resolve document department IDs
            $docDeptName = $this->resolveDocumentDepartment($validated['form_type'], $validated['form_id']);
            $allowedDeptIds = [];
            if ($docDeptName) {
                $allowedDeptIds = Department::where('department_name', 'like', "%{$docDeptName}%")
                    ->orWhere('department_fullname', 'like', "%{$docDeptName}%")
                    ->pluck('department_id')
                    ->toArray();
            }

            // Check department_ids: cannot share to other departments
            if (!empty($validated['department_ids'])) {
                foreach ($validated['department_ids'] as $targetDeptId) {
                    if (!in_array((int)$targetDeptId, array_map('intval', $allowedDeptIds))) {
                        return response()->json([
                            'success' => false,
                            'message' => 'คุณไม่มีสิทธิ์แชร์ไปหาแผนกอื่น (สิทธิ์แชร์ข้ามแผนกเฉพาะ Admin และ Editor เท่านั้น)',
                        ], 403);
                    }
                }
            }

            // Check user_ids: ensure individual target users belong to allowed department
            if (!empty($validated['user_ids'])) {
                $targetUsers = User::whereIn('id', $validated['user_ids'])->get();
                foreach ($targetUsers as $targetUser) {
                    if (!empty($allowedDeptIds) && !in_array((int)$targetUser->dept_id, array_map('intval', $allowedDeptIds))) {
                        return response()->json([
                            'success' => false,
                            'message' => 'คุณไม่มีสิทธิ์แชร์ให้พนักงานนอกแผนก (สิทธิ์แชร์ข้ามแผนกเฉพาะ Admin และ Editor เท่านั้น)',
                        ], 403);
                    }
                }
            }
        }

        $shareChannel = $validated['share_channel'] ?? 'link';
        $createdShares = [];

        // Share to all users (public in organization) if user_id is 0
        if (!empty($validated['user_ids'])) {
            foreach ($validated['user_ids'] as $targetUserId) {
                // Prevent duplicate active share
                $existing = FormShare::where('form_type', $validated['form_type'])
                    ->where('form_id', $validated['form_id'])
                    ->where('shared_to_user_id', $targetUserId)
                    ->first();

                if (!$existing) {
                    $createdShares[] = FormShare::create([
                        'form_type' => $validated['form_type'],
                        'form_id' => $validated['form_id'],
                        'shared_by' => $sender->id,
                        'shared_to_user_id' => $targetUserId,
                        'share_channel' => $shareChannel,
                        'note' => $validated['note'] ?? null,
                        'access_token' => Str::random(32),
                    ]);
                }
            }
        }

        // Share to departments
        if (!empty($validated['department_ids'])) {
            foreach ($validated['department_ids'] as $targetDeptId) {
                $existing = FormShare::where('form_type', $validated['form_type'])
                    ->where('form_id', $validated['form_id'])
                    ->where('shared_to_dept_id', $targetDeptId)
                    ->first();

                if (!$existing) {
                    $createdShares[] = FormShare::create([
                        'form_type' => $validated['form_type'],
                        'form_id' => $validated['form_id'],
                        'shared_by' => $sender->id,
                        'shared_to_dept_id' => $targetDeptId,
                        'share_channel' => $shareChannel,
                        'note' => $validated['note'] ?? null,
                        'access_token' => Str::random(32),
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'บันทึกการแชร์และกำหนดสิทธิ์เรียบร้อยแล้ว',
            'count' => count($createdShares),
        ]);
    }

    /**
     * Get share history for a specific form
     */
    public function history(Request $request)
    {
        $request->validate([
            'form_type' => 'required|string',
            'form_id' => 'required|integer',
        ]);

        $shares = FormShare::with(['sender', 'recipientUser', 'recipientDept', 'revokedByUser'])
            ->where('form_type', $request->input('form_type'))
            ->where('form_id', $request->input('form_id'))
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($s) {
                $recipientText = '-';
                $recipientType = 'user';
                if ((int)$s->shared_to_user_id === 0 && $s->shared_to_dept_id === null) {
                    $recipientText = 'ทุกคนในองค์กร (สาธารณะ)';
                    $recipientType = 'all';
                } elseif ($s->recipientUser) {
                    $recipientText = ($s->recipientUser->firstname ?? '') . ' ' . ($s->recipientUser->lastname ?? '') . ' (' . ($s->recipientUser->emp_code ?? '') . ')';
                } elseif ($s->recipientDept) {
                    $recipientText = 'แผนก: ' . ($s->recipientDept->department_name ?? '-');
                    $recipientType = 'dept';
                }

                $senderName = $s->sender ? (($s->sender->firstname ?? '') . ' ' . ($s->sender->lastname ?? '')) : 'ระบบ';

                $revokedByName = null;
                $revokedAt = null;
                if ($s->revoked_at) {
                    $revokedByName = $s->revokedByUser
                        ? trim(($s->revokedByUser->firstname ?? '') . ' ' . ($s->revokedByUser->lastname ?? ''))
                        : 'ระบบ';
                    $revokedAt = $s->revoked_at->format('d/m/Y H:i');
                }

                return [
                    'id' => $s->id,
                    'recipient' => $recipientText,
                    'recipient_type' => $recipientType,
                    'sender' => $senderName,
                    'channel' => $s->share_channel,
                    'note' => $s->note,
                    'created_at' => $s->created_at ? $s->created_at->format('d/m/Y H:i') : '-',
                    'is_revoked' => !is_null($s->revoked_at),
                    'revoked_by' => $revokedByName,
                    'revoked_at' => $revokedAt,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $shares,
        ]);
    }

    /**
     * Revoke a share (soft-revoke: marks as revoked instead of deleting)
     */
    public function destroy($id)
    {
        $share = FormShare::findOrFail($id);
        $user = auth()->user();

        if ($user->id !== $share->shared_by && !$user->isHrOrAdmin()) {
            return response()->json(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ยกเลิกการแชร์นี้'], 403);
        }

        if ($share->revoked_at) {
            return response()->json(['success' => false, 'message' => 'การแชร์นี้ถูกยกเลิกไปแล้ว'], 422);
        }

        $share->update([
            'revoked_by' => $user->id,
            'revoked_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ยกเลิกสิทธิ์การเข้าถึงเรียบร้อยแล้ว (บันทึกประวัติไว้)',
        ]);
    }

    /**
     * Search users and departments for share modal autocomplete.
     * When form_type and form_id are provided, only return users
     * belonging to the same department as the document.
     */
    public function searchTargets(Request $request)
    {
        $q = trim((string)$request->input('q'));
        $formType = $request->input('form_type');
        $formId = $request->input('form_id');

        // Resolve the department name from the document
        $docDeptName = null;
        if ($formType && $formId) {
            $docDeptName = $this->resolveDocumentDepartment($formType, $formId);
        }

        // Find department IDs matching the document's department name
        $allowedDeptIds = [];
        if ($docDeptName) {
            $allowedDeptIds = Department::where('department_name', 'like', "%{$docDeptName}%")
                ->orWhere('department_fullname', 'like', "%{$docDeptName}%")
                ->pluck('department_id')
                ->toArray();
        }

        // Query employees — filter by department if document has a known department
        $usersQuery = User::query();

        if (!empty($allowedDeptIds)) {
            $usersQuery->whereIn('dept_id', $allowedDeptIds);
        }

        if (!empty($q)) {
            // Split keyword by whitespace to allow searching e.g. "สมชาย ใจดี" or "10044 กิตติพงศ์"
            $terms = preg_split('/\s+/u', $q, -1, PREG_SPLIT_NO_EMPTY);
            
            $usersQuery->where(function ($query) use ($terms, $q) {
                foreach ($terms as $term) {
                    $query->where(function ($sub) use ($term) {
                        $sub->where('firstname', 'like', "%{$term}%")
                            ->orWhere('lastname', 'like', "%{$term}%")
                            ->orWhere('emp_code', 'like', "%{$term}%")
                            ->orWhere('email', 'like', "%{$term}%")
                            ->orWhere('username', 'like', "%{$term}%")
                            ->orWhereRaw("CONCAT(TRIM(COALESCE(firstname, '')), ' ', TRIM(COALESCE(lastname, ''))) LIKE ?", ["%{$term}%"])
                            ->orWhereRaw("CONCAT(TRIM(COALESCE(firstname, '')), TRIM(COALESCE(lastname, ''))) LIKE ?", ["%{$term}%"]);
                    });
                }
            });
        }

        // Return all matching results (or all employees in the same department when blank)
        $users = $usersQuery->orderBy('emp_code', 'asc')->get()->map(function ($u) {
            $firstName = trim($u->firstname ?? '');
            $lastName = trim($u->lastname ?? '');
            $fullName = trim($firstName . ' ' . $lastName);
            $statusText = ($u->status === 'resign') ? ' (ลาออก)' : '';
            return [
                'id' => $u->id,
                'type' => 'user',
                'name' => $fullName ?: ($u->username ?? 'User #' . $u->id),
                'code' => $u->emp_code ?? '',
                'label' => ($fullName ?: ($u->username ?? 'User #' . $u->id)) . ($u->emp_code ? ' (' . $u->emp_code . ')' : '') . $statusText,
                'is_resigned' => ($u->status === 'resign'),
            ];
        });

        // Check if current authenticated user is Admin or Editor based strictly on hr_user_roles (hrsystem)
        $currentUser = auth()->user();
        $isAdminOrEditor = ($currentUser && (
            in_array($currentUser->hr_role, ['admin', 'editor']) 
            || (method_exists($currentUser, 'isEditor') && $currentUser->isEditor())
        ));

        $deptsQuery = Department::query();

        if (!empty($q)) {
            $deptsQuery->where(function ($query) use ($q) {
                $query->where('department_name', 'like', "%{$q}%")
                      ->orWhere('department_fullname', 'like', "%{$q}%");
            });
        }
        $depts = $deptsQuery->orderBy('department_name', 'asc')->get()->map(function ($d) use ($allowedDeptIds) {
            $isDocDept = !empty($allowedDeptIds) && in_array((int)$d->department_id, array_map('intval', $allowedDeptIds));
            return [
                'id' => $d->department_id,
                'type' => 'dept',
                'name' => $d->department_name,
                'fullname' => $d->department_fullname ?? '',
                'label' => '[แผนก] ' . $d->department_name,
                'is_doc_dept' => $isDocDept,
            ];
        });

        return response()->json([
            'users' => $users,
            'departments' => $depts,
            'total_found' => $users->count(),
            'filtered_dept' => $docDeptName,
            'can_share_other_depts' => $isAdminOrEditor,
        ]);
    }

    /**
     * Resolve the department name from a document (form) based on form_type and form_id.
     * Returns the department text stored in the document, or null if not found.
     */
    private function resolveDocumentDepartment(string $formType, $formId): ?string
    {
        try {
            switch ($formType) {
                case 'manpower_request':
                    $doc = \App\Models\ManpowerRequest::find($formId);
                    // ManpowerRequest stores 'department' (ฝ่าย) and 'section' (แผนก)
                    // Use 'section' first as it's the more specific department unit
                    if ($doc) {
                        return !empty($doc->section) ? $doc->section : ($doc->department ?? null);
                    }
                    break;

                case 'probation_evaluation':
                    $doc = \App\Models\ProbationEvaluation::find($formId);
                    return $doc->department ?? null;

                case 'interview_evaluation':
                    $doc = \App\Models\InterviewEvaluation::find($formId);
                    return $doc->department ?? null;
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('resolveDocumentDepartment failed: ' . $e->getMessage());
        }

        return null;
    }
}
