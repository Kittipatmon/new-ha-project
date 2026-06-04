<?php

namespace App\Http\Controllers\Backend\RequestData;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Support\Facades\Cache;

class DivisionController extends Controller
{
    public function apiDivision()
    {
        $divisions = Cache::remember('all_divisions', 3600, fn() => Division::all());
        return response()->json($divisions);
    }

    public function index()
    {
        try {
            $divisions = Cache::remember('all_divisions', 3600, fn() => Division::all());
            return view('backend.division.index', compact('divisions'));
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching data.'], 500);
        }
    }
}
