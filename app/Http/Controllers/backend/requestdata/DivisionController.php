<?php

namespace App\Http\Controllers\backend\requestdata;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Division;

class DivisionController extends Controller
{
    public function apiDivision()
    {
        return response()->json(Division::all());
    }

    public function index()
    {
        try {
            $divisions = Division::all();
            return view('backend.division.index', compact('divisions'));
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching data.'], 500);
        }
    }
}
