<?php

namespace App\Http\Controllers\backend\users;
use App\Http\Controllers\Controller;

use App\Models\Department;
use App\Models\Division;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserType;


use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
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
        
        $departments = Department::all();
        $divisions = Division::all();
        $sections = Section::all();

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
            'employee_code' => 'like',
            'prefix'        => 'like',
            'position'      => 'like',
            'level_user'    => '=',
            'hr_status'     => '=',
            'status'        => '=',
            'employee_type' => 'like',
            'workplace'     => 'like',
        ];

        foreach ($simpleFilters as $field => $operator) {
            if ($request->filled($field)) {
                $value = trim($request->input($field));
                $query->where($field, $operator, ($operator === 'like' ? "%{$value}%" : $value));
            }
        }

        if ($request->filled('fullname')) {
            $name = trim($request->input('fullname'));
            $query->where(function ($q) use ($name) {
                $q->where('first_name', 'like', "%{$name}%")
                  ->orWhere('last_name', 'like', "%{$name}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$name}%"]);
            });
        }

        $relations = [
            'department' => ['name' => 'department_name', 'code' => 'department_code'],
            'division'   => ['name' => 'division_name',   'code' => 'division_code'],
            'section'    => ['name' => 'section_name',    'code' => 'section_code'],
        ];

        foreach ($relations as $relation => $fields) {
            if ($request->filled($relation)) {
                $value = $request->input($relation);
                if (is_numeric($value)) {
                    $query->where("{$relation}_id", (int)$value);
                } else {
                    $query->whereHas($relation, function ($q) use ($fields, $value) {
                        $q->where($fields['name'], 'like', "%{$value}%")
                          ->orWhere($fields['code'], 'like', "%{$value}%");
                    });
                }
            }
        }

        $from = $request->input('startwork_date_from');
        $to   = $request->input('startwork_date_to');

        if ($from && $to) {
            $query->whereBetween('startwork_date', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay()
            ]);
        } elseif ($from) {
            $query->whereDate('startwork_date', '>=', Carbon::parse($from));
        } elseif ($to) {
            $query->whereDate('startwork_date', '<=', Carbon::parse($to));
        }

        // เรียงรหัสพนักงานจากน้อยไปมาก
        $query->orderBy('employee_code', 'asc');

        return $query;
    }


public function create()
{
    $departments = Department::all();
    $divisions = Division::all();
    $sections = Section::all();

    return view('backend.users.create', compact('departments', 'divisions', 'sections'));
}


