<!-- Load FontAwesome CDN directly in sidebar to guarantee icons work -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Mobile Sidebar Backdrop -->
<div id="sidebar-backdrop" class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden transition-opacity opacity-0 cursor-pointer"></div>

        <aside id="sidebar"
            class="w-[280px] sm:w-[320px] lg:w-68 flex-shrink-0 flex flex-col h-full bg-kumwell-dark text-white border-r border-gray-800 shadow-xl z-30 fixed lg:relative transform -translate-x-full lg:translate-x-0 transition-transform duration-300 left-0 top-0 bottom-0">

            <!-- Header -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-gray-800/50 bg-gray-950">

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

                <button id="sidebar-toggle-btn" aria-label="Toggle Sidebar"
                    class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/5 transition-all focus:outline-none">
                    <i id="sidebar-toggle-icon" class="fa-solid fa-chevron-left text-sm"></i>
                </button>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-2 overflow-y-auto sidebar-scroll">

                <div class="sidebar-text px-2 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Main Menu</div>

                @if(Auth::check() && Auth::user()->isHrOrAdmin())
                    
                    <x-sidebar.link title="หน้าหลักข้อมูล (Master Data)" icon="house-user" href="{{ route('request.data') }}" :active="request()->routeIs('request.data')" />

                    <x-sidebar.dropdown id="dashboard" title="Dashboard" icon="chart-pie" :active="request()->routeIs('leavereports.dashboard')">
                        <x-sidebar.item href="{{ route('leavereports.dashboard') }}" :active="request()->routeIs('leavereports.dashboard')">
                            ขาด ลา
                        </x-sidebar.item>
                    </x-sidebar.dropdown>

                    <x-sidebar.dropdown id="datapublic" title="ข้อมูลทั่วไป" icon="database" :active="request()->routeIs('news.*')">
                        <x-sidebar.item href="{{ route('news.index') }}" :active="request()->routeIs('news.index')">
                            ข้อมูลข่าวสาร
                        </x-sidebar.item>
                    </x-sidebar.dropdown>

                    <!-- Section Divider: HR & Requests -->
                    <div class="sidebar-text px-2 pt-4 pb-1 text-xs font-semibold text-gray-500 uppercase tracking-wider border-t border-gray-800/60 mt-3 mb-1">HR & Request Management</div>

                    <x-sidebar.dropdown id="request" title="Request Settings" icon="file-signature" :active="request()->routeIs('request-categories.*') || request()->routeIs('request-types.*') || request()->routeIs('request-subtypes.*')">
                        <x-sidebar.item href="{{ route('request-categories.index') }}" :active="request()->routeIs('request-categories.index')">ประเภทคำร้อง</x-sidebar.item>
                        <x-sidebar.item href="{{ route('request-types.index') }}" :active="request()->routeIs('request-types.index')">ตัวเลือกการร้องขอ</x-sidebar.item>
                        <x-sidebar.item href="{{ route('request-subtypes.index') }}" :active="request()->routeIs('request-subtypes.index')">ประเภทย่อย</x-sidebar.item>
                    </x-sidebar.dropdown>

                    <x-sidebar.dropdown id="hr" title="HR Settings" icon="users-gear" :active="request()->routeIs('users.*') || request()->routeIs('usertypes.*') || request()->routeIs('sections.*') || request()->routeIs('divisions.*') || request()->routeIs('departments.*')">
                        <x-sidebar.item href="{{ route('users.index') }}" :active="request()->routeIs('users.index')">ข้อมูลพนักงาน</x-sidebar.item>
                        {{-- <x-sidebar.item href="{{ route('usertypes.index') }}" :active="request()->routeIs('usertypes.index')">ข้อมูลประเภทพนักงาน</x-sidebar.item>
                        <x-sidebar.item href="{{ route('sections.index') }}" :active="request()->routeIs('sections.index')">ข้อมูลสายงาน</x-sidebar.item>
                        <x-sidebar.item href="{{ route('divisions.index') }}" :active="request()->routeIs('divisions.index')">ข้อมูลฝ่าย</x-sidebar.item>
                        <x-sidebar.item href="{{ route('departments.index') }}" :active="request()->routeIs('departments.index')">ข้อมูลแผนก</x-sidebar.item> --}}
                    </x-sidebar.dropdown>

                    <x-sidebar.dropdown id="hr-consideration" title="พิจารณาคำขอ HR" icon="clipboard-check" :active="request()->routeIs('manpower-request.*') || request()->routeIs('probation-evaluation.*') || request()->routeIs('interview-evaluation.*') || request()->routeIs('admin.manpower-requests.*') || request()->routeIs('admin.probation-evaluations.*') || request()->routeIs('admin.interview-evaluations.*')">
                        <x-sidebar.item href="{{ Route::has('admin.manpower-requests.index') ? route('admin.manpower-requests.index') : route('manpower-request.index') }}" :active="request()->routeIs('manpower-request.*') || request()->routeIs('admin.manpower-requests.*')">พิจารณาคำขอ (กำลังคน)</x-sidebar.item>
                        <x-sidebar.item href="{{ Route::has('admin.probation-evaluations.index') ? route('admin.probation-evaluations.index') : route('probation-evaluation.create') }}" :active="request()->routeIs('probation-evaluation.*') || request()->routeIs('admin.probation-evaluations.*')">แบบประเมินทดลองงาน</x-sidebar.item>
                        <x-sidebar.item href="{{ Route::has('admin.interview-evaluations.index') ? route('admin.interview-evaluations.index') : route('interview-evaluation.create') }}" :active="request()->routeIs('interview-evaluation.*') || request()->routeIs('admin.interview-evaluations.*')">แบบประเมินผลสัมภาษณ์</x-sidebar.item>
                    </x-sidebar.dropdown>

                    @php
                        $newSuggestionsCount = \App\Models\Suggestion::where('status', 'รอรับเรื่องคำร้อง')->count();
                    @endphp
                    {{-- <x-sidebar.dropdown id="suggestion" title="รายการร้องเรียน" icon="database" :active="request()->routeIs('suggestion.*')">
                        <x-sidebar.item href="{{ route('suggestion.list') }}" :active="request()->routeIs('suggestion.list')" :badge="$newSuggestionsCount > 0 ? $newSuggestionsCount : null">
                            รับเรื่องร้องเรียน
                        </x-sidebar.item>
                    </x-sidebar.dropdown> --}}

                    <!-- Section Divider: Training -->
                    <div class="sidebar-text px-2 pt-4 pb-1 text-xs font-semibold text-gray-500 uppercase tracking-wider border-t border-gray-800/60 mt-3 mb-1">Training & Development</div>

                    <x-sidebar.dropdown id="training" title="ระบบฝึกอบรมและพัฒนาทักษะ" icon="graduation-cap" :active="request()->routeIs('backend.training.*')">
                        <x-sidebar.item href="{{ route('backend.training.index') }}" :active="request()->routeIs('backend.training.index')">รายการข้อมูลการฝึกอบรม</x-sidebar.item>
                        <x-sidebar.item href="{{ route('backend.training.applicants') }}" :active="request()->routeIs('backend.training.applicants')">จัดการผู้สมัคร</x-sidebar.item>
                    </x-sidebar.dropdown>

                    {{-- <x-sidebar.dropdown id="recruitment" title="ระบบสรรหาบุคลากร" icon="user-plus" :active="request()->routeIs('backend.recruitment.*')">
                        <x-sidebar.item href="{{ route('backend.recruitment.requests.index') }}" :active="request()->routeIs('backend.recruitment.requests.*')">คำขอเปิดรับสมัครพนักงาน</x-sidebar.item>
                        <x-sidebar.item href="{{ route('backend.recruitment.posts.index') }}" :active="request()->routeIs('backend.recruitment.posts.*')">ประกาศรับสมัครงาน</x-sidebar.item>
                        <x-sidebar.item href="{{ route('backend.recruitment.applications.index') }}" :active="request()->routeIs('backend.recruitment.applications.*')">รายชื่อผู้สมัคร</x-sidebar.item>
                    </x-sidebar.dropdown> --}}

                @endif

            </nav>

            <div class="border-t border-gray-800 p-3 bg-gray-950">

                <div class="sidebar-text flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-500 uppercase">Preferences</span>
                </div>

                <div id="user-profile" class="flex items-center gap-3">
                    <div title="{{ Auth::check() ? Auth::user()->fullname : '' }}"
                        class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-700 to-gray-900 border border-gray-600 flex items-center justify-center text-white font-bold shadow-md shrink-0 overflow-hidden cursor-pointer">
                        @if(Auth::check() && Auth::user()->photo_user)
                            <img src="{{ asset(Auth::user()->photo_user) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <i class="fa-solid fa-user"></i>
                        @endif
                    </div>

                    <div class="sidebar-text flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">
                            {{ Auth::check() ? Auth::user()->fullname : '' }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ Auth::check() ? Auth::user()->employee_code : '' }}
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Synchronous instant sidebar state & scroll position restoration to prevent flicker on refresh -->
        <script>
            (function() {
                try {
                    var sidebar = document.getElementById('sidebar');
                    var toggleIcon = document.getElementById('sidebar-toggle-icon');
                    var sidebarLogo = document.getElementById('sidebar-logo');
                    var sidebarTexts = document.querySelectorAll('.sidebar-text');
                    var tooltips = document.querySelectorAll('.tooltip');
                    var userProfile = document.getElementById('user-profile');
                    
                    var isOpen = localStorage.getItem('sidebar-open') !== 'false';
                    if (sidebar && !isOpen) {
                        sidebar.classList.remove('w-[280px]', 'sm:w-[320px]', 'lg:w-68');
                        sidebar.classList.add('w-20', 'lg:w-20', 'sm:w-20');
                        if (toggleIcon) toggleIcon.className = 'fa-solid fa-bars-staggered text-sm';
                        if (sidebarLogo) {
                            sidebarLogo.classList.remove('opacity-100');
                            sidebarLogo.classList.add('opacity-0', 'w-0');
                        }
                        if (sidebarTexts) sidebarTexts.forEach(function(el) { el.classList.add('hidden'); });
                        if (userProfile) userProfile.classList.add('justify-center');
                        if (sidebarNav) {
                            sidebarNav.classList.remove('overflow-y-auto');
                            sidebarNav.classList.add('overflow-visible');
                        }
                    } else if (isOpen) {
                        var savedDropdowns = JSON.parse(localStorage.getItem('sidebar-dropdowns') || '{}');
                        Object.keys(savedDropdowns).forEach(function(id) {
                            if (savedDropdowns[id]) {
                                var el = document.getElementById(id);
                                if (el) {
                                    el.classList.remove('hidden');
                                    var btn = el.previousElementSibling;
                                    if (btn) {
                                        var arrow = btn.querySelector('.fa-chevron-down');
                                        if (arrow) arrow.classList.add('rotate-180');
                                        btn.classList.add('bg-white/5', 'text-white');
                                    }
                                }
                            }
                        });
                    }

                    var sidebarNav = document.querySelector('.sidebar-scroll');
                    var savedScrollPos = localStorage.getItem('sidebar-scroll-pos');
                    if (sidebarNav && savedScrollPos) {
                        sidebarNav.scrollTop = parseInt(savedScrollPos, 10);
                    }
                } catch(e) {}
            })();
        </script>
