@extends('layouts.app')

@section('content')

<style>
    html {
        scroll-behavior: smooth;
    }
</style>

<div class="max-w-8xl mx-auto">
    <div class="card-body ">
        <div class="flex justify-between">
            <div class="flex items-center">
                <i class="fas fa-user-circle fa-2x mr-2"></i>
                <span class="text-lg font-semibold">Welcome, {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</span>
            </div>
            <div>
                <a href="{{ route('logout') }}" class="btn btn-danger">Logout</a>
            </div>
        </div>
        
    </div>
</div>

@endsection