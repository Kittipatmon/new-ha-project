@php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>HAMS Kumwell</title>
    <link rel="icon" href="{{ asset('images/kml-logo1.png') }}" type="image/png">
    <!-- Fonts -->
    <div id="app">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <!-- @notifyCss -->
</head>

<body>
    <!-- background: linear-gradient(90deg,rgb(46, 47, 48) 0%, rgb(53, 53, 53) 100%);" -->
    <div id="app">
        <nav class="sticky top-0 z-50 shadow-lg  " style="background: #dedede; ">
            <!-- <nav class="sticky top-0 z-50 bg-white shadow-lg " style="background: #dedede;"> -->
            <div class="flex justify-between max-w-8xl mx-auto px-2 sm:px-6">


                <a class="flex items-center" href="{{ url('/') }}">
                    <!-- <img src="{{ asset('images/kml-logo1.png') }}" alt=""
                        class="navbar-logo w-24 h-12 sm:w-28 sm:h-14 lg:w-32 lg:h-16 mr-2 sm:mr-4 lg:mr-6 rounded-xl bg-white p-1"> -->
                    <span class="text-red-600 font-bold text-lg sm:text-xl lg:text-3xl ml-2">Kumwell</span>
                </a>
                <div class="flex justify-end flex-1 h-20 items-center ">
                    <ul class="hidden lg:flex space-x-1 items-center">
                        <li>
                            <a class="navbar-link px-3 sm:px-4 lg:px-5 py-2 text-black rounded-xl shadow transition text-sm lg:text-base"
                                href="{{ route('dashboard') }}">หน้าหลัก</a>
                        </li>
                    </ul>

                    <ul class="hidden lg:flex space-x-1 items-center">
                        <li>
                            <a class="navbar-link px-3 sm:px-4 lg:px-5 py-2 text-black rounded-xl shadow transition text-sm lg:text-base"
                                href="{{ route('dashboard') }}">บริการ</a>
                        </li>
                        <li>
                            <a class="navbar-link px-3 sm:px-4 lg:px-5 py-2 text-black rounded-xl shadow transition text-sm lg:text-base"
                                href="{{ route('dashboard') }}">HA Guide Book</a>
                        </li>
                        <li>
                            <a class="navbar-link px-3 sm:px-4 lg:px-5 py-2 text-black rounded-xl shadow transition text-sm lg:text-base"
                                href="{{ route('dashboard') }}">Calendar</a>
                        </li>
                        <li class="relative">
                            <button type="button"
                                class="navbar-link px-3 py-2 text-black rounded-xl shadow flex items-center transition dropdown-toggle">
                                เกี่ยวกับเรา
                                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <ul
                                class="dropdown-menu absolute left-0 top-full mt-2 w-60 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 opacity-0 invisible transition-opacity duration-200">
                                <li><a class="block px-4 py-2 text-red-700 hover:bg-red-100 rounded-lg"
                                        href="#">
                                        <i class="fa-solid fa-newspaper mr-1"></i>
                                        นโยบาย/การดำเนินงาน</a>
                                </li>
                                <li><a class="block px-4 py-2 text-red-700 hover:bg-red-100 rounded-lg"
                                        href="#"><i
                                            class="fa fa-phone mr-2"></i>เบอร์ติดต่อภายใน</a></li>
                                <li><a class="block px-4 py-2 text-red-700 hover:bg-red-100 rounded-b-lg"
                                        href="#"><i
                                            class="fa fa-comment mr-2"></i>แสดงความคิดเห็น</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="navbar-link px-3 sm:px-4 lg:px-5 py-2 text-black rounded-xl shadow transition text-sm lg:text-base" href="#" onclick="Swal.fire({icon: 'info', title: 'อยู่ระหว่างพัฒนา', text: 'ฟีเจอร์นี้กำลังอยู่ระหว่างการพัฒนา', confirmButtonText: 'ตกลง'}); return false;">คู่มือการใช้</a>
                        </li>
                    </ul>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->firstname }}
                                    {{ Auth::user()->lastname }}
                                </div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                    <!-- Mobile menu button -->
                    <div class="lg:hidden flex items-center">
                        <button id="mobile-menu-button"
                            class="text-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 p-2">
                            <i class="fa fa-bars text-xl sm:text-2xl"></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Mobile menu -->
            <div id="mobile-menu" class="lg:hidden hidden bg-white border-t border-gray-200 shadow-2xl rounded-b-2xl">
                <ul class="flex flex-col p-3 sm:p-4 space-y-2">
                    <li>
                        <a class="block px-3 sm:px-5 py-2 text-black rounded-xl shadow text-sm sm:text-base"
                            href="{{ url('/') }}">หน้าหลัก</a>
                    </li>
                    <li>
                        <details class="group">
                            <summary
                                class="px-3 sm:px-5 py-2 text-black rounded-xl shadow cursor-pointer list-none text-sm sm:text-base flex items-center justify-between">
                                ติดต่อเรา
                            </summary>
                            <ul class="pl-4 mt-2 space-y-2">
                                <li>
                                    <a class="block px-2 sm:px-3 py-1 sm:py-2 text-red-700 hover:bg-red-100 rounded-lg text-xs sm:text-sm leading-tight"
                                        href="#">
                                        <i class="fa fa-phone mr-2"></i>เบอร์ติดต่อภายใน
                                    </a>
                                </li>
                                <li>
                                    <a class="block px-2 sm:px-3 py-1 sm:py-2 text-red-700 hover:bg-red-100 rounded-lg text-xs sm:text-sm leading-tight"
                                        href="#">
                                        <i class="fa fa-comment mr-2"></i>แสดงความคิดเห็น
                                    </a>
                                </li>
                            </ul>
                        </details>
                    </li>
                    @guest
                    @if (Route::has('login'))
                    <li>
                        <a href="{{ route('login') }}"
                            class="block px-5 py-2 text-black rounded-xl shadow hover:bg-red-100 transition">
                            <i class="fa fa-sign-in-alt mr-2"></i>{{ __('Login') }}
                        </a>
                    </li>
                    @endif
                    @else
                    <li>
                        <details class="group">
                            <summary
                                class="px-5 py-2 text-black rounded-xl shadow cursor-pointer hover:bg-red-100 transition">
                                <i class="fa fa-user-circle mr-2"></i>
                            </summary>
                            <ul class="pl-4 mt-2 space-y-2">
                                <li class="px-5 py-2 bg-gray-50 rounded-xl">
                                    <div class="font-bold mb-1 text-sm">
                                        <i class="fa fa-user-circle mr-2"></i>{{ Auth::user()->name }}
                                        <span class="ml-2 text-xs text-gray-500 font-normal">
                                            {{ Auth::user()->user_role == 0 ? '(Admin)' : (Auth::user()->user_role == 1 ? '(ผู้อนุมัติ)' : (Auth::user()->user_role == 4 ? '(ฝ่ายจัดเตรียม)' : '(User)')) }}
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        <i class="fa fa-envelope mr-1"></i>{{ Auth::user()->email }}
                                    </div>
                                </li>
                                <li>
                                    <a class="block px-5 py-2 text-red-700 hover:bg-red-100 rounded-xl transition"
                                        href="#">
                                        <i class="fa fa-user mr-2"></i>ข้อมูลส่วนตัว
                                    </a>
                                </li>
                                <li>
                                    <a class="block px-5 py-2 text-red-700 hover:bg-red-100 rounded-xl transition"
                                        href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fa fa-sign-out-alt mr-2"></i>ออกจากระบบ
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </details>
                    </li>
                    @endguest
                </ul>
            </div>
        </nav>

        @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            });
        </script>
        @endif
        @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            });
        </script>
        @endif
        <main>
            @yield('content')
        </main>

    </div>

    <footer class="shadow-lg">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-8 text-center md:text-left">
                <!-- Left: Logo & Description -->
                <div class="md:w-1/3 mb-6 md:mb-0 px-4">
                    <div class="flex items-center mb-2">
                        <span class="text-3xl font-bold"
                            style="background: linear-gradient(90deg, rgb(220, 38, 38) 0%, rgb(204, 11, 11) 100%); -webkit-background-clip: text; color: transparent; background-clip: text;">
                            Kumwell
                        </span>
                        <span class="text-1xs font-bold text-black mt-2">&nbsp;HR SYSTEM</span>
                    </div>
                    <p class="text-sm footer-desc leading-relaxed mb-6">
                        <a href="#" class="footer-link hover:text-red-400" target="_blank"><i
                                class="fa-solid fa-globe me-2"></i>HR SYSTEM</a>
                    </p>
                    <div class="flex space-x-4 mt-4">
                        <a href="https://www.facebook.com/KumwellOfficial" target="_blank"><i
                                class="fab fa-facebook fa-lg text-gray-700"></i></a>
                        <a href="https://www.instagram.com/kumwell" target="_blank"><i
                                class="fab fa-instagram fa-lg text-gray-700"></i></a>
                    </div>
                </div>
                <!-- Center: Quick Links & Support -->
                <div class="flex-1 grid grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold text-black mb-2">Quick Links</h3>
                        <hr class="footer-divider mb-2 px-2 w-12" style="margin-top: -7px;">
                        <ul class="space-y-1 text-gray-800 text-sm">
                            <li><a href="{{ route('dashboard') }}" class="footer-link">หน้าแรก</a></li>
                            <li><a href="#" class="footer-link">ข่าวสาร/ประชาสัมพันธ์</a></li>
                            <li><a href="#" class="footer-link">งานสนับสนุน</a></li>
                            <li><a href="#"
                                    class="footer-link">แสดงความคิดเห็น</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-black mb-2">Support</h3>
                        <ul class="space-y-1 text-gray-800 text-sm">
                            <li><a href="#" class="footer-link">ติดต่อเรา</a></li>
                            <li>
                                <a href="#" class="footer-link" onclick="Swal.fire({
                                    icon: 'info',
                                    title: 'อยู่ระหว่างพัฒนา',
                                    text: 'ฟีเจอร์นี้กำลังอยู่ระหว่างการพัฒนา',
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK'
                                }); return false;">ข้อเสนอแนะ</a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
            <hr class="footer-divider my-6">
            <div class="mt-4">
                <p class="footer-copyright text-sm">© 2025 Kumwell corporation plc.</p>
                <p class="footer-copyright text-xs">Developed by : <a href="#" target="_blank" class="footer-link">ICT
                        Kumwell <i class="fa fa-phone"></i> Tel: 3544</a></p>
            </div>
        </div>
    </footer>
    <!-- Kumwell Corporation Public Company Limited
