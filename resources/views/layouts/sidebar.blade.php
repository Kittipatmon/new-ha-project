<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HR System</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Prompt', sans-serif;
        }

        /* Custom Scrollbar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: #121418;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #333;
            border-radius: 10px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #D71920;
        }

        /* Helper class to hide elements via JS */
        .hidden-force {
            display: none !important;
        }

        /* Smooth transition for sidebar width */
        #sidebar {
            transition: width 0.3s ease;
        }
    </style>

    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        kumwell: {
                            red: '#D71920',
                            dark: '#121418',
                            card: '#1E2129',
                            hover: '#2A2E38'
                        }
                    },
                    width: {
                        '68': '17rem',
                    }
                }
            }
        }
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: @json(session('success')),
                    timer: 2500,
                    showConfirmButton: false
                });
            });
        @endif
    </script>
</head>

<body class="bg-gray-50 dark:bg-kumwell-dark text-gray-800 dark:text-gray-200 antialiased">

    <div class="flex h-screen overflow-hidden">

        <!-- Mobile Sidebar Backdrop -->
        <div id="sidebar-backdrop" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden transition-opacity opacity-0 cursor-pointer"></div>

        <aside id="sidebar"
            class="w-[280px] sm:w-[320px] md:w-68 flex-shrink-0 flex flex-col h-full bg-[#150f0f] text-white border-r border-gray-800 shadow-xl z-30 fixed md:relative transform -translate-x-full md:translate-x-0 transition-transform duration-300">

            <!-- Header -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-gray-800/50 bg-[#0B1120]">

                <div id="sidebar-logo"
                    class="flex items-center gap-3 overflow-hidden whitespace-nowrap transition-all duration-300 opacity-100">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-kumwell-red to-red-700 flex items-center justify-center shadow-lg shadow-red-900/20 flex-shrink-0">
                        <span class="font-bold text-white text-lg">H</span>
                    </div>
                    <a href="{{ route('welcome') }}">
                        <div class="flex flex-col leading-none">
                            <span class="text-lg font-bold tracking-wide text-white">Kumwell</span>
                            <span class="text-[10px] text-kumwell-red font-bold uppercase tracking-[0.2em]">HA
                                System</span>
                        </div>
                    </a>
                </div>

                <button id="sidebar-toggle-btn"
                    class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition-all focus:outline-none">
                    <i id="icon-bars" class="fa-solid fa-bars-staggered text-sm hidden"></i>
                    <i id="icon-chevron" class="fa-solid fa-chevron-left text-sm"></i>
                </button>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto sidebar-scroll">

                <div class="sidebar-text px-2 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Main
                    Menu</div>

                <!-- <a href="#" class="group relative flex items-center px-3 py-1 rounded-xl text-gray-400 hover:bg-gradient-to-r hover:from-kumwell-red hover:to-red-700 hover:text-white hover:shadow-lg hover:shadow-red-900/30 transition-all duration-200">
                    <i id="dashboard-icon" class="fa-solid fa-chart-pie text-sm w-6 text-center group-hover:text-white transition-colors mr-3"></i>
                    <span class="sidebar-text">Dashboard</span>
                    
                    <div class="tooltip absolute left-14 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 transition-opacity pointer-events-none z-50 whitespace-nowrap ml-2 shadow-md border border-gray-700 hidden">
                        Dashboard
                    </div>
                </a> -->
                @if(Auth::check() && Auth::user()->isHrOrAdmin())
                    <div class="relative group">
                        @php
                            $dashboardActive = request()->routeIs('leavereports.dashboard');
                        @endphp
                        <button onclick="toggleDropdown('dropdown-dashboard')"
                            class="w-full flex items-center justify-between px-3 py-1 rounded-xl transition-all duration-200 {{ $dashboardActive ? 'bg-white/5 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white' }}"
                            id="btn-dashboard">
                            <div class="flex items-center">
                                <i id="dashboard-icon"
                                    class="fa-solid fa-chart-pie text-sm w-6 text-center transition-colors mr-3 {{ $dashboardActive ? 'text-kumwell-red' : '' }}"></i>
                                <span class="sidebar-text">Dashboard</span>
                            </div>
                            <i id="arrow-dashboard"
                                class="sidebar-text fa-solid fa-chevron-down text-xs transition-transform duration-200 {{ $dashboardActive ? 'rotate-180' : '' }}"></i>
                        </button>

                        <div id="dropdown-dashboard" class="{{ $dashboardActive ? '' : 'hidden' }} pl-10 pr-2 py-1 space-y-1 transition-all duration-300">
                            <!-- <a href="#" class="block px-3 py-2 rounded-lg text-sm text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50 transition-colors">
                                            Request HR
                                        </a> -->
                            <a href="{{ route('leavereports.dashboard') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('leavereports.dashboard') ? 'bg-gray-800/80 text-kumwell-red font-bold shadow-sm' : 'text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50' }}">
                                ขาด ลา
                            </a>


                        </div>

                        <div
                            class="tooltip absolute left-14 top-2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 transition-opacity pointer-events-none z-50 whitespace-nowrap ml-2 shadow-md border border-gray-700 hidden">
                            Dashboard
                        </div>
                    </div>

                    <div class="relative group">
                        @php
                            $datapublicActive = request()->routeIs('news.*');
                        @endphp
                        <button onclick="toggleDropdown('dropdown-datapublic')"
                            class="w-full flex items-center justify-between px-3 py-1 rounded-xl transition-all duration-200 {{ $datapublicActive ? 'bg-white/5 text-white' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white' }}"
                            id="btn-datapublic">
                            <div class="flex items-center">
                                <i id="icon-datapublic" class="fa-solid fa-database text-sm w-6 text-center mr-3 {{ $datapublicActive ? 'text-kumwell-red' : '' }}"></i>
                                <span class="sidebar-text">ข้อมูลทั่วไป</span>
                            </div>
                            <i id="arrow-datapublic"
                                class="sidebar-text fa-solid fa-chevron-down text-xs transition-transform duration-200 {{ $datapublicActive ? 'rotate-180' : '' }}"></i>
                        </button>

                        <div id="dropdown-datapublic" class="{{ $datapublicActive ? '' : 'hidden' }} pl-10 pr-2 py-1 space-y-1 transition-all duration-300">
                            <a href="{{ route('news.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('news.index') ? 'bg-gray-800/80 text-kumwell-red font-bold shadow-sm' : 'text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50' }}">
                                - ข้อมูลข่าวสาร
                            </a>
                        </div>

                        <div
                            class="tooltip absolute left-14 top-2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 transition-opacity pointer-events-none z-50 whitespace-nowrap ml-2 shadow-md border border-gray-700 hidden">
                            ข้อมูลทั่วไป
                        </div>
                    </div>
                @endif

                @if(Auth::check() && Auth::user()->isHrOrAdmin())
                    <div class="relative group">
                        @php
                            $requestActive = request()->routeIs('request-categories.*') || request()->routeIs('request-types.*') || request()->routeIs('request-subtypes.*');
                        @endphp
                        <button onclick="toggleDropdown('dropdown-request')"
                            class="w-full flex items-center justify-between px-3 py-1 rounded-xl transition-all duration-200 {{ $requestActive ? 'bg-white/5 text-white' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white' }}"
                            id="btn-request">
                            <div class="flex items-center">
                                <i id="icon-request" class="fa-solid fa-file-signature text-sm w-6 text-center mr-3 {{ $requestActive ? 'text-kumwell-red' : '' }}"></i>
                                <span class="sidebar-text">Request Settings</span>
                            </div>
                            <i id="arrow-request"
                                class="sidebar-text fa-solid fa-chevron-down text-xs transition-transform duration-200 {{ $requestActive ? 'rotate-180' : '' }}"></i>
                        </button>

                        <div id="dropdown-request" class="{{ $requestActive ? '' : 'hidden' }} pl-10 pr-2 py-1 space-y-1 transition-all duration-300">
                            <a href="{{ route('request-categories.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('request-categories.index') ? 'bg-gray-800/80 text-kumwell-red font-bold shadow-sm' : 'text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50' }}">
                                - ประเภทคำร้อง
                            </a>
                            <a href="{{ route('request-types.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('request-types.index') ? 'bg-gray-800/80 text-kumwell-red font-bold shadow-sm' : 'text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50' }}">
                                - ตัวเลือกการร้องขอ
                            </a>
                            <a href="{{ route('request-subtypes.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('request-subtypes.index') ? 'bg-gray-800/80 text-kumwell-red font-bold shadow-sm' : 'text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50' }}">
                                - ประเภทย่อย
                            </a>
                        </div>

                        <div
                            class="tooltip absolute left-14 top-2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 transition-opacity pointer-events-none z-50 whitespace-nowrap ml-2 shadow-md border border-gray-700 hidden">
                            Request Settings
                        </div>
                    </div>
                @endif

                @if(Auth::check() && Auth::user()->isHrOrAdmin())
                    <div class="relative group">
                        @php
                            $hrActive = request()->routeIs('users.*') || request()->routeIs('usertypes.*') || request()->routeIs('sections.*') || request()->routeIs('divisions.*') || request()->routeIs('departments.*');
                        @endphp
                        <button onclick="toggleDropdown('dropdown-hr')"
                            class="w-full flex items-center justify-between px-3 py-1 rounded-xl transition-all duration-200 {{ $hrActive ? 'bg-white/5 text-white' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white' }}"
                            id="btn-hr">
                            <div class="flex items-center">
                                <i id="icon-hr" class="fa-solid fa-users-gear text-sm w-6 text-center mr-3 {{ $hrActive ? 'text-kumwell-red' : '' }}"></i>
                                <span class="sidebar-text">HR Settings</span>
                            </div>
                            <i id="arrow-hr"
                                class="sidebar-text fa-solid fa-chevron-down text-xs transition-transform duration-200 {{ $hrActive ? 'rotate-180' : '' }}"></i>
                        </button>

                        <div id="dropdown-hr" class="{{ $hrActive ? '' : 'hidden' }} pl-10 pr-2 py-1 space-y-1 transition-all duration-300">
                            <a href="{{ route('users.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('users.index') ? 'bg-gray-800/80 text-kumwell-red font-bold shadow-sm' : 'text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50' }}">-
                                ข้อมูลพนักงาน</a>
                            <a href="{{ route('usertypes.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('usertypes.index') ? 'bg-gray-800/80 text-kumwell-red font-bold shadow-sm' : 'text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50' }}">-
                                ข้อมูลประเภทพนักงาน</a>
                            <a href="{{ route('sections.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('sections.index') ? 'bg-gray-800/80 text-kumwell-red font-bold shadow-sm' : 'text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50' }}">-
                                ข้อมูลสายงาน</a>
                            <a href="{{ route('divisions.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('divisions.index') ? 'bg-gray-800/80 text-kumwell-red font-bold shadow-sm' : 'text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50' }}">-
                                ข้อมูลฝ่าย</a>
                            <a href="{{ route('departments.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('departments.index') ? 'bg-gray-800/80 text-kumwell-red font-bold shadow-sm' : 'text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50' }}">-
                                ข้อมูลแผนก</a>
                        </div>
                        <div
                            class="tooltip absolute left-14 top-2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 transition-opacity pointer-events-none z-50 whitespace-nowrap ml-2 shadow-md border border-gray-700 hidden">
                            HR Settings
                        </div>
                    </div>
                @endif

                @if(Auth::check() && Auth::user()->isHrOrAdmin())
                    <div class="relative group">
                        @php
                            $suggestionActive = request()->routeIs('suggestion.list');
                        @endphp
                        <button onclick="toggleDropdown('dropdown-suggestion')"
                            class="w-full flex items-center justify-between px-3 py-1 rounded-xl transition-all duration-200 {{ $suggestionActive ? 'bg-white/5 text-white' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white' }}"
                            id="btn-suggestion">
                            <div class="flex items-center">
                                <i id="icon-suggestion" class="fa-solid fa-database text-sm w-6 text-center mr-3 {{ $suggestionActive ? 'text-kumwell-red' : '' }}"></i>
                                <span class="sidebar-text">รายการร้องเรียน</span>
                            </div>
                            <i id="arrow-suggestion"
                                class="sidebar-text fa-solid fa-chevron-down text-xs transition-transform duration-200 {{ $suggestionActive ? 'rotate-180' : '' }}"></i>
                        </button>

                        <div id="dropdown-suggestion" class="{{ $suggestionActive ? '' : 'hidden' }} pl-10 pr-2 py-1 space-y-1 transition-all duration-300">
                            <!-- <a href="{{ route('suggestion.dashboard') }}"
                                            class="block px-3 py-2 rounded-lg text-sm text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50 transition-colors">
                                            - Dashboard
                                        </a> -->
                            <a href="{{ route('suggestion.list') }}"
                                class="flex justify-between items-center px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('suggestion.list') ? 'bg-gray-800/80 text-kumwell-red font-bold shadow-sm' : 'text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50' }}">
                                <span>- รับเรื่องร้องเรียน</span>
                                @php
                                    $newSuggestionsCount = \App\Models\Suggestion::where('status', 'รอรับเรื่องคำร้อง')->count();
                                @endphp
                                @if($newSuggestionsCount > 0)
                                    <span
                                        class="badge badge-error badge-sm text-white font-bold ml-2">{{ $newSuggestionsCount }}</span>
                                @endif
                            </a>
                        </div>

                        <div
                            class="tooltip absolute left-14 top-2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 transition-opacity pointer-events-none z-50 whitespace-nowrap ml-2 shadow-md border border-gray-700 hidden">
                            ข้อมูลทั่วไป
                        </div>
                    </div>
                @endif

                @if(Auth::check() && Auth::user()->isHrOrAdmin())
                    <div class="relative group">
                        @php
                            $trainingActive = request()->routeIs('backend.training.*');
                        @endphp
                        <button onclick="toggleDropdown('dropdown-training')"
                            class="w-full flex items-center justify-between px-3 py-1 rounded-xl transition-all duration-200 {{ $trainingActive ? 'bg-white/5 text-white' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white' }}"
                            id="btn-training">
                            <div class="flex items-center">
                                <i id="icon-training" class="fa-solid fa-graduation-cap text-sm w-6 text-center mr-3 {{ $trainingActive ? 'text-kumwell-red' : '' }}"></i>
                                <span class="sidebar-text">ระบบฝึกอบรมและพัฒนาทักษะ</span>
                            </div>
                            <i id="arrow-training"
                                class="sidebar-text fa-solid fa-chevron-down text-xs transition-transform duration-200 {{ $trainingActive ? 'rotate-180' : '' }}"></i>
                        </button>

                        <div id="dropdown-training" class="{{ $trainingActive ? '' : 'hidden' }} pl-10 pr-2 py-1 space-y-1 transition-all duration-300">
                            <!-- <a href="{{ route('backend.training.dashboard') }}"
                                        class="block px-3 py-2 rounded-lg text-sm text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50 transition-colors">
                                        - ภาพรวม (Dashboard)
                                    </a> -->
                            <a href="{{ route('backend.training.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('backend.training.index') ? 'bg-gray-800/80 text-kumwell-red font-bold shadow-sm' : 'text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50' }}">
                                - รายการข้อมูลการฝึกอบรม
                            </a>
                            <a href="{{ route('backend.training.applicants') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('backend.training.applicants') ? 'bg-gray-800/80 text-kumwell-red font-bold shadow-sm' : 'text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50' }}">
                                - จัดการผู้สมัคร
                            </a>
                        </div>

                        <div
                            class="tooltip absolute left-14 top-2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 transition-opacity pointer-events-none z-50 whitespace-nowrap ml-2 shadow-md border border-gray-700 hidden">
                            ระบบฝึกอบรมและพัฒนาทักษะ
                        </div>
                    </div>
                @endif

                @if(Auth::check() && Auth::user()->isHrOrAdmin())
                    <div class="relative group">
                        @php
                            $recruitmentActive = request()->routeIs('backend.recruitment.*');
                        @endphp
                        <button onclick="toggleDropdown('dropdown-recruitment')"
                            class="w-full flex items-center justify-between px-3 py-1 rounded-xl transition-all duration-200 {{ $recruitmentActive ? 'bg-white/5 text-white' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white' }}"
                            id="btn-recruitment">
                            <div class="flex items-center">
                                <i id="icon-recruitment" class="fa-solid fa-user-plus text-sm w-6 text-center mr-3 {{ $recruitmentActive ? 'text-kumwell-red' : '' }}"></i>
                                <span class="sidebar-text">ระบบสรรหาบุคลากร</span>
                            </div>
                            <i id="arrow-recruitment"
                                class="sidebar-text fa-solid fa-chevron-down text-xs transition-transform duration-200 {{ $recruitmentActive ? 'rotate-180' : '' }}"></i>
                        </button>

                        <div id="dropdown-recruitment" class="{{ $recruitmentActive ? '' : 'hidden' }} pl-10 pr-2 py-1 space-y-1 transition-all duration-300">

                            <a href="{{ route('backend.recruitment.requests.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('backend.recruitment.requests.*') ? 'bg-gray-800/80 text-kumwell-red font-bold shadow-sm' : 'text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50' }}">
                                - คำขอเปิดรับสมัครพนักงาน
                            </a>
                            <a href="{{ route('backend.recruitment.posts.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('backend.recruitment.posts.*') ? 'bg-gray-800/80 text-kumwell-red font-bold shadow-sm' : 'text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50' }}">
                                - ประกาศรับสมัครงาน
                            </a>
                            <a href="{{ route('backend.recruitment.applications.index') }}"
                                class="block px-3 py-2 rounded-lg text-sm transition-colors {{ request()->routeIs('backend.recruitment.applications.*') ? 'bg-gray-800/80 text-kumwell-red font-bold shadow-sm' : 'text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50' }}">
                                - รายชื่อผู้สมัคร
                            </a>
                        </div>

                        <div
                            class="tooltip absolute left-14 top-2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 transition-opacity pointer-events-none z-50 whitespace-nowrap ml-2 shadow-md border border-gray-700 hidden">
                            ระบบสรรหาบุคลากร
                        </div>
                    </div>
                @endif

            </nav>

            <div class="border-t border-gray-800 p-4 bg-[#0a0c10]">

                <div class="sidebar-text flex items-center justify-between mb-4">
                    <span class="text-xs font-semibold text-gray-500 uppercase">Preferences</span>
                </div>

                <div
                    class="sidebar-text flex items-center justify-between px-3 py-2 rounded-lg bg-gray-900 border border-gray-800 mb-3">
                    <div class="flex items-center gap-2 text-sm text-gray-400">
                        <i class="fa-solid fa-moon"></i>
                        <span>Dark Mode</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="dark-mode-toggle" class="sr-only peer">
                        <div
                            class="w-9 h-5 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-kumwell-red">
                        </div>
                    </label>
                </div>

                <div id="user-profile" class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-700 to-gray-900 border border-gray-600 flex items-center justify-center text-white font-bold shadow-md shrink-0 overflow-hidden">
                        @if(Auth::user()->photo_user)
                            <img src="{{ asset(Auth::user()->photo_user) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <i class="fa-solid fa-user"></i>
                        @endif
                    </div>

                    <div class="sidebar-text flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">
                            {{ Auth::user()->fullname }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ Auth::user()->employee_code }}
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        <main class="flex-1 bg-gray-50 dark:bg-kumwell-dark text-gray-900 dark:text-gray-100 overflow-y-auto relative w-full overflow-x-hidden">
            <div class="p-4 sm:p-6">
                <div
                    class="flex flex-col md:flex-row md:items-center justify-between mb-4 border-b border-gray-300/30 pb-3 gap-4">
                    <div class="flex items-center gap-3 md:gap-4">
                        <button id="mobile-sidebar-open" class="md:hidden p-2 -ml-2 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors">
                            <i class="fa-solid fa-bars text-lg"></i>
                        </button>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">
                            @yield('title', 'Dashboard')
                        </h1>
                        @hasSection('header_actions')
                            <div class="w-full sm:w-auto">
                                @yield('header_actions')
                            </div>
                        @endif
                    </div>
                    <div class="text-sm text-red-500 shrink-0">
                        <span id="current-date"></span>
                    </div>
                </div>
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // === 1. Sidebar Logic ===
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebar-toggle-btn');
        const iconBars = document.getElementById('icon-bars');
        const iconChevron = document.getElementById('icon-chevron');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');
        const sidebarLogo = document.getElementById('sidebar-logo');
        const tooltips = document.querySelectorAll('.tooltip');
        const dropdownSubmenus = document.querySelectorAll('[id^="dropdown-"]');
        const userProfile = document.getElementById('user-profile');

        let isSidebarOpen = localStorage.getItem('sidebar-open') !== 'false';

        // Initial State Restoration
        document.addEventListener('DOMContentLoaded', () => {
            restoreSidebarStates();
        });

        toggleBtn.addEventListener('click', () => {
            isSidebarOpen = !isSidebarOpen;
            localStorage.setItem('sidebar-open', isSidebarOpen);
            updateSidebarState();
        });

        function restoreSidebarStates() {
            // Restore Sidebar Width
            updateSidebarState();

            // Restore Dropdown States
            const savedDropdowns = JSON.parse(localStorage.getItem('sidebar-dropdowns') || '{}');
            Object.keys(savedDropdowns).forEach(id => {
                if (savedDropdowns[id]) {
                    const content = document.getElementById(id);
                    if (content) {
                        performToggle(id, true);
                    }
                }
            });

            // Restore Scroll Position (Do this after dropdowns to ensure height is correct)
            const sidebarNav = document.querySelector('.sidebar-scroll');
            const savedScrollPos = localStorage.getItem('sidebar-scroll-pos');
            if (sidebarNav && savedScrollPos) {
                sidebarNav.scrollTop = savedScrollPos;
            }
        }

        // Save Scroll Position on Scroll
        document.addEventListener('DOMContentLoaded', () => {
            const sidebarNav = document.querySelector('.sidebar-scroll');
            if (sidebarNav) {
                sidebarNav.addEventListener('scroll', () => {
                    localStorage.setItem('sidebar-scroll-pos', sidebarNav.scrollTop);
                });
            }
        });

        function updateSidebarState() {
            if (isSidebarOpen) {
                // Expand Sidebar
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-68');

                iconBars.classList.add('hidden');
                iconChevron.classList.remove('hidden');

                sidebarLogo.classList.remove('opacity-0', 'w-0');
                sidebarLogo.classList.add('opacity-100');

                sidebarTexts.forEach(el => el.classList.remove('hidden'));

                tooltips.forEach(t => t.classList.add('hidden'));

                userProfile.classList.remove('justify-center');

            } else {
                sidebar.classList.remove('w-68');
                sidebar.classList.add('w-20');

                iconBars.classList.remove('hidden');
                iconChevron.classList.add('hidden');

                sidebarLogo.classList.remove('opacity-100');
                sidebarLogo.classList.add('opacity-0', 'w-0');

                sidebarTexts.forEach(el => el.classList.add('hidden'));

                dropdownSubmenus.forEach(d => d.classList.add('hidden'));
                document.querySelectorAll('.fa-chevron-down').forEach(i => i.classList.remove('rotate-180'));

                tooltips.forEach(t => t.classList.remove('hidden'));

                userProfile.classList.add('justify-center');
            }
        }

        function toggleDropdown(dropdownId) {
            if (!isSidebarOpen) {
                isSidebarOpen = true;
                localStorage.setItem('sidebar-open', 'true');
                updateSidebarState();
                setTimeout(() => {
                    performToggle(dropdownId);
                }, 150);
            } else {
                performToggle(dropdownId);
            }
        }

        function performToggle(dropdownId, forceOpen = false) {
            const content = document.getElementById(dropdownId);
            if (!content) return;

            const btn = content.previousElementSibling;
            const arrow = btn.querySelector('.fa-chevron-down');

            const currentDropdowns = JSON.parse(localStorage.getItem('sidebar-dropdowns') || '{}');

            if (content.classList.contains('hidden') || forceOpen) {
                content.classList.remove('hidden');
                if (arrow) arrow.classList.add('rotate-180');
                btn.classList.add('bg-white/5', 'text-white');
                currentDropdowns[dropdownId] = true;
            } else {
                content.classList.add('hidden');
                if (arrow) arrow.classList.remove('rotate-180');
                btn.classList.remove('bg-white/5', 'text-white');
                currentDropdowns[dropdownId] = false;
            }

            localStorage.setItem('sidebar-dropdowns', JSON.stringify(currentDropdowns));
        }

        const darkModeToggle = document.getElementById('dark-mode-toggle');

        if (localStorage.getItem('theme') === 'dark' ||
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            darkModeToggle.checked = true;
        } else {
            document.documentElement.classList.remove('dark');
            darkModeToggle.checked = false;
        }

        darkModeToggle.addEventListener('change', function () {
            if (this.checked) {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            }
        });

        const dateElement = document.getElementById('current-date');
        if (dateElement) {
            const now = new Date();
            dateElement.textContent = now.toDateString();
        }

        // === Mobile Sidebar Logic ===
        const sidebarBackdrop = document.getElementById('sidebar-backdrop');
        const mobileSidebarOpen = document.getElementById('mobile-sidebar-open');
        
        let isMobileSidebarOpen = false;
        
        function closeMobileSidebar() {
            isMobileSidebarOpen = false;
            sidebar.classList.add('-translate-x-full');
            if (sidebarBackdrop) {
                sidebarBackdrop.classList.add('opacity-0');
                setTimeout(() => sidebarBackdrop.classList.add('hidden'), 300);
            }
        }
        
        if (mobileSidebarOpen) {
            mobileSidebarOpen.addEventListener('click', () => {
                isMobileSidebarOpen = true;
                sidebar.classList.remove('-translate-x-full');
                if (sidebarBackdrop) {
                    sidebarBackdrop.classList.remove('hidden');
                    setTimeout(() => sidebarBackdrop.classList.remove('opacity-0'), 10);
                }
            });
        }
        
        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', closeMobileSidebar);
        }

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: @json(session('success')),
                timer: 2500,
                showConfirmButton: false
            });
        @endif
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @yield('scripts')
    @stack('scripts')

</body>

</html>