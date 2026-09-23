<style>
    html {
        scroll-behavior: smooth;
    }

    .navbar-link {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 38px;
        padding-left: 1rem;
        padding-right: 1rem;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 4px 12px 0 rgba(220, 38, 38, 0.12);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: linear-gradient(135deg, rgb(220, 38, 38) 0%, rgb(168, 12, 12) 100%);
        font-size: 14px;
        border-radius: 0.75rem;
    }

    .navbar-link:hover {
        background: linear-gradient(90deg, #991b1b 0%, #7f1d1d 100%);
        color: #fff;
        box-shadow: 0 4px 16px 0 rgba(220, 38, 38, 0.15);
    }

    /* คลาสสำหรับซ่อน/แสดง (Utility Class) */
    .hidden-custom {
        display: none !important;
    }

    /* Dropdown Animation สำหรับ JS */
    .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        top: 100%;
        /* ปรับตำแหน่งตามต้องการ */
    }

    /* Select2 overrides for modal */
    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 6px 8px;
        border: 1px solid #dc2626;
        /* red-600 */
        border-radius: 0.375rem;
        /* rounded-md */
        background: linear-gradient(90deg, rgb(220, 38, 38) 0%, rgb(168, 12, 12) 100%);
        color: #fff;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #fff;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #f9fafb;
        opacity: .8;
    }

    .dark .select2-container--default .select2-selection--single {
        background: linear-gradient(90deg, #991b1b 0%, #7f1d1d 100%);
        border-color: #7f1d1d;
        color: #fff;
        box-shadow: 0 2px 6px rgba(153, 27, 27, 0.35);
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }

    /* Dropdown z-index so it appears above modal */
    .select2-container .select2-dropdown {
        z-index: 100000;
    }

    /* Custom Select2 Dropdown Item Colors */
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: linear-gradient(90deg, rgb(220, 38, 38) 0%, rgb(168, 12, 12) 100%) !important;
        color: #000 !important;
    }

    .select2-dropdown {
        border: 1px solid #dc2626 !important;
    }

    /* Fix search input text color */
    .select2-search__field {
        color: #000 !important;
    }
