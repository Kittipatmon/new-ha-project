<?php

namespace App\Http\Controllers\Backend\RequestData;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Support\Facades\Cache;

class SectionController extends Controller
{
    public function apiSection()
    {
        $sections = Cache::remember('all_sections', 3600, fn() => Section::all());
        return response()->json($sections);
    }

    public function index()
    {
        $sections = Cache::remember('all_sections', 3600, fn() => Section::all());
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
