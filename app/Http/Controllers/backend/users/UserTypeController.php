<?php

namespace App\Http\Controllers\backend\users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserType;

class UserTypeController extends Controller
{
    public function index()
    {
        $userTypes = UserType::all();
        return view('backend.usertypes.index', compact('userTypes'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'type_name' => 'required|string|max:255',
        ]);

        $data = [
            'type_name' => $request->type_name,
            'description' => $request->description,
            'status' => $request->status ?? 0,
        ];

        if (empty($data['type_code'])) {
            $data['type_code'] = 'UT-' . time() . rand(100, 999);
        }

        UserType::create($data);

        return redirect()->route('usertypes.index')->with('success', 'User type created successfully.');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'type_name' => 'required|string|max:255',
        ]);

        $userType = UserType::findOrFail($id);
        $userType->update([
            'type_name' => $request->type_name,
            'description' => $request->description,
            'status' => $request->status ?? 0,
        ]);

        return redirect()->route('usertypes.index')->with('success', 'User type updated successfully.');
    }

    public function destroy($id)
    {
        $userType = UserType::findOrFail($id);
        $userType->delete();

        return redirect()->route('usertypes.index')->with('success', 'User type deleted successfully.');
    }



}
