<?php

namespace App\Http\Controllers\backend\hrrequest;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\hrrequest\RequestSubtypes;
use App\Models\hrrequest\RequestType;

class RequestSubtypeController extends Controller
{
    public function index()
    {
        $requestsubtypes = RequestSubtypes::all();
        $requesttypes = RequestType::all();
        return view('backend.request_subtype.index', compact('requestsubtypes', 'requesttypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type_id' => 'nullable|integer|exists:request_type,id',
            'code' => 'nullable|string|max:255',
            'name_th' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data = $request->all();
        if (empty($data['code'])) {
            $data['code'] = 'SUB-' . time() . rand(100, 999);
        }

        RequestSubtypes::create($data);

        return redirect()->route('request-subtypes.index')->with('success', 'เพิ่มข้อมูลเรียบร้อยแล้ว.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type_id' => 'nullable|integer|exists:request_type,id',
            'code' => 'nullable|string|max:255',
            'name_th' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $requestsubtype = RequestSubtypes::findOrFail($id);
        $requestsubtype->update($request->all());

        return redirect()->route('request-subtypes.index')->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว.');
    }

    public function destroy($id)
    {
        $requestsubtype = RequestSubtypes::findOrFail($id);
        $requestsubtype->delete();

        return redirect()->route('request-subtypes.index')->with('success', 'ลบข้อมูลเรียบร้อยแล้ว.');
    }

}
