<?php

namespace App\Http\Controllers\Backend\RequestData;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Division;
use App\Models\Section;
use App\Models\UserType;
use App\Models\hrrequest\RequestCategories;
use App\Models\hrrequest\RequestType;
use App\Models\hrrequest\RequestSubtypes;
use App\Models\datacenter\News;

class RequestDataController extends Controller
{
    public function welcomeData()
    {
        $counts = [
            'users' => User::count(),
            'departments' => Department::count(),
            'divisions' => Division::count(),
            'sections' => Section::count(),
            'usertypes' => UserType::count(),
            'categories' => RequestCategories::count(),
            'types' => RequestType::count(),
            'subtypes' => RequestSubtypes::count(),
            'news' => News::count(),
        ];

        return view('backend.welcomedata', compact('counts'));
    }
}
