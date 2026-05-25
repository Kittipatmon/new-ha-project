<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;400;600&family=Prompt:wght@200;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Prompt', 'Kanit', sans-serif;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.46.0/dist/apexcharts.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>
    
<div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-2 rounded-2xl shadow-xl overflow-hidden bg-white/10">

    <div class="relative hidden lg:block">
        <!-- <img
            src="{{ asset('images/ict/ICT.jpg') }}"
            alt="Hero"
            class="absolute inset-0 h-full w-full object-cover"
        /> -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>

        {{-- Top-left brand --}}
        <div class="absolute top-6 left-6 flex items-center gap-3 text-orange-700">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/10 ring-1 ring-white/20">
                {{-- simple roof icon --}}
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 3 2 12h2v8h6v-5h4v5h6v-8h2z" />
                </svg>
            </span>
            <span class="text-lg font-semibold tracking-wide">
                ICT Kumwell
            </span>
        </div>

        {{-- Bottom-left caption --}}
        <div class="absolute bottom-10 left-8 right-8 text-white">
            <h2 class="text-3xl xl:text-xl font-semibold">Find Your Sweet Home</h2>
            <p class="mt-3 max-w-xl text-white/90 text-sm">
                Visiting your dream property is now just a few clicks away — fast, easy, reliable.
            </p>

            {{-- dots --}}
            <div class="mt-6 flex gap-2">
                <span class="h-1.5 w-4 rounded-full bg-white"></span>
                <span class="h-1.5 w-1.5 rounded-full bg-white/60"></span>
                <span class="h-1.5 w-1.5 rounded-full bg-white/40"></span>
            </div>
        </div>
    </div>

    {{-- Right: Login card --}}
    <div class="flex items-center justify-center p-6 lg:p-10">
        <div class="w-full max-w-md">

            <div class="text-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
                <h1 class="text-2xl font-semibold">
                    ICT Kumwell
                </h1>
                <p class="mt-2 text-sm text-gray-500">เข้าสู่ระบบบัญชีของคุณ</p>

                <form method="POST" action="/login" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="employee_code" class="block text-sm font-medium text-white">
                            รหัสพนักงาน
                        </label>
                        <select id="employee_code" name="employee_code" required
                            class="select select-bordered py-6 mt-2 block w-full text-black rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 selectemp2">
                            <option value="" disabled selected>-- เลือกข้อมูล --</option>
                            @foreach($employees as $code => $fullName)
                            <option value="{{ $code }}">{{ $fullName }} ({{ $code }})</option>
                            @endforeach
                        </select>
                        <!-- <input
                            id="employee_code"
                            name="employee_code"
                            type="text"
                            autocomplete="username"
                            required
                            class="mt-2 block w-full text-black rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="you@example.com"
                        /> -->
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm font-medium text-white">รหัสผ่าน</label>
                        </div>
                        <div class="mt-2 relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                required
                                class="block w-full text-black rounded-lg border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="กรอกรหัสผ่าน" />
                            <span class="pointer-events-none absolute inset-y-0 right-3 inline-flex items-center text-gray-400">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5C21.27 7.61 17 4.5 12 4.5zm0 12a4.5 4.5 0 110-9 4.5 4.5 0 010 9z" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    {{-- Remember + Forgot --}}
                    <!-- <div class="flex items-center justify-between">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                            <input id="remember_me" name="remember" type="checkbox"
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            Remember Me
                        </label>
                        <a href="/password/reset" class="text-sm text-gray-600 hover:text-gray-900">
                            Forgot Password?
                        </a>
                    </div> -->

                    {{-- Login button --}}
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-orange-700 px-4 py-2 font-medium text-white hover:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                        Login
                    </button>

                    {{-- Divider: Instant Login --}}
                    <div class="relative my-2">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="bg-white px-3 text-xs text-gray-500 rounded-full">Instant Login</span>
                        </div>
                    </div>

                    {{-- Register --}}
                    <p class="mt-6 text-center text-sm text-gray-600">
                        Don’t have any account?
                        <a href="#" class="text-orange-600 hover:text-orange-700 font-medium" onclick="Swal.fire('กรุณาติดต่อ ICT!', 'เพื่อดำเนินการสร้าง User เพื่อเข้าใช้งาน.', 'warning')">Register</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function () {
            function onReady(fn){
                if (document.readyState === 'complete' || document.readyState === 'interactive') {
                    queueMicrotask(fn);
                } else {
                    document.addEventListener('DOMContentLoaded', fn);
                }
            }
            onReady(function(){
                if (window.$ && $.fn && $.fn.select2) {
                    $('.selectemp2').select2({
                        placeholder: "-- Select Employee Code --",
                        allowClear: true,
                        width: '100%'
                    });
                }
            });
        })();
    </script>
@endpush