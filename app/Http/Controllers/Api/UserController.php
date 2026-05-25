<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    // GET /api/users
    public function index(Request $request)
    {
        $q = User::query();

        // Default: active users only (status = '0') unless status param provided
        if ($request->has('status')) {
            $q->where('status', $request->input('status'));
        } else {
            $q->where('status', '0');
        }

        if ($s = trim((string) $request->input('q', ''))) {
            $q->where(function ($w) use ($s) {
                $w->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name', 'like', "%$s%")
                  ->orWhere('employee_code', 'like', "%$s%");
            });
        }

        foreach (['department_id','division_id','section_id','level_user','employee_type','workplace'] as $f) {
            if (($v = $request->input($f)) !== null && $v !== '') {
                $q->where($f, $v);
            }
        }

        // Eager-load relations if requested: ?include=department,division,section
        $includes = array_filter(explode(',', (string) $request->input('include', '')));
        $allowed = ['department','division','section'];
        $load = array_values(array_intersect($includes, $allowed));
        if ($load) {
            $q->with($load);
        }

        // Order and pagination
        $q->orderBy('first_name')->orderBy('last_name');
        $perPage = (int) $request->input('per_page', 25);
        if ($perPage <= 0) { $perPage = 25; }
        $perPage = min($perPage, 100);

        // If paginate=false, return a simple collection (limit param applies)
        if ($request->boolean('paginate', true) === false) {
            $limit = (int) $request->input('limit', 100);
            if ($limit > 0) { $q->limit(min($limit, 500)); }
            $collection = $q->get();
            return UserResource::collection($collection);
        }

        $paginator = $q->paginate($perPage)->appends($request->query());
        return UserResource::collection($paginator);
    }

    // GET /api/users/{id}
    public function show(Request $request, $id)
    {
        $q = User::query();
        $includes = array_filter(explode(',', (string) $request->input('include', '')));
        $allowed = ['department','division','section'];
        $load = array_values(array_intersect($includes, $allowed));
        if ($load) { $q->with($load); }

        $user = $q->findOrFail($id);
        return new UserResource($user);
    }
}
