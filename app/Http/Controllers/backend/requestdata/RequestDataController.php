<?php

namespace App\Http\Controllers\backend\requestdata;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RequestDataController extends Controller
{
    public function welcomeData()
    {
        return view('backend.welcomedata');
    }
}
