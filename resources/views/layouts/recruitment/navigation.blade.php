<style>
    .navbar-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 33px;
        padding: 0 0.65rem;
        white-space: nowrap !important;
        flex-shrink: 0;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 6px 0 rgba(220, 38, 38, 0.15);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.15);
        background: linear-gradient(135deg, rgb(220, 38, 38) 0%, rgb(168, 12, 12) 100%);
        font-family: 'Inter', 'Prompt', 'Noto Sans Thai', sans-serif;
        font-size: 12.5px;
        font-weight: 500;
        letter-spacing: -0.01em;
        border-radius: 0.55rem;
        text-decoration: none;
        user-select: none;
    }

    @media (min-width: 1536px) {
        .navbar-link {
            height: 36px;
            padding: 0 0.85rem;
            font-size: 13.5px;
            border-radius: 0.625rem;
        }
    }

    .navbar-link:hover {
        background: linear-gradient(90deg, #991b1b 0%, #7f1d1d 100%);
        color: #fff;
        box-shadow: 0 4px 14px 0 rgba(220, 38, 38, 0.25);
        transform: translateY(-1px);
    }

    /* Active state to clearly highlight the current menu */
    .navbar-link.active,
    .navbar-link[aria-current="page"] {
        background: linear-gradient(135deg, #7f1d1d 0%, #450a0a 100%) !important;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.35), 0 0 0 2px rgba(254, 202, 202, 0.6) !important;
        font-weight: 700;
        color: #fff !important;
    }

    /* Hide scrollbar for clean horizontal scrolling when needed */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Utility Class */
    .hidden-custom {
        display: none !important;
    }

    /* Select2 overrides for modal */
    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 6px 8px;
        border: 1px solid #dc2626;
        border-radius: 0.375rem;
        background: linear-gradient(90deg, rgb(220, 38, 38) 0%, rgb(168, 12, 12) 100%);
        color: #fff;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #fff;
        line-height: 24px;
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

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }

    .select2-container .select2-dropdown {
        z-index: 100000;
    }
</style>

<!-- Top Navigation Bar -->
<nav class="border-b border-gray-100 dark:border-gray-800 shadow-md bg-white/95 dark:bg-[#1E2129]/95 backdrop-blur-md fixed w-full top-0 z-[9990] transition-all duration-300">
    <div class="w-full max-w-[1720px] mx-auto px-3 sm:px-4 lg:px-6">
        <div class="flex justify-between items-center h-16">
            
            <!-- Brand Logo (ทุกขนาดหน้าจอ) -->
            <div class="flex items-center h-full shrink-0">
                <a href="{{ route('welcome') }}" class="flex items-center gap-2 sm:gap-2.5 group">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-gradient-to-br from-red-600 to-red-800 rounded-xl flex items-center justify-center text-white font-black text-lg sm:text-xl shadow-md group-hover:scale-105 transition-transform duration-300">
                        H
                    </div>
                    <span class="text-red-600 dark:text-white font-extrabold text-xl sm:text-2xl tracking-tight group-hover:text-red-700 transition-colors duration-300">Kumwell</span>
                </a>
            </div>

            @php
                $navPendingJobPostCount = \App\Models\Recruitment\RecruitmentRequest::where('status', 'approved')->doesntHave('jobPosts')->count();
                $canViewDeptCandidates = false;
                $pendingDeptReviewCount = 0;
                if (Auth::check()) {
                    $u = Auth::user();
                    $canViewDeptCandidates = $u->canViewDeptCandidates();
                    if ($canViewDeptCandidates) {
                        $mDeptIds = $u->getManagedDepartmentIds();
                        $pendingDeptReviewCount = \App\Models\Recruitment\Application::where('status', 'dept_review')
                            ->when(!$u->isCentralHr(), function($q) use ($mDeptIds) {
                                $q->whereHas('jobPost', function($jq) use ($mDeptIds) {
                                    $jq->whereIn('department_id', $mDeptIds);
                                });
                            })
                            ->count();
                    }
                }
            @endphp

            <!-- Desktop Nav Links (แสดงเมื่อหน้าจอใหญ่พอที่จะขึ้นครบทุกเมนู xl: 1280px ขึ้นไป) -->
            <div class="hidden xl:flex xl:items-center xl:flex-1 xl:justify-end gap-1.5 2xl:gap-2.5 min-w-0 ms-4">
                <ul class="flex items-center gap-1 xl:gap-1.5 whitespace-nowrap">
                    <!-- 1. หน้าหลัก -->
                    <li>
                        <a class="navbar-link {{ request()->routeIs('welcome') ? 'active' : '' }}"
                            href="{{ route('welcome') }}">หน้าหลัก</a>
                    </li>

                    <!-- 2. สมัครงาน -->
                    <li>
                        <a class="navbar-link {{ (request()->routeIs('recruitment.index') || request()->routeIs('recruitment.jobs.*')) ? 'active' : '' }}"
                            href="{{ route('recruitment.index') }}">สมัครงาน</a>
                    </li>

                    @if(Auth::check() && Auth::user()->isHrOrAdmin())
                        <!-- 3. Dashboard -->
                        <li>
                            <a class="navbar-link {{ request()->routeIs('backend.recruitment.dashboard') ? 'active' : '' }}"
                                href="{{ route('backend.recruitment.dashboard') }}">Dashboard</a>
                        </li>

                        <!-- 4. รายชื่อผู้สมัคร -->
                        <li>
                            <a class="navbar-link {{ request()->routeIs('backend.recruitment.applications.*') ? 'active' : '' }}"
                                href="{{ route('backend.recruitment.applications.index') }}">รายชื่อผู้สมัคร</a>
                        </li>

                        <!-- 5. ประกาศรับสมัครงาน -->
                        <li>
                            <a class="navbar-link {{ request()->routeIs('backend.recruitment.posts.*') ? 'active' : '' }}"
                                href="{{ route('backend.recruitment.posts.index') }}">ประกาศ</a>
                        </li>

                        <!-- 6. คำขอเปิดรับสมัครพนักงาน -->
                        <li>
                            <a class="navbar-link {{ request()->routeIs('backend.recruitment.requests.*') ? 'active' : '' }} flex items-center gap-1.5"
                                href="{{ route('backend.recruitment.requests.index') }}">
                                <span>คำขอเปิดรับสมัคร</span>
                                @if($navPendingJobPostCount > 0)
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-extrabold text-amber-900 bg-amber-300 rounded-full shadow-sm animate-pulse" title="มีคำขออนุมัติแล้วรอสร้าง Job Post: {{ $navPendingJobPostCount }}">
                                        {{ $navPendingJobPostCount }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        <!-- 7. ประวัติส่งอีเมล -->
                        <li>
                            <a class="navbar-link {{ request()->routeIs('backend.recruitment.mail-logs.*') ? 'active' : '' }} flex items-center gap-1.5"
                                href="{{ route('backend.recruitment.mail-logs.index') }}" title="ประวัติการส่งอีเมล">
                                <i class="fa-solid fa-envelope-circle-check text-xs"></i>
                                <span>ประวัติส่งอีเมล</span>
                            </a>
                        </li>
                    @endif

                    @auth
                        @if($canViewDeptCandidates)
                            <!-- 8. ผู้สมัครที่ HA ส่งมา -->
                            <li>
                                <a class="navbar-link {{ request()->routeIs('recruitment.reports') ? 'active' : '' }} flex items-center gap-1.5"
                                    href="{{ route('recruitment.reports') }}"
                                    title="ดูรายชื่อผู้สมัครที่ HA คัดกรองและส่งมาให้พิจารณา">
                                    <i class="fa-solid fa-user-check text-xs"></i>
                                    <span>ผู้สมัครที่ HA ส่งมา</span>
                                    @if($pendingDeptReviewCount > 0)
                                        <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-extrabold text-red-700 bg-white rounded-full shadow-sm animate-pulse">
                                            {{ $pendingDeptReviewCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <!-- Desktop User Profile & Notification Bell -->
                <div class="relative shrink-0 flex items-center gap-2 ms-2 xl:ms-3">
                    @guest
                        <button type="button" class="login-open-btn navbar-link px-4 py-2 text-sm rounded-xl shadow transition">
                            <i class="fa-solid fa-arrow-right-from-bracket mr-1.5"></i>Login
                        </button>
                    @endguest
                    @auth
                        @php
                            $navUserPhoto = Auth::user()->photo_user;
                            $hasRealPhoto = $navUserPhoto && !str_contains($navUserPhoto, 'pngegg') && file_exists(public_path($navUserPhoto));
                        @endphp
                        <button type="button" id="profile-btn"
                            class="relative rounded-full border border-gray-300 dark:border-slate-600 p-0.5 hover:border-gray-400 dark:hover:border-slate-400 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all cursor-pointer">
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

                        <!-- Profile Dropdown -->
                        <div id="profile-menu"
                            class="hidden-custom absolute right-0 top-full mt-3 z-50 w-56 origin-top-right rounded-2xl bg-white dark:bg-[#1E2129] py-1.5 shadow-xl border border-slate-100 dark:border-slate-700/80 focus:outline-none transition-all duration-200">
                            
                            <div class="absolute -top-1.5 right-4 w-3 h-3 bg-white dark:bg-[#1E2129] border-t border-l border-slate-100 dark:border-slate-700/80 transform rotate-45"></div>

                            <div class="relative z-10 py-1">
                                <!-- Profile Info Header -->
                                <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800">
                                    <div class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()->department->department_name ?? 'Kumwell Staff' }}</div>
                                </div>

                                <!-- 1. Profile -->
                                <a href="{{ route('users.profile', ['id' => auth()->id()]) }}"
                                    class="flex items-center gap-3 px-4 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-red-50 dark:hover:bg-red-950/30 hover:text-red-600 transition-colors font-medium">
                                    <i class="fa-solid fa-user-gear text-slate-500 w-4 text-center"></i>
                                    <span>Profile</span>
                                </a>

                                <!-- 2. Saved (Manpower Requests) -->
                                <a href="{{ route('manpower-request.index') }}"
                                    class="flex items-center gap-3 px-4 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-red-50 dark:hover:bg-red-950/30 hover:text-red-600 transition-colors font-medium">
                                    <i class="fa-solid fa-bookmark text-slate-500 w-4 text-center"></i>
                                    <span>Saved</span>
                                </a>

                                <!-- Microsoft 365 Status / Connect (เฉพาะ HA และ Admin) -->
                                @if(Auth::check() && (Auth::user()->isHrOrAdmin() || Auth::user()->dept_id == 15))
                                    @if(Auth::user()->hasMicrosoftConnected())
                                        <div class="px-4 py-2 text-xs flex items-center justify-between bg-emerald-50/60 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400">
                                            <span class="flex items-center gap-2 truncate">
                                                <i class="fa-brands fa-microsoft"></i>
                                                <span class="truncate max-w-[120px] font-medium" title="{{ Auth::user()->microsoftToken?->microsoft_email }}">
                                                    {{ Auth::user()->microsoftToken?->microsoft_email }}
                                                </span>
                                            </span>
                                            <form action="{{ route('auth.microsoft.disconnect') }}" method="POST" class="inline m-0" onsubmit="return confirmDisconnectMicrosoft(this, event)">
                                                @csrf
                                                <button type="submit" class="text-rose-500 hover:underline text-[11px] font-bold">ยกเลิก</button>
                                            </form>
                                        </div>
                                    @else
                                        <a href="{{ route('auth.microsoft.redirect') }}"
                                            class="flex items-center gap-3 px-4 py-2 text-xs text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/40 transition-colors font-medium">
                                            <i class="fa-brands fa-microsoft text-base shrink-0"></i>
                                            <span>เชื่อมต่อ Microsoft 365</span>
                                        </a>
                                    @endif
                                @endif

                                <!-- Switch Accounts -->
                                <a href="{{ route('welcome') }}"
                                    class="flex items-center gap-3 px-4 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-red-50 dark:hover:bg-red-950/30 hover:text-red-600 transition-colors font-medium">
                                    <i class="fa-solid fa-repeat text-slate-500 w-4 text-center"></i>
                                    <span>Switch Accounts</span>
                                </a>

                                <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>

                                <!-- Log Out -->
                                <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors font-medium cursor-pointer">
                                        <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                    @include('layouts.partials.notification-bell')
                </div>
            </div>

            <!-- Mobile, Tablet & Laptop Bar (หน้าจอที่เมนูขึ้นไม่ครบ < 1280px จะแสดงแบบมือถือทันที) -->
            <div class="flex items-center xl:hidden gap-1.5 sm:gap-2 shrink-0">
                <!-- Notification Bell -->
                @include('layouts.partials.notification-bell')

                @auth
                    <!-- Mobile Avatar Button -->
                    <button type="button" id="mobile-profile-avatar-btn"
                        class="relative rounded-full border border-gray-300 dark:border-slate-600 p-0.5 hover:border-gray-400 dark:hover:border-slate-400 focus:outline-none transition-all cursor-pointer">
                        <div class="w-8 h-8 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                            @if(isset($hasRealPhoto) && $hasRealPhoto)
                                <img src="{{ asset($navUserPhoto) }}" alt="Avatar" class="w-full h-full object-cover">
                            @else
                                <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7 0 3.75 3.75 0 017 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                </svg>
                            @endif
                        </div>
                    </button>
                @endauth

                <!-- Hamburger Button -->
                <button id="mobile-menu-btn"
                    class="flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 bg-slate-100/90 dark:bg-slate-800/90 rounded-xl text-slate-700 dark:text-slate-200 transition-all active:scale-95 shadow-sm hover:bg-red-50 hover:text-red-600 dark:hover:bg-slate-700"
                    aria-label="เมนู">
                    <svg id="icon-hamburger" class="h-5 w-5 sm:h-6 sm:w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

        </div>
    </div>
</nav>

<!-- Mobile Menu Backdrop -->
<div id="mobile-menu-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9998] hidden opacity-0 transition-opacity duration-300"></div>

<!-- Mobile & iPad Menu Drawer (รองรับทั้ง มือถือ, iPad, Tablet) -->
<div id="mobile-menu"
    class="fixed top-0 right-0 h-full w-[85vw] max-w-[340px] sm:max-w-[380px] bg-white dark:bg-[#151821] z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col hidden shadow-2xl border-l border-slate-200/80 dark:border-slate-800">

    <!-- Drawer Header -->
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800 shrink-0">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 bg-gradient-to-br from-red-600 to-red-800 rounded-lg flex items-center justify-center text-white font-bold text-base shadow">
                H
            </div>
            <div>
                <span class="text-gray-900 dark:text-white font-black text-lg tracking-tight">Kumwell</span>
                <p class="text-gray-400 dark:text-gray-500 text-[10px] tracking-wider leading-none">Safety to Society</p>
            </div>
        </div>
        <button id="mobile-menu-close-btn" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800 transition-all cursor-pointer">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>
    </div>

    <!-- Drawer Content (Scrollable) -->
    <div class="flex-1 overflow-y-auto px-4 py-3 space-y-4 no-scrollbar">

        @auth
            <!-- User Profile Summary Card -->
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/80 space-y-2.5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-red-600 to-red-800 flex items-center justify-center overflow-hidden text-white shrink-0 shadow">
                        @if(Auth::user()->photo_user)
                            <img src="{{ asset(Auth::user()->photo_user) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <i class="fa-solid fa-user text-lg"></i>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-gray-900 dark:text-white font-bold text-sm truncate">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->department->department_name ?? 'Kumwell Staff' }}</div>
                        <div class="mt-0.5 inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-300">
                            {{ Auth::user()->isHrOrAdmin() ? 'ฝ่ายทรัพยากรบุคคล (HA)' : 'ผู้ใช้งานระบบ' }}
                        </div>
                    </div>
                </div>

                <!-- Microsoft 365 status for HA/Admin on Mobile/iPad -->
                @if(Auth::check() && (Auth::user()->isHrOrAdmin() || Auth::user()->dept_id == 15))
                    <div class="pt-2 border-t border-slate-200 dark:border-slate-700/60">
                        @if(Auth::user()->hasMicrosoftConnected())
                            <div class="p-2 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl flex items-center justify-between text-xs text-emerald-800 dark:text-emerald-300">
                                <span class="flex items-center gap-2 truncate">
                                    <i class="fa-brands fa-microsoft text-emerald-600 text-sm"></i>
                                    <span class="truncate max-w-[160px]" title="{{ Auth::user()->microsoftToken?->microsoft_email }}">
                                        {{ Auth::user()->microsoftToken?->microsoft_email }}
                                    </span>
                                </span>
                                <form action="{{ route('auth.microsoft.disconnect') }}" method="POST" class="inline m-0" onsubmit="return confirmDisconnectMicrosoft(this, event)">
                                    @csrf
                                    <button type="submit" class="text-rose-600 hover:underline text-[11px] font-bold ml-1 cursor-pointer">ยกเลิก</button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('auth.microsoft.redirect') }}"
                                class="flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/40 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-xs font-semibold transition-colors">
                                <i class="fa-brands fa-microsoft text-sm"></i>
                                <span>เชื่อมต่อ Microsoft 365</span>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        @endauth

        <!-- Section: เมนูหลัก -->
        <div>
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-2 mb-1.5">เมนูหลัก</div>
            <div class="space-y-1">
                <a href="{{ route('welcome') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('welcome') ? 'bg-red-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }} transition-all">
                    <i class="fa-solid fa-house w-4 text-center"></i>
                    <span>หน้าหลัก</span>
                </a>
                <a href="{{ route('recruitment.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold {{ (request()->routeIs('recruitment.index') || request()->routeIs('recruitment.jobs.*')) ? 'bg-red-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }} transition-all">
                    <i class="fa-solid fa-briefcase w-4 text-center"></i>
                    <span>สมัครงาน (Careers)</span>
                </a>
            </div>
        </div>

        @if(Auth::check() && Auth::user()->isHrOrAdmin())
            <!-- Section: จัดการรับสมัครงาน (HA) -->
            <div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-red-500 dark:text-red-400 px-2 mb-1.5 flex items-center justify-between">
                    <span>จัดการระบบรับสมัคร (HA)</span>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('backend.recruitment.dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('backend.recruitment.dashboard') ? 'bg-red-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }} transition-all">
                        <i class="fa-solid fa-chart-pie w-4 text-center text-red-500"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('backend.recruitment.applications.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('backend.recruitment.applications.*') ? 'bg-red-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }} transition-all">
                        <i class="fa-solid fa-users w-4 text-center text-blue-500"></i>
                        <span>รายชื่อผู้สมัคร</span>
                    </a>
                    <a href="{{ route('backend.recruitment.posts.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('backend.recruitment.posts.*') ? 'bg-red-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }} transition-all">
                        <i class="fa-solid fa-bullhorn w-4 text-center text-amber-500"></i>
                        <span>ประกาศรับสมัครงาน</span>
                    </a>
                    <a href="{{ route('backend.recruitment.requests.index') }}"
                        class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('backend.recruitment.requests.*') ? 'bg-red-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }} transition-all">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-file-signature w-4 text-center text-emerald-500"></i>
                            <span>คำขอเปิดรับสมัคร</span>
                        </div>
                        @if($navPendingJobPostCount > 0)
                            <span class="px-2 py-0.5 text-[10px] font-extrabold text-amber-900 bg-amber-300 rounded-full">
                                {{ $navPendingJobPostCount }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('backend.recruitment.mail-logs.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('backend.recruitment.mail-logs.*') ? 'bg-red-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }} transition-all">
                        <i class="fa-solid fa-envelope-circle-check w-4 text-center text-purple-500"></i>
                        <span>ประวัติส่งอีเมล</span>
                    </a>
                </div>
            </div>
        @endif

        @auth
            @if($canViewDeptCandidates)
                <!-- Section: สำหรับหัวหน้าแผนก / ผู้บริหาร -->
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 px-2 mb-1.5">สำหรับหัวหน้าแผนก</div>
                    <div class="space-y-1">
                        <a href="{{ route('recruitment.reports') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-semibold {{ request()->routeIs('recruitment.reports') ? 'bg-red-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800' }} transition-all">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-user-check w-4 text-center text-emerald-600"></i>
                                <span>ผู้สมัครที่ HA ส่งมา</span>
                            </div>
                            @if($pendingDeptReviewCount > 0)
                                <span class="px-2 py-0.5 text-[10px] font-extrabold text-white bg-red-600 rounded-full">
                                    {{ $pendingDeptReviewCount }}
                                </span>
                            @endif
                        </a>
                    </div>
                </div>
            @endif

            <!-- Section: จัดการบัญชี -->
            <div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-2 mb-1.5">บัญชีและการตั้งค่า</div>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('users.profile', ['id' => auth()->id()]) }}"
                        class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition-all">
                        <i class="fa-solid fa-user-gear"></i> โปรไฟล์
                    </a>
                    <a href="{{ route('manpower-request.index') }}"
                        class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold transition-all">
                        <i class="fa-solid fa-bookmark"></i> ที่บันทึกไว้
                    </a>
                </div>
            </div>
        @endauth
    </div>

    <!-- Drawer Footer -->
    <div class="p-4 border-t border-gray-100 dark:border-gray-800 shrink-0 space-y-2.5 bg-slate-50/50 dark:bg-slate-900/50">
        @auth
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-950/30 dark:hover:bg-red-900/40 text-red-600 dark:text-red-400 text-xs font-bold transition-all cursor-pointer">
                    <i class="fa-solid fa-power-off"></i> ออกจากระบบ (Log Out)
                </button>
            </form>
        @endauth
        @guest
            <button type="button" class="login-open-btn w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-red-600 text-white text-xs font-bold tracking-wider uppercase hover:bg-red-700 transition-all shadow-md cursor-pointer">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> เข้าสู่ระบบ (Log In)
            </button>
        @endguest
    </div>
