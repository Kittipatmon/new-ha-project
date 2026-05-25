<?php

namespace App\Http\Controllers\backend\users;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Section;

class SectionController extends Controller
{
    public function apiSections()
    {
        $sections = Section::all();
        return response()->json($sections);
    }

    public function index()
    {
        $sections = Section::all();
        return view('backend.section.index', compact('sections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'section_code' => 'required|string|max:255|unique:sections',
            'section_name' => 'required|string|max:255',
            'section_status' => 'required|integer',
        ]);

        $section = Section::create($request->all());

        return response()->json(['success' => true, 'section' => $section]);
    }

    public function update(Request $request, Section $section)
    {
        $request->validate([
            'section_code' => 'required|string|max:255|unique:sections,section_code,' . $section->section_id . ',section_id',
            'section_name' => 'required|string|max:255',
            'section_status' => 'required|integer',
        ]);

        $section->update($request->all());

        return response()->json(['success' => true, 'section' => $section]);
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return redirect()->route('sections.index')->with('success', 'ลบข้อมูลสายงานเรียบร้อยแล้ว');
    }
}
