@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[70vh] text-center px-4 py-12">
    <!-- Icon Alert Animation -->
    <div class="mb-6 relative inline-block">
        <div class="w-28 h-28 bg-red-100 dark:bg-red-950/60 rounded-3xl flex items-center justify-center mx-auto border-4 border-red-200 dark:border-red-800/80 shadow-xl shadow-red-500/10">
            <i class="fa-solid fa-triangle-exclamation text-5xl text-red-600 dark:text-red-400 animate-pulse"></i>
        </div>
        <div class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-black px-2.5 py-0.5 rounded-full shadow">
            403
        </div>
    </div>

    <!-- Alert Badge -->
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-red-100 dark:bg-red-950/80 text-red-700 dark:text-red-300 mb-3 border border-red-200 dark:border-red-800">
        <i class="fa-solid fa-ban"></i>
        <span>ไม่มีสิทธิ์เข้าถึง (Access Denied)</span>
    </div>

    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white mb-3 tracking-tight">
        ขออภัย คุณไม่มีสิทธิ์เข้าใช้งานหน้านี้
    </h1>
    
    <!-- Detail Message -->
    <div class="bg-white dark:bg-gray-800/90 border border-gray-200 dark:border-gray-700/80 rounded-2xl p-5 mb-8 max-w-lg mx-auto shadow-sm">
        <p class="text-sm sm:text-base text-gray-700 dark:text-gray-300 leading-relaxed">
            <i class="fa-solid fa-circle-exclamation text-amber-500 mr-1.5"></i>
            {{ $exception->getMessage() ?: 'หน้านี้สงวนสิทธิ์เฉพาะผู้ดูแลระบบและผู้ที่มีสิทธิ์การใช้งานที่ได้รับอนุญาตเท่านั้น' }}
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
            หากคุณต้องการใช้งานส่วนนี้ กรุณาติดต่อผู้ดูแลระบบสังกัดฝ่าย ICT เพื่อขอรับการกำหนดสิทธิ์
        </p>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap items-center justify-center gap-3">
        <a href="javascript:history.back()" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold text-sm rounded-xl transition-all shadow-md active:scale-95">
            <i class="fa-solid fa-arrow-left"></i>
            <span>ย้อนกลับหน้าก่อนหน้า</span>
        </a>

        <a href="{{ route('welcome') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-600 to-amber-600 hover:from-red-700 hover:to-amber-700 text-white font-semibold text-sm rounded-xl transition-all shadow-lg shadow-red-600/20 active:scale-95">
            <i class="fa-solid fa-house"></i>
            <span>กลับหน้าหลัก</span>
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'ไม่มีสิทธิ์เข้าถึง',
                text: @json($exception->getMessage() ?: 'ขออภัย คุณไม่มีสิทธิ์เข้าใช้งานหน้านี้ (Access Denied)'),
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'รับทราบ',
                timer: 4500
            });
        }
    });
</script>
@endsection
