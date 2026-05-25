<?php

namespace App\Http\Controllers\backend;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Models\User;

class UserController extends Controller
{
    public function profileUser(){
        // Get the currently authenticated user as a single model instance
        $user = Auth::user();

        // If not authenticated, redirect to login (or handle as you prefer)
        if (!$user) {
            return redirect()->route('login');
        }

        // Pass a single $user to the view
        return view('backend.users.profile', compact('user'));
    }
}