</div>

<!-- Login Modal -->
@guest
    <div id="login-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden-custom">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 relative overflow-hidden">
            <div class="flex justify-between items-center px-6 pt-5">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">เข้าสู่ระบบ HA System</h2>
                <button type="button" id="login-close-btn"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 cursor-pointer">
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
                        class="block text-xs font-semibold text-gray-700 dark:text-gray-300">รหัสพนักงาน</label>
                    <input id="employee_code" name="employee_code" type="text" autocomplete="username" required
                        class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:border-red-500 focus:ring-red-500"
                        placeholder="กรอกรหัสพนักงาน" value="{{ old('employee_code') }}">
                    @error('employee_code')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password"
                        class="block text-xs font-semibold text-gray-700 dark:text-gray-300">รหัสผ่าน</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                        class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                        <input type="checkbox" name="remember"
                            class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        จดจำฉัน
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                            class="text-xs text-red-600 hover:text-red-700">ลืมรหัสผ่าน?</a>
                    @endif
                </div>
                <div>
                    <button type="submit" class="w-full navbar-link h-10 px-4 rounded-xl font-bold cursor-pointer">เข้าสู่ระบบ</button>
                </div>
            </form>
        </div>
    </div>
@endguest

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Mobile Menu Drawer Logic ---
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const mobileProfileAvatarBtn = document.getElementById('mobile-profile-avatar-btn');
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

        if (mobileBtn) mobileBtn.addEventListener('click', openMobileMenu);
        if (mobileProfileAvatarBtn) mobileProfileAvatarBtn.addEventListener('click', openMobileMenu);
        if (mobileMenuCloseBtn) mobileMenuCloseBtn.addEventListener('click', closeMobileMenu);
        if (mobileMenuBackdrop) mobileMenuBackdrop.addEventListener('click', closeMobileMenu);

        // --- Desktop Profile Dropdown Logic ---
        const profileBtn = document.getElementById('profile-btn');
        const profileMenu = document.getElementById('profile-menu');
        if (profileBtn && profileMenu) {
            profileBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden-custom');
            });
        }

        // --- Global Click Outside to Close Menus ---
        document.addEventListener('click', function (e) {
            if (profileBtn && profileMenu && !profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.add('hidden-custom');
            }
        });

        // --- Login Modal Logic (Guest only) ---
        const loginOpenBtns = document.querySelectorAll('.login-open-btn');
        const loginModal = document.getElementById('login-modal');
        const loginCloseBtn = document.getElementById('login-close-btn');

        if (loginModal) {
            function openLoginModal() {
                closeMobileMenu();
                loginModal.classList.remove('hidden-custom');
            }
            function closeLoginModal() {
                loginModal.classList.add('hidden-custom');
            }
            loginOpenBtns.forEach(btn => btn.addEventListener('click', openLoginModal));
            if (loginCloseBtn) loginCloseBtn.addEventListener('click', closeLoginModal);
            loginModal.addEventListener('click', (e) => { if (e.target === loginModal) closeLoginModal(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeLoginModal(); });
            
            const hasErrors = {{ (isset($errors) && ($errors->has('employee_code') || $errors->has('password'))) ? 'true' : 'false' }};
            if (hasErrors) openLoginModal();
        }
    });
</script>