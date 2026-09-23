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
        color: #fff !important;
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: linear-gradient(135deg, rgb(220, 38, 38) 0%, rgb(168, 12, 12) 100%);
        font-size: 14px;
        border-radius: 0.75rem;
    }

    .navbar-link:hover {
        background: linear-gradient(90deg, #991b1b 0%, #7f1d1d 100%);
        color: #fff !important;
        box-shadow: 0 4px 16px 0 rgba(220, 38, 38, 0.15);
    }
</style>

<nav id="main-navbar" x-data="{ open: false }" class="sticky top-0 inset-x-0 z-[9990] w-full border-b border-gray-100 dark:border-gray-700 shadow-xl transition-all duration-300 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md">
    <!-- Primary Navigation Menu -->
    <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-10">
        <div class="relative flex items-center justify-between h-16">
            <!-- Logo (Left) -->
            <div class="shrink-0 flex items-center z-20">
                <a href="{{ route('welcome') }}" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-red-600 to-red-800 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg group-hover:scale-105 transition-transform duration-300">
                        H
                    </div>
                    <span class="text-red-500 dark:text-white font-bold text-2xl tracking-tight group-hover:text-red-600 transition-colors duration-300">Kumwell</span>
                </a>
            </div>

            <!-- Navigation Links (Centered/Right) -->
            @php
                $user = auth()->user();
                $actionPendingCount = 0;

                if ($user) {
                    $userName = trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));
                    $userFullName = trim($user->fullname ?? $userName);

                    $pendingQuery = \App\Models\ManpowerRequest::whereNotIn('status', ['approved', 'rejected', 'draft']);

                    if ($user->role !== 'admin') {
                        $pendingQuery->where(function($q) use ($user, $userName, $userFullName) {
                            $q->where(function($sub) use ($userName, $userFullName) {
                                $sub->where('status', 'pending_manager')
                                    ->where(function($m) use ($userName, $userFullName) {
                                        $m->where('manager_name', 'like', "%{$userName}%")
                                          ->orWhere('manager_name', 'like', "%{$userFullName}%")
                                          ->orWhereNull('manager_name')
                                          ->orWhere('manager_name', '');
                                    });
                            })
                            ->orWhere(function($sub) use ($userName, $userFullName) {
                                $sub->where('status', 'pending_vp')
                                    ->where(function($v) use ($userName, $userFullName) {
                                        $v->where('vp_name', 'like', "%{$userName}%")
                                          ->orWhere('vp_name', 'like', "%{$userFullName}%")
                                          ->orWhereNull('vp_name')
                                          ->orWhere('vp_name', '');
                                    });
                            });

                            if ($user->isHrOrAdmin()) {
                                $q->orWhere('status', 'pending_hr');
                            }
                            if ($user->level_user == '9') {
                                $q->orWhere('status', 'pending_ceo');
                            }
                        });
                    }

                    $actionPendingCount = $pendingQuery->count();
                }

                $mprPending = \App\Models\ManpowerRequest::where('user_id', auth()->id())
                    ->whereIn('status', ['pending_manager','pending_vp','pending_hr','pending_ceo'])
                    ->exists();
            @endphp

            <div class="hidden lg:flex lg:items-center gap-2 z-20">
                <a href="{{ route('welcome') }}" class="navbar-link shadow transition">
                    หน้าหลัก
                </a>

                <a href="{{ route('manpower-request.index') }}" class="navbar-link shadow transition flex items-center gap-1.5">
                    <span>ติดตามสถานะ</span>
                    @if($actionPendingCount > 0)
                        <span class="inline-flex items-center justify-center w-5 h-5 px-1 text-[11px] font-extrabold leading-none text-red-600 bg-white rounded-full shadow-sm">
                            {{ $actionPendingCount }}
                        </span>
                    @elseif($mprPending)
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                        </span>
                    @endif
                </a>

                @auth
                <div class="flex items-center gap-2 ms-3 sm:ms-4">
                    <!-- User Profile Dropdown Button (Instagram Style - Image 2) -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                        @php
                            $navUserPhoto = Auth::user()->photo_user;
                            $hasRealPhoto = $navUserPhoto && !str_contains($navUserPhoto, 'pngegg') && file_exists(public_path($navUserPhoto));
                        @endphp
                        <button type="button" @click="open = !open"
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

                        <div x-show="open"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            style="display: none;"
                            class="absolute right-0 top-full mt-3 z-50 w-52 origin-top-right rounded-xl bg-white dark:bg-[#1E2129] py-1.5 shadow-xl border border-slate-100 dark:border-slate-700/80 focus:outline-none">
                            
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
                    </div>
                    @include('layouts.partials.notification-bell')
                </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center lg:hidden gap-3 pl-2">
                <div class="shrink-0 flex items-center">
                    @include('layouts.partials.notification-bell')
                </div>
                <button id="mobile-menu-btn"
                    class="shrink-0 flex items-center justify-center w-10 h-10 ml-2 bg-slate-100/80 dark:bg-slate-800/80 rounded-xl text-slate-600 dark:text-slate-300 transition-all active:scale-95 shadow-sm hover:bg-slate-200 dark:hover:bg-slate-700">
                    <svg id="icon-hamburger" class="h-6 w-6 block" stroke="currentColor" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="icon-close-menu" class="h-6 w-6 hidden" stroke="currentColor" fill="none"
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

        <!-- Mobile Forms Section -->
        <div class="border-b border-gray-100 dark:border-white/10 py-2">
            <div class="px-6 py-2 text-[11px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest flex items-center justify-between">
                <span>แบบฟอร์ม HR</span>
                <span class="text-[10px] font-mono bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 px-1.5 py-0.5 rounded">Forms</span>
            </div>
            @auth
                <a href="{{ route('manpower-request.create') }}"
                    class="flex items-center gap-3 px-6 py-3 text-sm font-semibold text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5 transition-all">
                    <i class="fa-solid fa-user-plus text-red-500 w-5 text-center"></i>
                    <div class="flex-1 min-w-0">
                        <div class="truncate">ใบขออนุมัติกำลังคน</div>
                        <div class="text-[10px] text-gray-400 dark:text-gray-500 font-mono">QF-HR-13</div>
                    </div>
                </a>
                <a href="{{ route('probation-evaluation.create') }}"
                    class="flex items-center gap-3 px-6 py-3 text-sm font-semibold text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5 transition-all">
                    <i class="fa-solid fa-clipboard-check text-red-500 w-5 text-center"></i>
                    <div class="flex-1 min-w-0">
                        <div class="truncate">แบบประเมินทดลองงาน</div>
                        <div class="text-[10px] text-gray-400 dark:text-gray-500 font-mono">QF-HR-18</div>
                    </div>
                </a>
                <a href="{{ route('interview-evaluation.create') }}"
                    class="flex items-center gap-3 px-6 py-3 text-sm font-semibold text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5 transition-all">
                    <i class="fa-solid fa-id-card-clip text-red-500 w-5 text-center"></i>
                    <div class="flex-1 min-w-0">
                        <div class="truncate">แบบประเมินผลสัมภาษณ์</div>
                        <div class="text-[10px] text-gray-400 dark:text-gray-500 font-mono">QF-HR-25</div>
                    </div>
                </a>
                <a href="{{ route('manpower-request.index') }}"
                    class="flex items-center justify-between px-6 py-3 text-sm font-semibold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-white/5 transition-all">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-list-check w-5 text-center"></i>
                        <span>ติดตามสถานะแบบฟอร์ม</span>
                    </div>
                    @if($actionPendingCount > 0)
                        <span class="inline-flex items-center justify-center min-w-[20px] h-[20px] px-1.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full shadow-sm animate-pulse">
                            {{ $actionPendingCount }}
                        </span>
                    @elseif($mprPending)
                        <span class="inline-flex items-center justify-center min-w-[20px] h-[20px] px-1.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">!</span>
                    @endif
                </a>
            @else
                <button type="button" id="login-open-btn" onclick="closeMobileMenu()"
                    class="w-full flex items-center gap-3 px-6 py-3 text-sm font-semibold text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5 transition-all text-left">
                    <i class="fa-solid fa-user-plus text-red-500 w-5 text-center"></i>
                    <div class="flex-1 min-w-0">
                        <div class="truncate">ใบขออนุมัติกำลังคน</div>
                        <div class="text-[10px] text-gray-400 dark:text-gray-500 font-mono">QF-HR-13 (กรุณาเข้าสู่ระบบ)</div>
                    </div>
                </button>
                <button type="button" class="login-open-btn w-full flex items-center gap-3 px-6 py-3 text-sm font-semibold text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5 transition-all text-left" onclick="closeMobileMenu()">
                    <i class="fa-solid fa-clipboard-check text-red-500 w-5 text-center"></i>
                    <div class="flex-1 min-w-0">
                        <div class="truncate">แบบประเมินทดลองงาน</div>
                        <div class="text-[10px] text-gray-400 dark:text-gray-500 font-mono">QF-HR-18 (กรุณาเข้าสู่ระบบ)</div>
                    </div>
                </button>
                <button type="button" class="login-open-btn w-full flex items-center gap-3 px-6 py-3 text-sm font-semibold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-white/5 transition-all text-left" onclick="closeMobileMenu()">
                    <i class="fa-solid fa-list-check w-5 text-center"></i>
                    <span>ติดตามสถานะแบบฟอร์ม (กรุณาเข้าสู่ระบบ)</span>
                </button>
            @endauth
        </div>
    </nav>

    <!-- Footer: Auth/Guest Actions -->
    <div class="px-6 pb-8 pt-4 mt-auto space-y-3">
        @auth
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-red-600 flex items-center justify-center overflow-hidden text-white">
                    @if(Auth::user()->photo_user)
                        <img src="{{ asset(Auth::user()->photo_user) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <i class="fa-solid fa-user text-base"></i>
                    @endif
                </div>
                <div class="min-w-0">
                    <div class="text-gray-900 dark:text-white font-bold text-sm truncate">{{ Auth::user()->first_name ?? Auth::user()->name }} {{ Auth::user()->last_name ?? '' }}</div>
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
            <button type="button" id="login-open-btn"
                class="w-full flex items-center justify-center gap-2 px-4 py-3.5 rounded-xl bg-red-600 text-white text-sm font-bold tracking-widest uppercase hover:bg-red-700 transition-all shadow-lg shadow-red-900/30">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> LOG IN
            </button>
        @endguest
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
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
    });
</script>
