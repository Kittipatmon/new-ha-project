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
                <a href="{{ route('welcome') }}">
                    <span class="text-red-600 font-bold text-lg sm:text-xl xl:text-3xl ml-2">Kumwell</span>
                </a>
            </div>

            @php
                $navPendingJobPostCount = \App\Models\Recruitment\RecruitmentRequest::where('status', 'approved')->doesntHave('jobPosts')->count();
                $navNewApplicationsCount = \App\Models\Recruitment\Application::whereIn('status', ['new', 'submitted'])->count();
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

                    <!-- 2.1 ตรวจสอบสถานะการสมัคร -->
                    <li>
                        <a class="navbar-link {{ request()->routeIs('recruitment.track*') ? 'active' : '' }} flex items-center gap-1.5"
                            href="{{ route('recruitment.track') }}" title="ตรวจสอบสถานะการสมัครงาน">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                            <span>เช็คสถานะ</span>
                        </a>
                    </li>

                    @if(Auth::check() && Auth::user()->isHrOrAdmin())
                        <!-- 3. Dashboard -->
                        <li>
                            <a class="navbar-link {{ request()->routeIs('backend.recruitment.dashboard') ? 'active' : '' }}"
                                href="{{ route('backend.recruitment.dashboard') }}">Dashboard</a>
                        </li>

                        <!-- 4. รายชื่อผู้สมัคร -->
                        <li>
                            <a class="navbar-link {{ request()->routeIs('backend.recruitment.applications.*') ? 'active' : '' }} flex items-center gap-1.5"
                                href="{{ route('backend.recruitment.applications.index') }}">
                                <span>รายชื่อผู้สมัคร</span>
                                @if($navNewApplicationsCount > 0)
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-extrabold {{ request()->routeIs('backend.recruitment.applications.*') ? 'text-white bg-red-600' : 'text-red-700 bg-white' }} rounded-full shadow-sm animate-pulse" title="มีผู้สมัครใหม่รอคัดกรอง: {{ $navNewApplicationsCount }} คน">
                                        {{ $navNewApplicationsCount }}
                                    </span>
                                @endif
                            </a>
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

            <!-- Mobile, Tablet & Laptop Bar -->
            <div class="-me-2 flex items-center xl:hidden gap-2">
                <!-- Notification Bell -->
                @include('layouts.partials.notification-bell')

                <!-- Hamburger Button -->
                <button id="mobile-menu-btn"
                    class="flex items-center justify-center w-10 h-10 bg-slate-100/80 dark:bg-slate-800/80 rounded-xl text-slate-600 dark:text-slate-300 transition-all active:scale-95 shadow-sm hover:bg-slate-200 dark:hover:bg-slate-700"
                    aria-label="เมนู">
                    <svg id="icon-hamburger" class="h-6 w-6 block" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="icon-close-menu" class="h-6 w-6 hidden-custom" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>
</nav>

<!-- Mobile Menu Backdrop -->
<div id="mobile-menu-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[9998] hidden opacity-0 transition-opacity duration-300"></div>