</style>
<!-- bg-white dark:bg-gray-800 -->
<nav id="main-navbar"
    class="fixed top-0 inset-x-0 z-[9990] w-full border-b border-gray-100 dark:border-gray-700 shadow-xl transition-all duration-300 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-50">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center h-full">
                <div class="shrink-0 flex items-center gap-3">
                    <a href="{{ route('welcome') }}" class="flex items-center gap-2 group">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-red-600 to-red-800 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg group-hover:scale-105 transition-transform duration-300">
                            H
                        </div>
                        <span
                            class="text-red-500 dark:text-white font-bold text-2xl tracking-tight group-hover:text-red-600 transition-colors duration-300">Kumwell</span>
                    </a>
                </div>
            </div>


            <div class="hidden lg:flex lg:items-center gap-2">

                <div class="hidden lg:flex items-center">
                    

                    <ul class="hidden lg:flex gap-2 items-center">
                        <li>
                            <a class="navbar-link shadow transition" href="{{ route('welcome') }}">หน้าหลัก</a>
                        </li>
                        <li>
                            @auth
                                <section id="HAService">
                                    <a class="navbar-link shadow transition" href="{{ request()->routeIs('welcome') ? '#services-grid' : route('welcome') . '#services-grid' }}">HA Service</a>
                                </section>
                            @else
                                <section id="HAService">
                                    <button type="button" class="login-open-btn navbar-link shadow transition">HA
                                        Service</button>
                                </section>
                            @endauth
                        </li>
                        <li>
                            @auth
                                <section id="news">
                                    <a class="navbar-link shadow transition" href="{{ request()->routeIs('welcome') ? '#news-grid' : route('welcome') . '#news-grid' }}">ประชาสัมพันธ์</a>
                                </section>
                            @else
                                <section id="news">
                                    <button type="button"
                                        class="login-open-btn navbar-link shadow transition">ประชาสัมพันธ์</button>
                                </section>
                            @endauth
                        </li>
                        <li>
                            @auth
                                <a class="navbar-link shadow transition" href="{{ route('manpower-request.index') }}">แบบฟอร์ม</a>
                            @else
                                <button type="button"
                                    class="login-open-btn navbar-link shadow transition">แบบฟอร์ม</button>
                            @endauth
                        </li>
                    </ul>
                </div>

                <div class="relative flex items-center gap-2 ms-3 sm:ms-4">
                    @guest
                        <button type="button" id="login-open-btn"
                            class="login-open-btn navbar-link px-4 py-2 text-base text-black rounded-xl shadow transition">
                            <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i>Login
                        </button>
                    @endguest
                    @auth
                        @php
                            $navUserPhoto = Auth::user()->photo_user;
                            $hasRealPhoto = $navUserPhoto && !str_contains($navUserPhoto, 'pngegg') && file_exists(public_path($navUserPhoto));
                        @endphp
                        <!-- Profile Trigger Button: Circular Avatar (Instagram Style - Image 2) -->
                        <button type="button" id="profile-btn"
                            class="relative rounded-full border border-gray-300 dark:border-slate-600 p-0.5 hover:border-gray-400 dark:hover:border-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer">
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                @if($hasRealPhoto)
                                    <img src="{{ asset($navUserPhoto) }}" alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-5 h-5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7 0 3.75 3.75 0 017 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                    </svg>
                                @endif
                            </div>
                        </button>

                        <!-- Clean Profile Dropdown (Exact Match to Image 2) -->
                        <div id="profile-menu"
                            class="hidden-custom absolute right-0 top-full mt-3 z-50 w-52 origin-top-right rounded-xl bg-white dark:bg-[#1E2129] py-1.5 shadow-xl border border-slate-100 dark:border-slate-700/80 focus:outline-none transition-all duration-200 ease-out">
                            
                            <!-- Top Pointer Arrow (Instagram Style) -->
                            <div class="absolute -top-1.5 right-4 w-3 h-3 bg-white dark:bg-[#1E2129] border-t border-l border-slate-100 dark:border-slate-700/80 transform rotate-45"></div>

                            <div class="relative z-10 py-1">
                                <!-- 1. Profile -->
                                <a href="{{ route('users.profile', ['id' => auth()->id()]) }}"
                                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors font-medium">
                                    <svg class="w-4 h-4 text-slate-700 dark:text-slate-300 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>Profile</span>
                                </a>

                                <!-- 2. Saved -->
                                <a href="{{ route('manpower-request.index') }}"
                                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors font-medium">
                                    <svg class="w-4 h-4 text-slate-700 dark:text-slate-300 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                                    </svg>
                                    <span>Saved</span>
                                </a>

                                <!-- 3. Settings -->
                                <a href="{{ route('users.profile', ['id' => auth()->id()]) }}"
                                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors font-medium">
                                    <svg class="w-4 h-4 text-slate-700 dark:text-slate-300 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>Settings</span>
                                </a>

                                <!-- 4. Switch Accounts -->
                                <a href="{{ route('welcome') }}"
                                    class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors font-medium">
                                    <svg class="w-4 h-4 text-slate-700 dark:text-slate-300 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                    <span>Switch Accounts</span>
                                </a>

                                <!-- Divider -->
                                <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>

                                <!-- 5. Log Out -->
                                <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors font-medium cursor-pointer">
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                    @include('layouts.partials.notification-bell')
                </div>
            </div>

            <div class="-me-2 flex items-center lg:hidden gap-2">
                <!-- Mobile Theme Toggle -->
                <div class="relative">
                    

                    <div id="mobile-theme-dropdown"
                        class="z-[110] hidden-custom absolute right-0 mt-2 w-32 bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700 ring-1 ring-black/5">
                        <ul class="py-2 text-sm text-gray-700 dark:text-gray-200">
                            <li>
                                <button type="button"
                                    class="flex items-center w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                    data-theme-value="light">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                                    </svg>
                                    Light
                                </button>
                            </li>
                            <li>
                                <button type="button"
                                    class="flex items-center w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                    data-theme-value="dark">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                    </svg>
                                    Dark
                                </button>
                            </li>
                            <li>
                                <button type="button"
                                    class="flex items-center w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                    data-theme-value="auto">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    System
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                @include('layouts.partials.notification-bell')
                <button id="mobile-menu-btn"
                    class="flex items-center justify-center w-10 h-10 bg-slate-100/80 dark:bg-slate-800/80 rounded-xl text-slate-600 dark:text-slate-300 transition-all active:scale-95 shadow-sm hover:bg-slate-200 dark:hover:bg-slate-700">
                    <svg id="icon-hamburger" class="h-6 w-6 block" stroke="currentColor" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="icon-close-menu" class="h-6 w-6 hidden-custom" stroke="currentColor" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile Menu Backdrop -->
    <div id="mobile-menu-backdrop" class="fixed inset-0 bg-black/70 z-[9998] hidden opacity-0 transition-opacity duration-300"></div>

    <!-- Mobile Menu Drawer — Dark Premium Style (Slides from Right) -->
    <div id="mobile-menu"
        class="fixed top-0 right-0 h-full w-[300px] bg-white dark:bg-[#0f1117] z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto flex flex-col hidden shadow-2xl">

        <!-- Drawer Header: Logo + Close -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-white/10">
            <div>
                <span class="text-gray-900 dark:text-white font-bold text-xl tracking-tight">Kumwell</span>
                <p class="text-gray-500 dark:text-white/40 text-[11px] tracking-widest mt-0.5">"Safety to Society"</p>
            </div>
            <button id="mobile-menu-close-btn" class="w-9 h-9 flex items-center justify-center rounded-full text-gray-500 hover:text-gray-900 hover:bg-gray-100 dark:text-white/60 dark:hover:text-white dark:hover:bg-white/10 transition-all">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Nav Links -->
        <nav class="flex-1 flex flex-col">
            <a href="{{ route('welcome') }}"
                class="flex items-center px-6 py-5 text-sm font-semibold tracking-widest uppercase text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5 border-b border-gray-100 dark:border-white/10 transition-all duration-200">
                หน้าหลัก
            </a>

            @auth
                <a href="{{ route('manpower-request.index') }}"
                    class="flex items-center px-6 py-5 text-sm font-semibold tracking-widest uppercase text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5 border-b border-gray-100 dark:border-white/10 transition-all duration-200"
                    onclick="closeMobileMenu()">
                    แบบฟอร์ม
                </a>
            @else
                <button type="button" onclick="closeMobileMenu(); openLoginModal();"
                    class="flex items-center px-6 py-5 text-sm font-semibold tracking-widest uppercase text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5 border-b border-gray-100 dark:border-white/10 transition-all duration-200 text-left w-full">
                    แบบฟอร์ม
                </button>
            @endauth

            @auth
                <a href="#services-grid"
                    class="flex items-center px-6 py-5 text-sm font-semibold tracking-widest uppercase text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5 border-b border-gray-100 dark:border-white/10 transition-all duration-200"
                    onclick="closeMobileMenu()">
                    HA Service
                </a>
                <a href="#news-grid"
                    class="flex items-center px-6 py-5 text-sm font-semibold tracking-widest uppercase text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5 border-b border-gray-100 dark:border-white/10 transition-all duration-200"
                    onclick="closeMobileMenu()">
                    ประชาสัมพันธ์
                </a>
                @if(Auth::user()->isHrOrAdmin())
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-6 py-5 text-sm font-semibold tracking-widest uppercase text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5 border-b border-gray-100 dark:border-white/10 transition-all duration-200">
                    Dashboard
                </a>
                @endif
            @else
                <button type="button" onclick="closeMobileMenu(); openLoginModal();"
                    class="flex items-center px-6 py-5 text-sm font-semibold tracking-widest uppercase text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5 border-b border-gray-100 dark:border-white/10 transition-all duration-200 text-left w-full">
                    HA Service
                </button>
                <button type="button" onclick="closeMobileMenu(); openLoginModal();"
                    class="flex items-center px-6 py-5 text-sm font-semibold tracking-widest uppercase text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5 border-b border-gray-100 dark:border-white/10 transition-all duration-200 text-left w-full">
                    ประชาสัมพันธ์
                </button>
            @endauth
        </nav>

        <!-- Footer: Auth/Guest Actions -->
        <div class="px-6 pb-8 pt-4 mt-auto space-y-3">
            @auth
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-red-600 flex items-center justify-center overflow-hidden text-white text-base font-bold">
                        @if(Auth::user()->photo_user)
                            <img src="{{ asset(Auth::user()->photo_user) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            {{ substr(Auth::user()->first_name, 0, 1) }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="text-gray-900 dark:text-white font-bold text-sm truncate">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                        <div class="text-red-500 dark:text-red-400 text-[10px] uppercase tracking-wider font-bold">Authorized User</div>
                    </div>
                </div>

                <a href="{{ route('users.profile', ['id' => auth()->id()]) }}"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200 hover:text-gray-900 dark:bg-white/10 dark:text-white/80 dark:hover:bg-white/15 dark:hover:text-white transition-all">
                    <i class="fa-solid fa-user-gear"></i> Profile Settings
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-red-50 text-red-600 text-sm font-semibold hover:bg-red-100 dark:bg-red-600/20 dark:text-red-400 dark:hover:bg-red-600/30 dark:hover:text-red-300 transition-all"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="fa-solid fa-power-off"></i> Log Out
                    </a>
                </form>
            @endauth

            @guest
                <button type="button" class="login-open-btn w-full flex items-center justify-center gap-2 px-4 py-3.5 rounded-xl bg-red-600 text-white text-sm font-bold tracking-widest uppercase hover:bg-red-700 transition-all shadow-lg shadow-red-900/30">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> LOG IN
                </button>
            @endguest
        </div>
    </div>
