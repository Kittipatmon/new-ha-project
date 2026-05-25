<?php

namespace App\Http\Controllers\backend\requestdata;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Section;

class SectionController extends Controller
{
    public function apiSection()
    {
        return response()->json(Section::all());
    }

    public function index()
    {
        $sections = Section::all();
        return view('backend.section.index', compact('sections'));
    }

    public function create()
    {
        return view('backend.section.create');
    }

       public function store(Request $request)
    {
        $validated = $request->validate([
            'section_code' => 'required|string|max:255',
            'section_name' => 'required|string|max:255',
            'section_fullname' => 'required|string|max:255',
            'section_status' => 'required|integer',
            'section_description' => 'nullable|string',
        ]);
        Section::create($validated);
        return redirect()->route('sections.index')->with('success', 'Section created successfully.');
    }

    public function edit($id)
    {
        $section = Section::findOrFail($id);
        return view('backend.section.edit', compact('section'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'section_code' => 'required|string|max:255',
            'section_name' => 'required|string|max:255',
            'section_fullname' => 'required|string|max:255',
            'section_status' => 'required|integer',
            'section_description' => 'nullable|string',
        ]);
        $section = Section::findOrFail($id);
        $section->update($validated);
        return redirect()->route('sections.index')->with('success', 'Section updated successfully.');
    }

    public function destroy($id)
    {
        $section = Section::findOrFail($id);
        $section->delete();
        return redirect()->route('sections.index')->with('success', 'Section deleted successfully.');
    }


}
