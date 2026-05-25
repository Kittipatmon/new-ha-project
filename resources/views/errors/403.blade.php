@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
    <div class="mb-6">
        <div class="w-24 h-24 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-lock text-5xl text-red-600"></i>
        </div>
    </div>

    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
        ไม่มีสิทธิ์เข้าถึง
    </h1>
    
    <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
        หน้านี้สงวนสิทธิ์เฉพาะเจ้าหน้าที่ HR เท่านั้น
    </p>

    <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-red-900/20 active:scale-95">
        <i class="fa-solid fa-home"></i>
        กลับหน้าหลัก
    </a>
</div>
@endsection
