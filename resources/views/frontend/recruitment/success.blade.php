@extends('layouts.recruitment.app')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900 flex items-center justify-center px-6 py-20">
        <div class="max-w-md w-full text-center space-y-8">
            <div class="relative">
                <div
                    class="w-24 h-24 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto text-green-500 text-4xl shadow-xl shadow-green-500/10">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div
                    class="absolute -top-2 -right-2 w-8 h-8 bg-white dark:bg-slate-800 rounded-full shadow-lg flex items-center justify-center text-kumwell-red animate-bounce">
                    <i class="fa-solid fa-heart text-xs"></i>
                </div>
            </div>

            <div class="space-y-4">
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">ส่งใบสมัครเรียบร้อยแล้ว!</h1>
                <p class="text-gray-500 dark:text-gray-400 leading-relaxed">
                    ขอบคุณที่สนใจร่วมงานกับ Kumwell<br>
                    เราได้รับข้อมูลของคุณสำหรับตำแหน่ง <span
                        class="text-kumwell-red font-bold">{{ $post->position_name }}</span>
                    แล้ว เจ้าหน้าที่จะพิจารณาและติดต่อกลับโดยเร็วที่สุด
                </p>
            </div>

            <div class="pt-8 space-y-3">
                <a href="{{ route('recruitment.index') }}"
                    class="block w-full bg-kumwell-red hover:bg-red-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-red-500/30 transition-all active:scale-95">
                    กลับไปดูตำแหน่งงานอื่น
                </a>
                <a href="{{ route('welcome') }}"
                    class="block w-full text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 font-medium py-2 transition-colors">
                    กลับหน้าหลักเว็บไซต์
                </a>
            </div>

            <div
                class="mt-12 p-6 bg-blue-50 dark:bg-blue-900/10 rounded-2xl border border-blue-100 dark:border-blue-800 text-left">
                <h4
                    class="text-xs font-bold text-blue-800 dark:text-blue-300 uppercase tracking-widest mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info"></i>
                    ขั้นตอนถัดไป
                </h4>
                <ul class="text-xs text-blue-700 dark:text-blue-400 space-y-2">
                    <li class="flex gap-2"><span>1.</span> เจ้าหน้าที่ตรวจสอบคุณสมบัติเบื้องต้น (1-3 วันทำการ)</li>
                    <li class="flex gap-2"><span>2.</span> หากผ่านการคัดเลือก จะมีการโทรนัดสัมภาษณ์</li>
                    <li class="flex gap-2"><span>3.</span> คุณสามารถเข้ามาเช็คสถานะได้ในภายหลังด้วย Email/Phone</li>
                </ul>
            </div>
        </div>
    </div>
@endsection