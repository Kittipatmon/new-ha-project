<?php

namespace App\Http\Controllers\backend\hrrequest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\hrrequest\RequestType;
use App\Models\hrrequest\RequestCategories;

class RequestTypeController extends Controller
{
    public function index()
    {
        $requesttypes = RequestType::all();
        $requestcategories = RequestCategories::all();
        return view('backend.request_type.index', compact('requesttypes', 'requestcategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|integer|exists:request_categories,id',
            'code' => 'nullable|string|max:255',
            'name_th' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data = $request->all();
        if (empty($data['code'])) {
            $data['code'] = 'REQ-' . time() . rand(100, 999);
        }

        RequestType::create($data);

        return redirect()->route('request-types.index')->with('success', 'เพิ่มข้อมูลเรียบร้อยแล้ว.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'nullable|integer|exists:request_categories,id',
            'code' => 'nullable|string|max:255',
            'name_th' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $requesttype = RequestType::findOrFail($id);
        $requesttype->update($request->all());

        return redirect()->route('request-types.index')->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว.');
    }

    public function destroy($id)
    {
        $requesttype = RequestType::findOrFail($id);
        $requesttype->delete();

        return redirect()->route('request-types.index')->with('success', 'ลบข้อมูลเรียบร้อยแล้ว.');
    }
}