<!-- Login Modal -->
@guest
    <div id="login-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden-custom">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md mx-4 relative">
            <div class="flex justify-between items-center px-6 pt-5">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">เข้าสู่ระบบ</h2>
                <!-- <span class="text-gray-500 dark:text-gray-300">ยินดีต้อนรับสู้ระบบ HA</span> -->
                <button type="button" id="login-close-btn"
                    class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('login') }}" class="px-6 pb-6 pt-4 space-y-4">
                @csrf
                <div>
                    <label for="employee_code"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">รหัสพนักงาน</label>
                    <input id="employee_code" name="employee_code" type="text" autocomplete="username" required
                        class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-red-500 focus:ring-red-500"
                        placeholder="กรอกรหัสพนักงาน" value="{{ old('employee_code') }}">
                    @error('employee_code')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">รหัสผ่าน</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                        <input type="checkbox" name="remember"
                            class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        จดจำฉัน
                    </label>
                    <!-- Placeholder for forgot password route if available -->
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-xs text-red-600 hover:text-red-700">ลืมรหัสผ่าน?</a>
                    @endif
                </div>
                <div>
                    <button type="submit" class="w-full navbar-link px-4 py-2 rounded-xl font-medium">เข้าสู่ระบบ</button>
                </div>
            </form>
        </div>
    </div>
