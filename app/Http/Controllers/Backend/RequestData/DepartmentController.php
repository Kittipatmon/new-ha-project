<?php

namespace App\Http\Controllers\Backend\RequestData;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Support\Facades\Cache;

class DepartmentController extends Controller
{
    public function apiDepartment()
    {
        $departments = Cache::remember('all_departments', 3600, fn() => Department::all());
        return response()->json($departments);
    }

    public function index()
    {
        $departments = Cache::remember('all_departments', 3600, fn() => Department::all());
        return view('backend.department.index', compact('departments'));
    }
}
