<?php

namespace App\Http\Controllers\backend\requestdata;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function apiDepartment()
    {
        return response()->json(Department::all());
    }

    public function index()
    {
        $departments = Department::all();
        return view('backend.department.index', compact('departments'));
    }
}
