<?php

namespace App\Http\Controllers\backend\hrrequest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\hrrequest\RequestCategories;

class RequestCategoriesController extends Controller
{
    public function index()
    {
        $categories = RequestCategories::all();
        return view('backend.request_categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'nullable|string|max:255',
            'name_th' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        RequestCategories::create($request->all());

        return redirect()->route('request-categories.index')->with('success', 'เพิ่มข้อมูลเรียบร้อยแล้ว.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'nullable|string|max:255',
            'name_th' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $category = RequestCategories::findOrFail($id);
        $category->update($request->all());

        return redirect()->route('request-categories.index')->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว.');
    }

    public function destroy($id)
    {
        $category = RequestCategories::findOrFail($id);
        $category->delete();

        return redirect()->route('request-categories.index')->with('success', 'ลบข้อมูลเรียบร้อยแล้ว.');
    }
}
