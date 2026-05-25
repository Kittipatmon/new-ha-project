<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;

class SystemController extends Controller
{
    public function welcomeSystem()
    {
        $breadcrumbs = [
            ['label' => 'หน้าหลัก', 'url' => route('dashboard')],
            ['label' => 'งานบริการ', 'url' => null],
        ];
        return view('hrsystem.welcomesystem', compact('breadcrumbs'));
    }
}