@endguest

<script>


    document.addEventListener('DOMContentLoaded', function () {
        // Theme Toggle Logic (match manpower)
        const themeToggleBtn = document.getElementById('theme-toggle-btn');
        const themeDropdown = document.getElementById('theme-dropdown');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        const root = document.documentElement;

        function applyTheme(theme) {
            let resolvedTheme = theme;
            if (theme === 'auto') {
                try { localStorage.removeItem('theme'); } catch (_) { }
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                resolvedTheme = prefersDark ? 'dark' : 'light';
            } else {
                try { localStorage.setItem('theme', theme); } catch (_) { }
            }

            const isDark = resolvedTheme === 'dark';
            root.classList.toggle('dark', isDark);
            root.setAttribute('data-theme', resolvedTheme);

            // Sync any other theme toggles in the page (if present)
            document.querySelectorAll('[data-theme-toggle]')
                .forEach(el => { if ('checked' in el) el.checked = isDark; });
            
            updateThemeIcons();
        }

        // Initialize theme on load
        const savedTheme = localStorage.getItem('theme') || 'auto';
        applyTheme(savedTheme);

        function updateThemeIcons() {
            const isDark = root.classList.contains('dark');
            
            // Desktop Icons
            if (themeToggleDarkIcon && themeToggleLightIcon) {
                if (isDark) {
                    themeToggleDarkIcon.classList.add('hidden');
                    themeToggleLightIcon.classList.remove('hidden');
                } else {
                    themeToggleDarkIcon.classList.remove('hidden');
                    themeToggleLightIcon.classList.add('hidden');
                }
            }
            
            // Mobile Icons
            const mobileDarkIcon = document.getElementById('mobile-theme-toggle-dark-icon');
            const mobileLightIcon = document.getElementById('mobile-theme-toggle-light-icon');
            if (mobileDarkIcon && mobileLightIcon) {
                if (isDark) {
                    mobileDarkIcon.classList.add('hidden');
                    mobileLightIcon.classList.remove('hidden');
                } else {
                    mobileDarkIcon.classList.remove('hidden');
                    mobileLightIcon.classList.add('hidden');
                }
            }
        }

        // Initial icon update
        updateThemeIcons();

        // Toggle dropdown
        if (themeToggleBtn && themeDropdown) {
            themeToggleBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                themeDropdown.classList.toggle('hidden-custom');
                if (mobileThemeDropdown) mobileThemeDropdown.classList.add('hidden-custom');
            });
        }

        // Mobile Theme Toggle
        const mobileThemeToggleBtn = document.getElementById('mobile-theme-toggle-btn');
        const mobileThemeDropdown = document.getElementById('mobile-theme-dropdown');

        if (mobileThemeToggleBtn && mobileThemeDropdown) {
            mobileThemeToggleBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                mobileThemeDropdown.classList.toggle('hidden-custom');
                if (themeDropdown) themeDropdown.classList.add('hidden-custom');
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (e) {
            if (themeDropdown && !themeToggleBtn?.contains(e.target) && !themeDropdown.contains(e.target)) {
                themeDropdown.classList.add('hidden-custom');
            }
            if (mobileThemeDropdown && !mobileThemeToggleBtn?.contains(e.target) && !mobileThemeDropdown.contains(e.target)) {
                mobileThemeDropdown.classList.add('hidden-custom');
            }
        });

        // Handle theme selection
        const themeButtons = document.querySelectorAll('[data-theme-value]');
        themeButtons.forEach(button => {
            button.addEventListener('click', function () {
                const theme = this.getAttribute('data-theme-value');

                if (theme === 'dark' || theme === 'light' || theme === 'auto') {
                    applyTheme(theme);
                }

                updateThemeIcons();
                if (themeDropdown) themeDropdown.classList.add('hidden-custom');
            });
        });

        // --- Mobile Menu Logic ---
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuBackdrop = document.getElementById('mobile-menu-backdrop');
        const mobileMenuCloseBtn = document.getElementById('mobile-menu-close-btn');

        function openMobileMenu() {
            if (!mobileMenu) return;
            mobileMenu.classList.remove('hidden');
            setTimeout(() => {
                mobileMenu.classList.remove('translate-x-full');
            }, 10);
            if (mobileMenuBackdrop) {
                mobileMenuBackdrop.classList.remove('hidden');
                setTimeout(() => mobileMenuBackdrop.classList.remove('opacity-0'), 10);
            }
        }

        function closeMobileMenu() {
            if (!mobileMenu) return;
            mobileMenu.classList.add('translate-x-full');
            if (mobileMenuBackdrop) {
                mobileMenuBackdrop.classList.add('opacity-0');
                setTimeout(() => mobileMenuBackdrop.classList.add('hidden'), 300);
            }
            setTimeout(() => {
                if (mobileMenu.classList.contains('translate-x-full')) {
                    mobileMenu.classList.add('hidden');
                }
            }, 300);
        }

        if (mobileBtn) {
            mobileBtn.addEventListener('click', openMobileMenu);
        }

        if (mobileMenuCloseBtn) {
            mobileMenuCloseBtn.addEventListener('click', closeMobileMenu);
        }

        if (mobileMenuBackdrop) {
            mobileMenuBackdrop.addEventListener('click', closeMobileMenu);
        }

        // --- Profile Dropdown Logic ---
        const profileBtn = document.getElementById('profile-btn');
        const profileMenu = document.getElementById('profile-menu');
        if (profileBtn && profileMenu) {
            profileBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden-custom');
            });
        }

        // --- Forms Dropdown Logic ---
        const formsBtn = document.getElementById('forms-btn');
        const formsMenu = document.getElementById('forms-menu');
        if (formsBtn && formsMenu) {
            formsBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (formsMenu.classList.contains('invisible') || formsMenu.classList.contains('opacity-0')) {
                    formsMenu.classList.remove('invisible', 'opacity-0');
                    formsMenu.classList.add('opacity-100');
                } else {
                    formsMenu.classList.remove('opacity-100');
                    formsMenu.classList.add('invisible', 'opacity-0');
                }
            });
        }

        // --- About Us Dropdown Logic ---
        const aboutBtn = document.getElementById('about-btn');
        const aboutMenu = document.getElementById('about-menu');
        if (aboutBtn && aboutMenu) {
            aboutBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (aboutMenu.classList.contains('invisible') || aboutMenu.classList.contains('opacity-0')) {
                    aboutMenu.classList.remove('invisible', 'opacity-0');
                    aboutMenu.classList.add('opacity-100');
                } else {
                    aboutMenu.classList.remove('opacity-100');
                    aboutMenu.classList.add('invisible', 'opacity-0');
                }
            });
        }

        // --- Global Click Outside to Close Menus ---
        document.addEventListener('click', function (e) {
            if (profileBtn && profileMenu && !profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.add('hidden-custom');
            }
            if (formsBtn && formsMenu && !formsBtn.contains(e.target) && !formsMenu.contains(e.target)) {
                formsMenu.classList.remove('opacity-100');
                formsMenu.classList.add('invisible', 'opacity-0');
            }
            if (aboutBtn && aboutMenu && !aboutBtn.contains(e.target) && !aboutMenu.contains(e.target)) {
                aboutMenu.classList.remove('opacity-100');
                aboutMenu.classList.add('invisible', 'opacity-0');
            }
        });

        // --- Login Modal Logic (Guest only) ---
        const loginOpenBtns = document.querySelectorAll('.login-open-btn');
        const loginModal = document.getElementById('login-modal');
        const loginCloseBtn = document.getElementById('login-close-btn');
        if (loginOpenBtns.length > 0 && loginModal && loginCloseBtn) {
            function openLoginModal() {
                loginModal.classList.remove('hidden-custom');
            }
            function closeLoginModal() {
                loginModal.classList.add('hidden-custom');
            }
            loginOpenBtns.forEach(btn => {
                btn.addEventListener('click', openLoginModal);
            });
            loginCloseBtn.addEventListener('click', closeLoginModal);
            loginModal.addEventListener('click', (e) => { if (e.target === loginModal) closeLoginModal(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeLoginModal(); });
            // Auto-open if validation errors exist or redirected from login route (?login=1)
            const urlParams = new URLSearchParams(window.location.search);
            const shouldOpenLogin = urlParams.get('login') === '1';
            const hasErrors = {{ ($errors->has('employee_code') || $errors->has('password')) ? 'true' : 'false' }};
            if (hasErrors || shouldOpenLogin) openLoginModal();
        }
    });
</script>