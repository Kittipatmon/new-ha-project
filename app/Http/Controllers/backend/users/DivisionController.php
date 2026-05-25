<?php

namespace App\Http\Controllers\backend\users;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Division;
use App\Models\Section;

class DivisionController extends Controller
{
    public function apiDivisions()
    {
        $divisions = Division::all();
        return response()->json($divisions);
    }

    public function index()
    {
        // Eager load section เพื่อลด query
        $divisions = Division::with('section')->get();
        $sections = Section::all();

        return view('backend.division.index', compact('divisions', 'sections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // ตรวจสอบว่า section_id มีอยู่จริงในตาราง sections (สมมติว่า PK ชื่อ section_id หรือ id)
            'section_id' => 'required|integer|exists:sections,section_id', 
            'division_name' => 'required|string|max:255',
            'division_fullname' => 'nullable|string|max:255',
            'division_status' => 'required|integer|in:0,1', // บังคับค่า 0 หรือ 1
        ]);

        $division = Division::create($request->all());

        return response()->json(['success' => true, 'division' => $division]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'section_id' => 'required|integer|exists:sections,section_id',
            'division_name' => 'required|string|max:255',
            'division_fullname' => 'nullable|string|max:255',
            'division_status' => 'required|integer|in:0,1',
        ]);

        $division = Division::findOrFail($id);
        $division->update($request->all());

        return response()->json(['success' => true, 'division' => $division]);
    }

    public function destroy($id)
    {
        $division = Division::findOrFail($id);
        $division->delete();
        return response()->json(['success' => true]);
    }
}