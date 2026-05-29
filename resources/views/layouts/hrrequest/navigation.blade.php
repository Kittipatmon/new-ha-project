<style>
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
        transform: translateY(-1px);
        background: linear-gradient(135deg, rgb(185, 28, 28) 0%, rgb(153, 27, 27) 100%);
        box-shadow: 0 6px 16px 0 rgba(220, 38, 38, 0.2);
        color: #fff;
    }

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
</style>
<!-- bg-white dark:bg-gray-800 -->
<nav class="border-b border-gray-100 dark:border-gray-700 shadow-xl bg-white/90 dark:bg-[#1E2129]/90 backdrop-blur-md sticky top-0 z-50 transition-all duration-300">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center h-full">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('welcome') }}">
                        <span class="text-red-600 font-bold text-lg sm:text-xl lg:text-3xl ml-2">Kumwell</span>
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">

                <div class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex">
                    <div class="relative">
                        <button id="theme-toggle-btn" type="button"
                            class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                            <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor"
                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                            <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor"
                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z"
                                    fill-rule="evenodd" clip-rule="evenodd"></path>
                            </svg>
                        </button>

                        <!-- Dropdown menu -->
                        <div id="theme-dropdown"
                            class="z-50 hidden-custom absolute right-0 mt-2 w-32 bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700">
                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200"
                                aria-labelledby="theme-toggle-btn">
                                <li>
                                    <button type="button"
                                        class="flex items-center w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                        data-theme-value="light">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z"
                                                fill-rule="evenodd" clip-rule="evenodd"></path>
                                        </svg>
                                        Light
                                    </button>
                                </li>
                                <li>
                                    <button type="button"
                                        class="flex items-center w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                        data-theme-value="dark">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z">
                                            </path>
                                        </svg>
                                        Dark
                                    </button>
                                </li>
                                <li>
                                    <button type="button"
                                        class="flex items-center w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                        data-theme-value="auto">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        System
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <ul class="hidden lg:flex space-x-1 items-center">
                        <li>
                            <a class="navbar-link shadow transition"
                                href="{{ route('welcome') }}">หน้าหลัก</a>
                        </li>
                        <li>
                            <a class="navbar-link shadow transition"
                                href="{{ route('request.hr') }}">Request HR</a>
                        </li>
                        <li>
                            <a class="navbar-link shadow transition"
                                href="{{ route('requesthr.dashboard') }}">Dashboard</a>
                        </li>
                    </ul>
                </div>

                <div class="relative ml-1">
                    @guest
                        <button type="button" id="login-open-btn"
                            class="navbar-link px-4 py-2 text-base text-black rounded-xl shadow transition">
                            <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i>Login
                        </button>
                    @endguest
                    @auth

                        <button type="button" id="profile-btn"
                            class="navbar-link p-1 text-black rounded-xl shadow flex items-center transition overflow-hidden">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center overflow-hidden border border-white/10">
                                    @if(Auth::user()->photo_user)
                                        <img src="{{ asset(Auth::user()->photo_user) }}" alt="Avatar" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-gear text-white text-sm"></i>
                                    @endif
                                </div>
                                <svg class="fill-current h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                        <div id="profile-menu"
                            class="absolute right-0 z-50 mt-3 w-64 origin-top-right rounded-2xl bg-white/95 dark:bg-[#1E2129]/95 backdrop-blur-xl py-2 shadow-2xl ring-1 ring-black/5 focus:outline-none hidden-custom border border-gray-100 dark:border-white/5 animate-fade-in">
                            <!-- Header Section -->
                            <div class="px-4 py-4 border-b border-gray-100 dark:border-white/5 mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="relative group">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-red-600 p-[2px] shadow-lg shadow-red-500/20">
                                            <div class="w-full h-full rounded-[10px] bg-white dark:bg-[#1E2129] overflow-hidden">
                                                @if(Auth::user()->photo_user)
                                                    <img src="{{ asset(Auth::user()->photo_user) }}" alt="Avatar" class="w-full h-full object-cover transition-transform group-hover:scale-110">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center bg-slate-50 dark:bg-slate-800">
                                                        <i class="fa-solid fa-user text-slate-400"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white dark:border-[#1E2129] rounded-full"></div>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-black text-slate-800 dark:text-white line-clamp-1">
                                            {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                                        </p>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="flex-shrink-0 w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                            <p class="text-[10px] font-bold text-red-500 uppercase tracking-wider truncate">
                                                Authorized User
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Section -->
                            <div class="px-2 space-y-1">
                                <a href="{{ route('users.profile', ['id' => auth()->id()]) }}"
                                    class="flex items-center gap-3 px-3 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-red-600 dark:hover:text-red-500 transition-all rounded-xl group">
                                    <div class="w-9 h-9 rounded-lg bg-sky-50 dark:bg-sky-500/10 flex items-center justify-center text-sky-500 group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-user-gear text-base"></i>
                                    </div>
                                    <span>{{ __('Profile Settings') }}</span>
                                </a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}"
                                        class="flex items-center gap-3 px-3 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all rounded-xl group"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        <div class="w-9 h-9 rounded-lg bg-red-50 dark:bg-red-500/10 flex items-center justify-center text-red-600 group-hover:scale-110 transition-transform">
                                            <i class="fa-solid fa-power-off text-base"></i>
                                        </div>
                                        <span>{{ __('Log Out') }}</span>
                                    </a>
                                </form>
                            </div>

                            <!-- Footer Section -->
                            <div class="mt-2 px-4 py-2 border-t border-gray-100 dark:border-white/5 flex justify-between items-center">
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Kumwell HR System</p>
                                <span class="text-[9px] font-black text-red-500/50">v2.1</span>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>

            <div class="-me-2 flex items-center lg:hidden gap-2">
                <!-- Mobile Theme Toggle -->
                <div class="relative">
                    <button id="mobile-theme-toggle-btn" type="button"
                        class="flex items-center justify-center w-10 h-10 bg-slate-100/80 dark:bg-slate-800/80 rounded-xl text-slate-600 dark:text-slate-300 transition-all active:scale-95 shadow-sm hover:bg-slate-200 dark:hover:bg-slate-700">
                        <svg id="mobile-theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg id="mobile-theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor"
                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 100 2h1z"
                                fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                    </button>

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

    <div id="mobile-menu"
        class="hidden-custom lg:hidden absolute top-full left-0 w-full bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl border-b border-gray-100 dark:border-gray-800 shadow-2xl animate-fade-in shadow-xl overflow-y-auto max-h-[85vh]">
        <div class="pt-4 pb-6 px-4 space-y-2">
            <!-- Main Links -->
            <a href="{{ route('welcome') }}"
                class="flex items-center gap-4 px-4 py-3.5 rounded-2xl text-base font-bold text-slate-700 dark:text-slate-200 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 transition-all duration-200 group">
                <div
                    class="w-10 h-10 rounded-xl bg-red-100/50 dark:bg-red-500/10 flex items-center justify-center text-red-600 group-hover:scale-110 transition-transform shadow-sm">
                    <i class="fa-solid fa-house text-lg"></i>
                </div>
                <span>หน้าหลัก</span>
            </a>

            <a href="{{ route('request.hr') }}"
                class="flex items-center gap-4 px-4 py-3.5 rounded-2xl text-base font-bold text-slate-700 dark:text-slate-200 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 transition-all duration-200 group">
                <div
                    class="w-10 h-10 rounded-xl bg-red-100/50 dark:bg-red-500/10 flex items-center justify-center text-red-600 group-hover:scale-110 transition-transform shadow-sm">
                    <i class="fa-solid fa-file-invoice text-lg"></i>
                </div>
                <span>Request HR</span>
            </a>

            <a href="{{ route('requesthr.dashboard') }}"
                class="flex items-center gap-4 px-4 py-3.5 rounded-2xl text-base font-bold text-slate-700 dark:text-slate-200 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 transition-all duration-200 group">
                <div
                    class="w-10 h-10 rounded-xl bg-red-100/50 dark:bg-red-500/10 flex items-center justify-center text-red-600 group-hover:scale-110 transition-transform shadow-sm">
                    <i class="fa-solid fa-gauge-high text-lg"></i>
                </div>
                <span>Dashboard</span>
            </a>
        </div>

        @auth
            <div class="mt-4 pt-6 pb-8 px-4 border-t border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-4 px-2 mb-6">
                    <div class="relative">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-700 rounded-2xl flex items-center justify-center text-white text-2xl font-black shadow-xl shadow-red-500/20 rotate-3 overflow-hidden">
                            @if(Auth::user()->photo_user)
                                <img src="{{ asset(Auth::user()->photo_user) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <i class="fa-solid fa-gear text-2xl"></i>
                            @endif
                        </div>
                        <div
                            class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-white dark:border-gray-900 rounded-full">
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-black text-lg text-slate-800 dark:text-white leading-tight truncate">
                            {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                        </div>
                        <div class="text-xs font-bold text-red-500 uppercase tracking-wider mt-0.5">Authorized User</div>
                    </div>
                </div>

                <div class="space-y-1">
                    <a href="{{ route('users.profile', ['id' => auth()->id()]) }}"
                        class="flex items-center gap-4 px-4 py-3 rounded-2xl text-base font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/5 hover:text-red-600 transition-all">
                        <div
                            class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-500 dark:text-gray-400">
                            <i class="fa-solid fa-gear text-lg"></i>
                        </div>
                        <span>Profile Settings</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}"
                            class="flex items-center gap-4 px-4 py-3 rounded-2xl text-base font-bold text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            <div
                                class="w-10 h-10 rounded-xl bg-red-100/50 dark:bg-red-500/10 flex items-center justify-center text-red-600">
                                <i class="fa-solid fa-arrow-right-from-bracket text-lg"></i>
                            </div>
                            <span>Log Out</span>
                        </a>
                    </form>
                </div>
            </div>
        @endauth

        @guest
            <div class="pt-4 pb-4 px-4 border-t border-gray-100 dark:border-gray-800">
                <button type="button" id="login-open-btn"
                    class="w-full navbar-link flex items-center justify-center gap-2 px-4 py-3 text-base font-bold rounded-2xl shadow-lg transition-all active:scale-95">
                    <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i>Login
                </button>
            </div>
        @endguest
    </div>
</nav>

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
        const iconHamburger = document.getElementById('icon-hamburger');
        const iconCloseMenu = document.getElementById('icon-close-menu');

        mobileBtn.addEventListener('click', function () {
            mobileMenu.classList.toggle('hidden-custom');
            mobileMenu.classList.toggle('block'); // Ensure proper display type

            // Toggle Icons
            if (mobileMenu.classList.contains('hidden-custom')) {
                iconHamburger.classList.remove('hidden-custom');
                iconCloseMenu.classList.add('hidden-custom');
            } else {
                iconHamburger.classList.add('hidden-custom');
                iconCloseMenu.classList.remove('hidden-custom');
            }
        });

        // --- Profile Dropdown Logic ---
        const profileBtn = document.getElementById('profile-btn');
        const profileMenu = document.getElementById('profile-menu');
        if (profileBtn && profileMenu) {
            profileBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden-custom');
            });
        }

        // --- About Us Dropdown Logic ---
        const aboutBtn = document.getElementById('about-btn');
        const aboutMenu = document.getElementById('about-menu');
        if (aboutBtn && aboutMenu) {
            aboutBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (aboutMenu.classList.contains('invisible')) {
                    aboutMenu.classList.remove('invisible', 'opacity-0');
                } else {
                    aboutMenu.classList.add('invisible', 'opacity-0');
                }
            });
        }

        // --- Global Click Outside to Close Menus ---
        document.addEventListener('click', function (e) {
            if (profileBtn && profileMenu && !profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.add('hidden-custom');
            }
            if (aboutBtn && aboutMenu && !aboutBtn.contains(e.target) && !aboutMenu.contains(e.target)) {
                aboutMenu.classList.add('invisible', 'opacity-0');
            }
        });

        // --- Login Modal Logic (Guest only) ---
        const loginOpenBtn = document.getElementById('login-open-btn');
        const loginModal = document.getElementById('login-modal');
        const loginCloseBtn = document.getElementById('login-close-btn');
        if (loginOpenBtn && loginModal && loginCloseBtn) {
            function openLoginModal() {
                loginModal.classList.remove('hidden-custom');
            }
            function closeLoginModal() {
                loginModal.classList.add('hidden-custom');
            }
            loginOpenBtn.addEventListener('click', openLoginModal);
            loginCloseBtn.addEventListener('click', closeLoginModal);
            loginModal.addEventListener('click', (e) => { if (e.target === loginModal) closeLoginModal(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeLoginModal(); });
            // Auto-open if validation errors exist
            const hasErrors = {{ ($errors->has('employee_code') || $errors->has('password')) ? 'true' : 'false' }};
            if (hasErrors) openLoginModal();
        }
    });
</script>