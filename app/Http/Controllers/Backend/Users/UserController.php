<?php

namespace App\Http\Controllers\Backend\Users;
use App\Http\Controllers\Controller;

use App\Models\HrUserRole;
use App\Models\Department;
use App\Models\Division;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserType;
use App\Http\Requests\Backend\Users\StoreUserRequest;
use App\Http\Requests\Backend\Users\UpdateUserRequest;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    // API: รองรับฟิลเตอร์แบบเดียวกับหน้าเว็บ
    public function apiUsers(Request $request)
    {
        // ส่งกลับเป็นรูปแบบ paginate เพื่อให้ frontend ใช้ข้อมูลหน้า/ลิงก์ได้
        $perPage = (int) $request->input('per_page', 50);
        // กันค่าแปลกๆ
        $perPage = max(1, min($perPage, 100));

        $users = $this->filteredUsers($request)
            ->paginate($perPage)
            ->appends($request->query());


        return response()->json($users);
    }

    public function profileUser(){
        // Get the currently authenticated user as a single model instance
        $user = Auth::user();

        // If not authenticated, redirect to login (or handle as you prefer)
        if (!$user) {
            return redirect()->route('login');
        }

        // Pass a single $user to the view
        return view('backend.users.profile', compact('user'));
    }

    // หน้าเว็บ: แสดงผลแบบ paginate + ส่งค่ากลับไปเติมในฟอร์ม
    public function index(Request $request)
    {
        if (auth()->check() && !auth()->user()->canManageUsers()) {
            abort(403, 'คุณไม่มีสิทธิ์จัดการข้อมูลพนักงาน (เฉพาะสิทธิ์ ADMIN เท่านั้น)');
        }

        $perPage = (int) $request->input('per_page', 50);
        $perPage = max(1, min($perPage, 100));

        $users = $this->filteredUsers($request)->paginate($perPage)->withQueryString();
        
        if ($request->ajax()) {
            return view('backend.users.partials.table', compact('users'))->render();
        }

        $departments = Cache::remember('all_departments', 3600, fn() => Department::all());
        $divisions = Cache::remember('all_divisions', 3600, fn() => Division::all());
        $sections = Cache::remember('all_sections', 3600, fn() => Section::all());

        return view('backend.users.index', compact('users', 'departments', 'divisions', 'sections'));
    }

    /**
     * Core filter (ใช้ร่วมกันทั้ง apiUsers และ index)
     * @param  Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function filteredUsers(Request $request)
    {
        $query = User::with(['department', 'division', 'section', 'hrRole']);

        $simpleFilters = [
            'prefix'        => 'like',
            'position'      => 'like',
            'level_user'    => '=',
            'hr_status'     => '=',
            'status'        => '=',
            'employee_type' => 'like',
            'workplace'     => 'like',
        ];

        // Cache the schema column listings to avoid database metadata queries on every filter
        $columns = Cache::remember('user_employees_columns', 3600, function () {
            try {
                return Schema::connection('userkml2025')->getColumnListing('employees');
            } catch (\Exception $e) {
                // Fallback to static columns if DB connection fails
                return ['prefix', 'position', 'level_user', 'hr_status', 'status', 'employee_type', 'workplace', 'emp_code', 'created_at', 'resign_date'];
            }
        });

        foreach ($simpleFilters as $field => $operator) {
            if ($request->filled($field)) {
                $value = trim($request->input($field));
                if (in_array($field, $columns)) {
                    $query->where($field, $operator, ($operator === 'like' ? "%{$value}%" : $value));
                }
            }
        }

        if ($request->filled('keyword')) {
            $keyword = trim($request->input('keyword'));
            $query->where(function ($q) use ($keyword) {
                $q->where('emp_code', 'like', "%{$keyword}%")
                  ->orWhere('firstname', 'like', "%{$keyword}%")
                  ->orWhere('lastname', 'like', "%{$keyword}%")
                  ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$keyword}%"])
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('username', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('employee_code')) {
            $value = trim($request->input('employee_code'));
            $query->where('emp_code', 'like', "%{$value}%");
        }

        if ($request->filled('role')) {
            $roleFilter = strtolower(trim($request->input('role')));
            $codesForRole = HrUserRole::where('role', $roleFilter)->pluck('employee_code')->toArray();
            
            if ($roleFilter === 'admin') {
                $query->where(function($q) use ($codesForRole) {
                    $q->whereIn('emp_code', $codesForRole)
                      ->orWhere('role', 'admin')
                      ->orWhere('role', 'superadmin');
                });
            } elseif ($roleFilter === 'editor') {
                $query->whereIn('emp_code', $codesForRole);
            } elseif ($roleFilter === 'viewer') {
                // Users explicitly marked viewer OR not having admin/editor hr_user_roles and central not admin
                $otherCodes = HrUserRole::whereIn('role', ['admin', 'editor'])->pluck('employee_code')->toArray();
                $query->whereNotIn('emp_code', $otherCodes)
                      ->where('role', '!=', 'admin');
            }
        }

        if ($request->filled('fullname')) {
            $name = trim($request->input('fullname'));
            $query->where(function ($q) use ($name) {
                $q->where('firstname', 'like', "%{$name}%")
                  ->orWhere('lastname', 'like', "%{$name}%")
                  ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$name}%"]);
            });
        }

        if ($request->filled('department')) {
            $val = $request->input('department');
            if (is_numeric($val)) {
                $query->where('dept_id', (int)$val);
            } else {
                $query->whereHas('department', function ($q) use ($val) {
                    $q->where('department_name', 'like', "%{$val}%")
                      ->orWhere('department_code', 'like', "%{$val}%");
                });
            }
        }

        if ($request->filled('division')) {
            $val = $request->input('division');
            if (is_numeric($val)) {
                $query->whereHas('division', fn($q) => $q->where('divisions.division_id', (int)$val));
            } else {
                $query->whereHas('division', function ($q) use ($val) {
                    $q->where('division_name', 'like', "%{$val}%")
                      ->orWhere('division_code', 'like', "%{$val}%");
                });
            }
        }

        if ($request->filled('section')) {
            $val = $request->input('section');
            if (is_numeric($val)) {
                $query->whereHas('section', fn($q) => $q->where('sections.section_id', (int)$val));
            } else {
                $query->whereHas('section', function ($q) use ($val) {
                    $q->where('section_name', 'like', "%{$val}%")
                      ->orWhere('section_code', 'like', "%{$val}%");
                });
            }
        }

        $from = $request->input('startwork_date_from');
        $to   = $request->input('startwork_date_to');

        if ($from && $to) {
            $query->whereBetween('created_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);
        } elseif ($from) {
            $query->whereDate('created_at', '>=', Carbon::parse($from));
        } elseif ($to) {
            $query->whereDate('created_at', '<=', Carbon::parse($to));
        }

        // เรียงรหัสพนักงานจากน้อยไปมาก
        $query->orderBy('emp_code', 'asc');

        return $query;
    }


public function create()
{
    abort(403, 'ระบบไม่อนุญาตให้เพิ่มข้อมูลพนักงานโดยตรง ข้อมูลพนักงานจะถูกเชื่อมโยงมาจากฐานข้อมูลกลาง (Central User Database)');
}


public function store(StoreUserRequest $request)
{
    abort(403, 'ระบบไม่อนุญาตให้เพิ่มข้อมูลพนักงานโดยตรง ข้อมูลพนักงานจะถูกเชื่อมโยงมาจากฐานข้อมูลกลาง (Central User Database)');

    // ตรวจสอบสิทธิ์การให้ ADMIN: เฉพาะฝ่าย 16 Information Communication Technology เท่านั้น
    if (($validated['role'] ?? '') === 'admin' && !auth()->user()->canAssignAdminRole()) {
        return back()->withInput()->with('error', 'เฉพาะผู้ดูแลระบบสังกัดฝ่าย 16 Information Communication Technology เท่านั้นที่สามารถกำหนดสิทธิ์เป็น ADMIN ได้');
    }

    DB::transaction(function () use ($validated) {
        $user = new User();
        $user->employee_code = $validated['employee_code'];
        $user->sex           = $validated['sex'];
        $user->prefix        = $validated['prefix'];
        $user->firstname    = $validated['first_name'];
        $user->lastname     = $validated['last_name'];

        $user->position      = $validated['position'] ?? null;
        $user->workplace     = $validated['workplace'] ?? null;
        $user->employee_type = $validated['employee_type'] ?? null;
        $user->department_id = $validated['department_id'] ?? null;
        $user->division_id   = $validated['division_id'] ?? null;
        $user->section_id    = $validated['section_id'] ?? null;

        $user->level_user    = $validated['level_user'];
        $user->hr_status     = $validated['hr_status'];

        // Central database role remains 'staff' unless set to admin
        $user->role          = ($validated['role'] ?? '') === 'admin' ? 'admin' : 'staff';

        $user->startwork_date = $validated['startwork_date'] ?? null;
        $user->save();

        // Save HR System Rule (admin, editor, viewer) in Database: hrsystem
        if (!empty($validated['role'])) {
            HrUserRole::updateOrCreate(
                ['employee_code' => (string)$user->employee_code],
                [
                    'employee_id' => $user->id,
                    'role'        => $validated['role'],
                    'updated_at'  => now(),
                ]
            );
        }
    });

    return redirect()->route('users.index')->with('success', 'บันทึกข้อมูลพนักงานเรียบร้อยแล้ว');
}

public function show($id)
{
    if (auth()->check() && !auth()->user()->canManageUsers()) {
        abort(403, 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลพนักงาน (สำหรับ Admin เท่านั้น)');
    }

    $user = User::with(['department', 'division', 'section'])->findOrFail($id);
    return view('backend.users.detail', compact('user'));
}

public function edit($id)
{
    if (auth()->check() && !auth()->user()->canManageUsers()) {
        abort(403, 'คุณไม่มีสิทธิ์แก้ไขข้อมูลพนักงาน (สำหรับ Admin เท่านั้น)');
    }

    $user = User::findOrFail($id);
    $departments = Cache::remember('all_departments', 3600, fn() => Department::all());
    $divisions = Cache::remember('all_divisions', 3600, fn() => Division::all());
    $sections = Cache::remember('all_sections', 3600, fn() => Section::all());
    $userTypes = UserType::where('status', 0)->orWhere('status', 1)->get();

    return view('backend.users.edit', compact('user', 'departments', 'divisions', 'sections', 'userTypes'));
}

public function update(UpdateUserRequest $request, $id)
{
    if (auth()->check() && !auth()->user()->canManageUsers()) {
        abort(403, 'คุณไม่มีสิทธิ์แก้ไขข้อมูลพนักงาน (สำหรับ Admin เท่านั้น)');
    }

    $validated = $request->validated();
    $targetUser = User::findOrFail($id);

    // ตรวจสอบสิทธิ์การให้ ADMIN: ถ้าเปลี่ยนคนที่ไม่ใช่ admin ให้เป็น admin ต้องเป็นฝ่าย 16 ICT เท่านั้น
    if (($validated['role'] ?? '') === 'admin' && $targetUser->hr_role !== 'admin' && !auth()->user()->canAssignAdminRole()) {
        return back()->withInput()->with('error', 'เฉพาะผู้ดูแลระบบสังกัดฝ่าย 16 Information Communication Technology เท่านั้นที่สามารถเปลี่ยนสิทธิ์เป็น ADMIN ได้');
    }

    $oldUserValues = [
        'prefix' => $targetUser->prefix,
        'firstname' => $targetUser->firstname,
        'lastname' => $targetUser->lastname,
        'position' => $targetUser->position,
        'employee_type' => $targetUser->employee_type,
        'workplace' => $targetUser->workplace,
        'department_id' => $targetUser->department_id,
        'division_id' => $targetUser->division_id,
        'section_id' => $targetUser->section_id,
        'level_user' => $targetUser->level_user,
        'hr_status' => $targetUser->hr_status,
        'status' => $targetUser->status,
        'role' => $targetUser->hr_role,
        'startwork_date' => $targetUser->startwork_date,
    ];

    DB::transaction(function () use ($validated, $targetUser) {
        $user = $targetUser;
        $schema = Schema::connection($user->getConnectionName());
        $table = $user->getTable();

        $user->employee_code = $validated['employee_code'];
        if ($schema->hasColumn($table, 'sex')) {
            $user->sex = $validated['sex'];
        }
        if ($schema->hasColumn($table, 'prefix')) {
            $user->prefix = $validated['prefix'];
        }
        $user->firstname    = $validated['first_name'];
        $user->lastname     = $validated['last_name'];
        if ($schema->hasColumn($table, 'position')) {
            $user->position = $validated['position'] ?? null;
        }
        if ($schema->hasColumn($table, 'employee_type')) {
            $user->employee_type = $validated['employee_type'] ?? null;
        }
        if ($schema->hasColumn($table, 'workplace')) {
            $user->workplace = $validated['workplace'] ?? null;
        }
        if ($schema->hasColumn($table, 'department_id')) {
            $user->department_id = $validated['department_id'] ?? null;
        }
        if ($schema->hasColumn($table, 'division_id')) {
            $user->division_id = $validated['division_id'] ?? null;
        }
        if ($schema->hasColumn($table, 'section_id')) {
            $user->section_id = $validated['section_id'] ?? null;
        }
        if ($schema->hasColumn($table, 'level_user')) {
            $user->level_user = $validated['level_user'];
        }
        if ($schema->hasColumn($table, 'hr_status')) {
            $user->hr_status = $validated['hr_status'];
        }

        // Central database role: keep 'admin' if role is admin, otherwise 'staff'
        if (!empty($validated['role']) && auth()->user()->isAdmin()) {
            $user->role = $validated['role'] === 'admin' ? 'admin' : 'staff';
            
            // Save HR System Rule (admin, editor, viewer) in Database: hrsystem
            HrUserRole::updateOrCreate(
                ['employee_code' => (string)$user->employee_code],
                [
                    'employee_id' => $user->id,
                    'role'        => $validated['role'],
                    'updated_at'  => now(),
                ]
            );
        }
        $user->status        = $validated['status'];
        $user->startwork_date = $validated['startwork_date'] ?? null;

        $isInactive = (string)($validated['status'] ?? '') === (string)User::STATUS_INACTIVE;

        $schema = Schema::connection($user->getConnectionName());
        if ($schema->hasColumn($user->getTable(), 'endwork_date')) {
            $user->endwork_date = $isInactive ? ($validated['endwork_date'] ?? null) : null;
        } elseif ($schema->hasColumn($user->getTable(), 'resign_date')) {
            $user->resign_date = $isInactive ? ($validated['endwork_date'] ?? null) : null;
        }
        if ($schema->hasColumn($user->getTable(), 'endwork_comment')) {
            $user->endwork_comment = $isInactive ? ($validated['endwork_comment'] ?? null) : null;
        }
        $user->save();
    });

    if (class_exists(\App\Services\AuditLogService::class)) {
        try {
            $newUserValues = [
                'prefix' => $targetUser->prefix,
                'firstname' => $targetUser->firstname,
                'lastname' => $targetUser->lastname,
                'position' => $targetUser->position,
                'employee_type' => $targetUser->employee_type,
                'workplace' => $targetUser->workplace,
                'department_id' => $targetUser->department_id,
                'division_id' => $targetUser->division_id,
                'section_id' => $targetUser->section_id,
                'level_user' => $targetUser->level_user,
                'hr_status' => $targetUser->hr_status,
                'status' => $targetUser->status,
                'role' => $targetUser->hr_role,
                'startwork_date' => $targetUser->startwork_date,
            ];
            \App\Services\AuditLogService::log(
                action: 'updated',
                description: "แก้ไขข้อมูลพนักงาน: {$targetUser->fullname} (รหัส: {$targetUser->employee_code})",
                model: $targetUser,
                oldValues: $oldUserValues,
                newValues: $newUserValues,
                module: 'users',
                moduleName: 'จัดการผู้ใช้งานและสิทธิ์'
            );
        } catch (\Throwable $e) {
            Log::warning('AuditLog recording failed for user update: ' . $e->getMessage());
        }
    }

    return redirect()->route('users.index')->with('success', 'อัปเดตข้อมูลพนักงานเรียบร้อยแล้ว');  
}

public function updateRole(Request $request, $id)
{
    if (auth()->check() && !auth()->user()->canManageUsers()) {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => 'คุณไม่มีสิทธิ์จัดการสิทธิ์พนักงาน (เฉพาะสิทธิ์ ADMIN เท่านั้น)'], 403);
        }
        abort(403, 'คุณไม่มีสิทธิ์จัดการสิทธิ์พนักงาน (เฉพาะสิทธิ์ ADMIN เท่านั้น)');
    }

    $validator = Validator::make($request->all(), [
        'role' => ['required', 'string', Rule::in(['admin', 'editor', 'viewer'])],
    ], [
        'role.required' => 'กรุณาเลือกสิทธิ์ที่ต้องการกำหนด',
        'role.in'       => 'สิทธิ์ที่เลือกไม่ถูกต้อง (ต้องเป็น admin, editor หรือ viewer)',
    ]);

    if ($validator->fails()) {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }
        return back()->withErrors($validator)->withInput();
    }

    $targetUser = User::findOrFail($id);
    $oldRole = $targetUser->hr_role ?? 'viewer';
    $newRole = strtolower(trim($request->input('role')));

    // ตรวจสอบสิทธิ์การให้ ADMIN: ถ้าเปลี่ยนคนที่ไม่ใช่ admin ให้เป็น admin ต้องเป็นฝ่าย 16 ICT เท่านั้น
    if ($newRole === 'admin' && $targetUser->hr_role !== 'admin' && !auth()->user()->canAssignAdminRole()) {
        $msg = 'เฉพาะผู้ดูแลระบบสังกัดฝ่าย 16 Information Communication Technology เท่านั้นที่สามารถเปลี่ยนสิทธิ์เป็น ADMIN ได้';
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => $msg], 403);
        }
        return back()->with('error', $msg);
    }

    DB::transaction(function () use ($targetUser, $newRole) {
        // บันทึก/อัปเดตสิทธิ์ลงตาราง hr_user_roles (Database: hrsystem)
        HrUserRole::updateOrCreate(
            ['employee_code' => (string)$targetUser->employee_code],
            [
                'employee_id' => $targetUser->id,
                'role'        => $newRole,
                'updated_at'  => now(),
            ]
        );

        // ปรับ role ในฐานข้อมูลหลัก (central)
        $targetUser->role = ($newRole === 'admin' ? 'admin' : 'staff');
        $targetUser->save();
    });

    Cache::forget("user_hr_role_{$targetUser->employee_code}");

    if (class_exists(\App\Services\AuditLogService::class)) {
        try {
            \App\Services\AuditLogService::log(
                action: 'role_changed',
                description: "ปรับเปลี่ยนสิทธิ์ผู้ใช้งานของ {$targetUser->fullname} (รหัส: {$targetUser->employee_code}) จาก " . strtoupper($oldRole) . " เป็น " . strtoupper($newRole),
                model: $targetUser,
                oldValues: ['role' => $oldRole],
                newValues: ['role' => $newRole],
                module: 'users',
                moduleName: 'จัดการผู้ใช้งานและสิทธิ์'
            );
        } catch (\Throwable $e) {
            Log::warning('AuditLog recording failed for updateRole: ' . $e->getMessage());
        }
    }

    $roleDisplay = match($newRole) {
        'admin' => '<span class="inline-flex items-center gap-1.5 font-medium text-purple-600 dark:text-purple-400"><i class="fa-solid fa-shield-halved text-purple-600"></i> Admin</span>',
        'editor' => '<span class="inline-flex items-center gap-1.5 font-medium text-cyan-600 dark:text-cyan-400"><i class="fa-solid fa-pen text-cyan-500"></i> Editor</span>',
        default => '<span class="inline-flex items-center gap-1.5 font-medium text-emerald-600 dark:text-emerald-400"><i class="fa-solid fa-eye text-emerald-500"></i> Viewer</span>',
    };

    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => "ปรับสิทธิ์การใช้งานของ {$targetUser->fullname} เป็น " . strtoupper($newRole) . " เรียบร้อยแล้ว",
            'role' => $newRole,
            'role_label' => strtoupper($newRole),
            'role_html' => $roleDisplay,
        ]);
    }

    return redirect()->route('users.index')->with('success', "ปรับสิทธิ์การใช้งานของ {$targetUser->fullname} เป็น " . strtoupper($newRole) . " เรียบร้อยแล้ว");
}

public function destroy($id)
{
    abort(403, 'ระบบไม่อนุญาตให้ลบข้อมูลพนักงานโดยตรง ข้อมูลพนักงานจะถูกเชื่อมโยงมาจากฐานข้อมูลกลาง (Central User Database)');
}

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($request->hasFile('avatar')) {
            $oldPhoto = $user->photo_user;
            $file = $request->file('avatar');
            $filename = time() . '_' . $user->employee_code . '.' . $file->getClientOriginalExtension();
            
            // Ensure directory exists
            $path = public_path('images/profiles');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            // Delete old photo if exists
            if ($user->photo_user && File::exists(public_path($user->photo_user))) {
                File::delete(public_path($user->photo_user));
            }

            $file->move($path, $filename);
            $user->photo_user = 'images/profiles/' . $filename;
            $user->save();

            if (class_exists(\App\Services\AuditLogService::class)) {
                try {
                    \App\Services\AuditLogService::log(
                        action: 'avatar_updated',
                        description: "เปลี่ยนรูปโปรไฟล์พนักงาน: {$user->fullname} (รหัส: {$user->employee_code})",
                        model: $user,
                        oldValues: ['photo_user' => $oldPhoto],
                        newValues: ['photo_user' => $user->photo_user],
                        module: 'users',
                        moduleName: 'จัดการผู้ใช้งานและสิทธิ์'
                    );
                } catch (\Throwable $e) {
                    Log::warning('AuditLog recording failed for updateAvatar: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'อัปโหลดรูปภาพสำเร็จ',
                'avatar_url' => asset($user->photo_user)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'ไม่พบไฟล์รูปภาพ'], 400);
    }
}