<!-- Mobile & iPad Menu Drawer (Image 1 Standard) -->
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

    <!-- Nav Links (Standard Image 1 Format) -->
    <nav class="flex-1 flex flex-col">
        <a href="{{ route('welcome') }}"
            class="flex items-center px-6 py-5 text-sm font-semibold tracking-widest uppercase {{ request()->routeIs('welcome') ? 'text-red-600 font-bold bg-red-50/50 dark:bg-white/5' : 'text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5' }} border-b border-gray-100 dark:border-white/10 transition-all duration-200">
            หน้าหลัก
        </a>
        <a href="{{ route('recruitment.index') }}"
            class="flex items-center px-6 py-5 text-sm font-semibold tracking-widest uppercase {{ (request()->routeIs('recruitment.index') || request()->routeIs('recruitment.jobs.*')) ? 'text-red-600 font-bold bg-red-50/50 dark:bg-white/5' : 'text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5' }} border-b border-gray-100 dark:border-white/10 transition-all duration-200">
            สมัครงาน
        </a>
        <a href="{{ route('recruitment.track') }}"
            class="flex items-center px-6 py-5 text-sm font-semibold tracking-widest uppercase {{ request()->routeIs('recruitment.track*') ? 'text-red-600 font-bold bg-red-50/50 dark:bg-white/5' : 'text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5' }} border-b border-gray-100 dark:border-white/10 transition-all duration-200">
            เช็คสถานะการสมัคร
        </a>

        @if(Auth::check() && Auth::user()->isHrOrAdmin())
            <!-- 3. Dashboard -->
            <a href="{{ route('backend.recruitment.dashboard') }}"
                class="flex items-center justify-between px-6 py-5 text-sm font-semibold tracking-widest uppercase {{ request()->routeIs('backend.recruitment.dashboard') ? 'text-red-600 font-bold bg-red-50/50 dark:bg-white/5' : 'text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5' }} border-b border-gray-100 dark:border-white/10 transition-all duration-200">
                <span>Dashboard</span>
            </a>

            <!-- 4. รายชื่อผู้สมัคร -->
            <a href="{{ route('backend.recruitment.applications.index') }}"
                class="flex items-center justify-between px-6 py-5 text-sm font-semibold tracking-widest uppercase {{ request()->routeIs('backend.recruitment.applications.*') ? 'text-red-600 font-bold bg-red-50/50 dark:bg-white/5' : 'text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5' }} border-b border-gray-100 dark:border-white/10 transition-all duration-200">
                <span>รายชื่อผู้สมัคร</span>
                @if($navNewApplicationsCount > 0)
                    <span class="inline-flex items-center justify-center min-w-[20px] h-[20px] px-1.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full shadow-sm animate-pulse">
                        {{ $navNewApplicationsCount }}
                    </span>
                @endif
            </a>

            <!-- 5. ประกาศ -->
            <a href="{{ route('backend.recruitment.posts.index') }}"
                class="flex items-center justify-between px-6 py-5 text-sm font-semibold tracking-widest uppercase {{ request()->routeIs('backend.recruitment.posts.*') ? 'text-red-600 font-bold bg-red-50/50 dark:bg-white/5' : 'text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5' }} border-b border-gray-100 dark:border-white/10 transition-all duration-200">
                <span>ประกาศ</span>
            </a>

            <!-- 6. คำขอเปิดรับสมัคร -->
            <a href="{{ route('backend.recruitment.requests.index') }}"
                class="flex items-center justify-between px-6 py-5 text-sm font-semibold tracking-widest uppercase {{ request()->routeIs('backend.recruitment.requests.*') ? 'text-red-600 font-bold bg-red-50/50 dark:bg-white/5' : 'text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5' }} border-b border-gray-100 dark:border-white/10 transition-all duration-200">
                <span>คำขอเปิดรับสมัคร</span>
                @if($navPendingJobPostCount > 0)
                    <span class="inline-flex items-center justify-center min-w-[20px] h-[20px] px-1.5 text-xs font-bold leading-none text-amber-900 bg-amber-300 rounded-full shadow-sm animate-pulse">
                        {{ $navPendingJobPostCount }}
                    </span>
                @endif
            </a>

            <!-- 7. ประวัติส่งอีเมล -->
            <a href="{{ route('backend.recruitment.mail-logs.index') }}"
                class="flex items-center justify-between px-6 py-5 text-sm font-semibold tracking-widest uppercase {{ request()->routeIs('backend.recruitment.mail-logs.*') ? 'text-red-600 font-bold bg-red-50/50 dark:bg-white/5' : 'text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5' }} border-b border-gray-100 dark:border-white/10 transition-all duration-200">
                <span>ประวัติส่งอีเมล</span>
            </a>
        @endif

        @auth
            @if($canViewDeptCandidates)
                <a href="{{ route('recruitment.reports') }}"
                    class="flex items-center justify-between px-6 py-5 text-sm font-semibold tracking-widest uppercase {{ request()->routeIs('recruitment.reports') ? 'text-red-600 font-bold bg-red-50/50 dark:bg-white/5' : 'text-gray-700 hover:text-red-600 hover:bg-red-50 dark:text-white/70 dark:hover:text-white dark:hover:bg-white/5' }} border-b border-gray-100 dark:border-white/10 transition-all duration-200">
                    <span>ผู้สมัครที่ HA ส่งมา</span>
                    @if($pendingDeptReviewCount > 0)
                        <span class="inline-flex items-center justify-center min-w-[20px] h-[20px] px-1.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full shadow-sm">
                            {{ $pendingDeptReviewCount }}
                        </span>
                    @endif
                </a>
            @endif
        @endauth
    </nav>

    <!-- Footer: Auth/Guest Actions (Standard Image 1 Format) -->
    <div class="px-6 pb-8 pt-4 mt-auto space-y-3">
        @auth
            <div class="flex items-center gap-3 mb-4">
                <div class="w-11 h-11 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0 border border-gray-200 dark:border-white/10">
                    @if(Auth::user()->photo_user && !str_contains(Auth::user()->photo_user, 'pngegg') && file_exists(public_path(Auth::user()->photo_user)))
                        <img src="{{ asset(Auth::user()->photo_user) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <i class="fa-solid fa-user text-slate-500 dark:text-slate-400 text-base"></i>
                    @endif
                </div>
                <div class="min-w-0">
                    <div class="text-gray-900 dark:text-white font-bold text-sm truncate">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                    <div class="text-red-600 dark:text-red-400 text-[10px] uppercase tracking-wider font-bold">Authorized User</div>
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