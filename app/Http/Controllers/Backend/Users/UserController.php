<?php

namespace App\Http\Controllers\Backend\Users;
use App\Http\Controllers\Controller;

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
        $users = $this->filteredUsers($request)->paginate(50)->withQueryString();
        
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
        $query = User::with(['department', 'division', 'section']);

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

        if ($request->filled('employee_code')) {
            $value = trim($request->input('employee_code'));
            $query->where('emp_code', 'like', "%{$value}%");
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
    $departments = Cache::remember('all_departments', 3600, fn() => Department::all());
    $divisions = Cache::remember('all_divisions', 3600, fn() => Division::all());
    $sections = Cache::remember('all_sections', 3600, fn() => Section::all());

    return view('backend.users.create', compact('departments', 'divisions', 'sections'));
}


public function store(StoreUserRequest $request)
{
    $validated = $request->validated();

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

        $user->startwork_date = $validated['startwork_date'] ?? null;
        $user->save();
    });

    return redirect()->route('users.index')->with('success', 'บันทึกข้อมูลพนักงานเรียบร้อยแล้ว');
}

public function show($id)
{
    $user = User::with(['department', 'division', 'section'])->findOrFail($id);
    return view('backend.users.detail', compact('user'));
}

public function edit($id)
{
    $user = User::findOrFail($id);
    $departments = Cache::remember('all_departments', 3600, fn() => Department::all());
    $divisions = Cache::remember('all_divisions', 3600, fn() => Division::all());
    $sections = Cache::remember('all_sections', 3600, fn() => Section::all());
    $userTypes = UserType::where('status', 0)->get();

    return view('backend.users.edit', compact('user', 'departments', 'divisions', 'sections', 'userTypes'));
}

public function update(UpdateUserRequest $request, $id)
{
    $validated = $request->validated();

    DB::transaction(function () use ($validated, $id) {
        $user = User::findOrFail($id);
        $user->employee_code = $validated['employee_code'];
        $user->sex           = $validated['sex'];
        $user->prefix        = $validated['prefix'];    
        $user->firstname    = $validated['first_name'];
        $user->lastname     = $validated['last_name'];
        $user->position      = $validated['position'] ?? null;
        $user->employee_type = $validated['employee_type'] ?? null;
        $user->workplace     = $validated['workplace'] ?? null;
        $user->department_id = $validated['department_id'] ?? null;
        $user->division_id   = $validated['division_id'] ?? null;
        $user->section_id    = $validated['section_id'] ?? null;
        $user->level_user    = $validated['level_user'];
        $user->hr_status     = $validated['hr_status'];
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

    return redirect()->route('users.index')->with('success', 'อัปเดตข้อมูลพนักงานเรียบร้อยแล้ว');  
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'ลบข้อมูลพนักงานเรียบร้อยแล้ว');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($request->hasFile('avatar')) {
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

            return response()->json([
                'success' => true,
                'message' => 'อัปโหลดรูปภาพสำเร็จ',
                'avatar_url' => asset($user->photo_user)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'ไม่พบไฟล์รูปภาพ'], 400);
    }
}