public function store(Request $request)
{
    $validated = $request->validate(
        [
            // unique บนตาราง + connection ตามที่ใช้จริง
            'employee_code' => [
                'required',
                'max:50',
                Rule::unique('userkml2025.userskml', 'employee_code'),
            ],
            'sex'           => 'required|string|max:20',
            'prefix'        => 'required|string|max:50',
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'position'      => 'nullable|string|max:255',
            'employee_type' => 'nullable|string|max:255',
            'workplace'    => 'nullable|string|max:255',
            'department_id' => 'nullable|integer|exists:userkml2025.department,department_id',
            'division_id'   => 'nullable|integer|exists:userkml2025.divisions,division_id',
            'section_id'    => 'nullable|integer|exists:userkml2025.sections,section_id',

            'level_user'    => 'required|integer',
            'hr_status'     => 'required|integer',
            'startwork_date' => 'nullable|date',
        ],
        [
            'employee_code.required' => 'กรุณาระบุรหัสพนักงาน',
            'employee_code.unique'   => 'รหัสพนักงานนี้มีอยู่ในระบบแล้ว',
            'first_name.required'    => 'กรุณาระบุชื่อจริง',
            'last_name.required'     => 'กรุณาระบุนามสกุล',
            'sex.required'           => 'กรุณาเลือกเพศ',
            'prefix.required'        => 'กรุณาระบุคำนำหน้า',
            'level_user.required'    => 'กรุณาเลือกระดับพนักงาน',
            'hr_status.required'     => 'กรุณาเลือกสถานะ HR',
        ]
    );

    DB::transaction(function () use ($validated) {
        // $fullname = trim(($validated['prefix'] ?? '') . ' ' . $validated['first_name'] . ' ' . $validated['last_name']);

        $user = new User();
        $user->employee_code = $validated['employee_code'];
        $user->sex           = $validated['sex'];
        $user->prefix        = $validated['prefix'];
        $user->first_name    = $validated['first_name'];
        $user->last_name     = $validated['last_name'];
        // $user->fullname      = $fullname;         // ถ้ามีคอลัมน์นี้ในตาราง

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
    $departments = Department::all();
    $divisions = Division::all();
    $sections = Section::all();
    $userTypes = UserType::where('status', 0)->get();

    return view('backend.users.edit', compact('user', 'departments', 'divisions', 'sections', 'userTypes'));
}

public function update(Request $request, $id)
{
    $validated = $request->validate(
        [
            'employee_code' => [
                'required',
                'max:50',
                Rule::unique('userkml2025.userskml', 'employee_code')->ignore($id, 'id'),
            ],
            'sex'           => 'required|string|max:20',
            'prefix'        => 'required|string|max:50',
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'position'      => 'nullable|string|max:255',
            'employee_type' => 'nullable|string|max:255',
            'workplace'    => 'nullable|string|max:255',
            'department_id' => 'nullable|integer|exists:userkml2025.department,department_id',
            'division_id'   => 'nullable|integer|exists:userkml2025.divisions,division_id',
            'section_id'    => 'nullable|integer|exists:userkml2025.sections,section_id',
            'level_user'    => 'required|integer',
            'hr_status'     => 'required|integer',
            'status'        => 'required|integer',
            'startwork_date' => 'nullable|date',
            'endwork_date' => 'nullable|date|required_if:status,' . User::STATUS_INACTIVE,
            'endwork_comment' => 'nullable|string|max:1000|required_if:status,' . User::STATUS_INACTIVE,
        ],
        [
            'employee_code.required' => 'กรุณาระบุรหัสพนักงาน',
            'employee_code.unique'   => 'รหัสพนักงานนี้มีอยู่ในระบบแล้ว',
            'first_name.required'    => 'กรุณาระบุชื่อจริง',
            'last_name.required'     => 'กรุณาระบุนามสกุล',
            'sex.required'           => 'กรุณาเลือกเพศ',
            'prefix.required'        => 'กรุณาระบุคำนำหน้า',
            'level_user.required'    => 'กรุณาเลือกระดับพนักงาน',
            'hr_status.required'     => 'กรุณาเลือกสถานะ HR',
            'status.required'        => 'กรุณาเลือกสถานะพนักงาน',
            'endwork_date.required_if' => 'กรุณาเลือกวันที่สิ้นสุดการทำงานกรณีไม่ใช้งาน',
            'endwork_comment.required_if' => 'กรุณาระบุเหตุผลกรณีไม่ใช้งาน',
        ]
    );

    DB::transaction(function () use ($validated, $id) {
        // $fullname = trim(($validated['prefix'] ?? '') . ' ' . $validated['first_name'] . ' ' . $validated['last_name']);

        $user = User::findOrFail($id);
        $user->employee_code = $validated['employee_code'];
        $user->sex           = $validated['sex'];
        $user->prefix        = $validated['prefix'];    
        $user->first_name    = $validated['first_name'];
        $user->last_name     = $validated['last_name'];
        // $user->fullname      = $fullname;         // ถ้ามีคอลัม
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

        // รองรับทั้งกรณีมี/ยังไม่มีคอลัมน์ในฐานข้อมูล เพื่อกัน error ระหว่างรอ migrate
        $schema = Schema::connection($user->getConnectionName());
        if ($schema->hasColumn($user->getTable(), 'endwork_date')) {
            $user->endwork_date = $isInactive ? ($validated['endwork_date'] ?? null) : null;
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