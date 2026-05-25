<?php

namespace App\Http\Controllers\backend\users;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Division;

class DepartmentController extends Controller
{
    public function apiDepartments()
    {
        $departments = Department::all();
        return response()->json($departments);
    }
    public function index()
    {
        $departments = Department::all();
        $divisions = Division::all();
        return view('backend.department.index', compact('departments', 'divisions'));
    }

    public function create()
    {
        $divisions = Division::all();
        return view('backend.department.create', compact('divisions'));
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        $divisions = Division::all();
        return view('backend.department.edit', compact('department', 'divisions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_name' => 'nullable|string|max:255',
            'department_fullname' => 'nullable|string|max:255',
            'department_status' => 'required|integer|in:0,1',
            'division_id' => 'nullable|integer|exists:divisions,division_id',
        ]);

        $data = $request->all();
        if ($request->filled('division_id')) {
            $division = Division::find($request->division_id);
            if ($division) {
                $data['section_id'] = $division->section_id;
            }
        }

        Department::create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Department created successfully.']);
        }

        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'department_name' => 'nullable|string|max:255',
            'department_fullname' => 'nullable|string|max:255',
            'department_status' => 'required|integer|in:0,1',
            'division_id' => 'nullable|integer|exists:divisions,division_id',
        ]);

        $data = $request->all();
        if ($request->filled('division_id')) {
            $division = Division::find($request->division_id);
            if ($division) {
                $data['section_id'] = $division->section_id;
            }
        }

        $department = Department::findOrFail($id);
        $department->update($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Department updated successfully.']);
        }

        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Department deleted successfully.']);
        }

        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }
}
