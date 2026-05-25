<?php

namespace App\Http\Controllers\hrrequest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\hrrequest\RequestType;
use App\Models\hrrequest\RequestSubtypes;

class RequestDataController extends Controller
{
    /**
     * Return active request types filtered by category_id
     */
    public function types(Request $request)
    {
        $categoryId = $request->query('category_id');
        if (!$categoryId) {
            return response()->json([], 200);
        }

        $types = RequestType::query()
            ->where('category_id', $categoryId)
            ->where('is_active', 1)
            ->orderBy('name_th')
            ->get(['id','code','name_th','name_en']);

        return response()->json($types, 200);
    }

    /**
     * Return active subtypes filtered by type_id
     */
    public function subtypes(Request $request)
    {
        $typeId = $request->query('type_id');
        if (!$typeId) {
            return response()->json([], 200);
        }

        $subtypes = RequestSubtypes::query()
            ->where('type_id', $typeId)
            ->where('is_active', 1)
            ->orderBy('name_th')
            ->get(['id','code','name_th','name_en']);

        return response()->json($subtypes, 200);
    }
}