Contact Center Tel : 02-954-3455 ต่อ 3522 E-Mail : it@kumwell.com
Created by IT Kumwell V.2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    @stack('scripts')
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('mobile-menu-button');
            const menu = document.getElementById('mobile-menu');
            btn.addEventListener('click', function() {
                menu.classList.toggle('hidden');
            });

            // Dropdown toggle for desktop (click)
            document.querySelectorAll('.dropdown-toggle').forEach(function(toggleBtn) {
                toggleBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    // Close other dropdowns
                    document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                        if (!toggleBtn.nextElementSibling.isSameNode(menu)) {
                            menu.classList.add('opacity-0', 'invisible');
                            menu.classList.remove('opacity-100', 'visible');
                        }
                    });
                    // Toggle current dropdown
                    const dropdown = toggleBtn.nextElementSibling;
                    if (dropdown.classList.contains('opacity-0')) {
                        dropdown.classList.remove('opacity-0', 'invisible');
                        dropdown.classList.add('opacity-100', 'visible');
                    } else {
                        dropdown.classList.add('opacity-0', 'invisible');
                        dropdown.classList.remove('opacity-100', 'visible');
                    }
                });
            });
            // Show dropdown instantly on mouseenter, hide on mouseleave
            document.querySelectorAll('.relative').forEach(function(li) {
                const btn = li.querySelector('.dropdown-toggle');
                const menu = li.querySelector('.dropdown-menu');
                let hideTimeout;
                if (btn && menu) {
                    li.addEventListener('mouseenter', function() {
                        clearTimeout(hideTimeout);
                        menu.classList.remove('opacity-0', 'invisible');
                        menu.classList.add('opacity-100', 'visible');
                    });
                    li.addEventListener('mouseleave', function() {
                        hideTimeout = setTimeout(function() {
                            menu.classList.add('opacity-0', 'invisible');
                            menu.classList.remove('opacity-100', 'visible');
                        }, 100);
                    });
                    menu.addEventListener('mouseenter', function() {
                        clearTimeout(hideTimeout);
                    });
                    menu.addEventListener('mouseleave', function() {
                        hideTimeout = setTimeout(function() {
                            menu.classList.add('opacity-0', 'invisible');
                            menu.classList.remove('opacity-100', 'visible');
                        }, 100);
                    });
                }
            });
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                    menu.classList.add('opacity-0', 'invisible');
                    menu.classList.remove('opacity-100', 'visible');
                });
            });

            let lastScrollTop = 0;
            const navbar = document.querySelector('nav');
            let ticking = false;
            window.addEventListener('scroll', function() {
                if (!navbar) return;
                let st = window.pageYOffset || document.documentElement.scrollTop;
                if (!ticking) {
                    window.requestAnimationFrame(function() {
                        if (st > lastScrollTop && st > 50) {
                            navbar.style.transform = 'translateY(-100%)';
                        } else {
                            navbar.style.transform = 'translateY(0)';
                        }
                        lastScrollTop = st <= 0 ? 0 : st;
                        ticking = false;
                    });
                    ticking = true;
                }
            });
            if (navbar) {
                navbar.style.transition = 'transform 0.3s cubic-bezier(0.4,0,0.2,1)';
            }
        });
    </script>

    <!-- @notifyJs -->
</body>

</html>