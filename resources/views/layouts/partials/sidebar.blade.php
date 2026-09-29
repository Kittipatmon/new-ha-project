<!-- Load FontAwesome CDN directly in sidebar to guarantee icons work -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Mobile Sidebar Backdrop -->
<div id="sidebar-backdrop" class="fixed inset-0 bg-black/50 z-20 hidden xl:hidden transition-opacity opacity-0 cursor-pointer"></div>

        <aside id="sidebar"
            class="w-[260px] sm:w-[280px] xl:w-68 flex-shrink-0 flex flex-col h-full bg-white dark:bg-[#1E2129] text-slate-700 dark:text-slate-200 border-r border-slate-200/80 dark:border-slate-800 shadow-sm z-30 fixed xl:relative transform -translate-x-full xl:translate-x-0 transition-transform duration-300 left-0 top-0 bottom-0">

            <!-- Header -->
            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-100 dark:border-slate-800/80 bg-white dark:bg-[#1E2129]">

                <div id="sidebar-logo"
                    class="flex items-center gap-3 overflow-hidden whitespace-nowrap transition-all duration-300 opacity-100">
                    <div
                        class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center shadow-md shadow-indigo-500/25 flex-shrink-0">
                        <span class="font-extrabold text-white text-lg leading-none tracking-tighter">H</span>
                    </div>
                    <a href="{{ route('welcome') }}" class="flex items-center">
                        <span class="text-xl font-bold tracking-tight text-indigo-600 dark:text-indigo-400">Kumwell</span>
                        <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider ml-1.5 pt-0.5">HA</span>
                    </a>
                </div>

                <button id="sidebar-toggle-btn" aria-label="Toggle Sidebar"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 transition-all focus:outline-none">
                    <i id="sidebar-toggle-icon" class="fa-regular fa-circle-dot text-base"></i>
                </button>
            </div>

            <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto sidebar-scroll">

                <div class="sidebar-text px-3 pt-3 pb-1 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                    Apps & Pages
                </div>

                @if(Auth::check() && Auth::user()->canAccessBackend())
                    
                    <x-sidebar.link title="หน้าหลักข้อมูล (Master Data)" icon="house-user" href="{{ route('request.data') }}" :active="request()->routeIs('request.data')" />

                    <x-sidebar.dropdown id="dashboard" title="Dashboard" icon="chart-pie" :active="request()->routeIs('leavereports.dashboard')">
                        <x-sidebar.item href="{{ route('leavereports.dashboard') }}" :active="request()->routeIs('leavereports.dashboard')">
                            ขาด ลา
                        </x-sidebar.item>
                    </x-sidebar.dropdown>

                    <x-sidebar.dropdown id="datapublic" title="ข้อมูลทั่วไป" icon="newspaper" :active="request()->routeIs('news.*') || request()->routeIs('posters.*') || request()->routeIs('hero-backgrounds.*')">
                        <x-sidebar.item href="{{ route('news.index') }}" :active="request()->routeIs('news.index')">
                            ข้อมูลข่าวสาร
                        </x-sidebar.item>
                        <x-sidebar.item href="{{ route('posters.index') }}" :active="request()->routeIs('posters.index')">
                            จัดการโปสเตอร์ประชาสัมพันธ์
                        </x-sidebar.item>
                        <x-sidebar.item href="{{ route('hero-backgrounds.index') }}" :active="request()->routeIs('hero-backgrounds.*')">
                            จัดการภาพพื้นหลัง
                        </x-sidebar.item>
                    </x-sidebar.dropdown>

                    <!-- Section Divider: HR & Requests -->
                    <div class="sidebar-text px-3 pt-5 pb-1 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        HR & Requests
                    </div>

                    <x-sidebar.dropdown id="request" title="Request Settings" icon="file-signature" :active="request()->routeIs('request-categories.*') || request()->routeIs('request-types.*') || request()->routeIs('request-subtypes.*')">
                        <x-sidebar.item href="{{ route('request-categories.index') }}" :active="request()->routeIs('request-categories.index')">ประเภทคำร้อง</x-sidebar.item>
                        <x-sidebar.item href="{{ route('request-types.index') }}" :active="request()->routeIs('request-types.index')">ตัวเลือกการร้องขอ</x-sidebar.item>
                        <x-sidebar.item href="{{ route('request-subtypes.index') }}" :active="request()->routeIs('request-subtypes.index')">ประเภทย่อย</x-sidebar.item>
                    </x-sidebar.dropdown>

                    @if(Auth::user()->canManageUsers())
                    <x-sidebar.link title="จัดการพนักงาน" icon="users-gear" href="{{ route('users.index') }}" :active="request()->routeIs('users.*')" />
                    @endif

                    <x-sidebar.link title="พิจารณาคำขอ HR" icon="clipboard-check" href="{{ route('manpower-request.index') }}" :active="request()->routeIs('manpower-request.*') || request()->routeIs('probation-evaluation.*') || request()->routeIs('interview-evaluation.*') || request()->routeIs('admin.manpower-requests.*') || request()->routeIs('admin.probation-evaluations.*') || request()->routeIs('admin.interview-evaluations.*')" />

                    <!-- Section Divider: Training -->
                    <div class="sidebar-text px-3 pt-5 pb-1 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        Training & Recruitment
                    </div>

                    <x-sidebar.dropdown id="training" title="ระบบฝึกอบรมและพัฒนาทักษะ" icon="graduation-cap" :active="request()->routeIs('backend.training.*')">
                        <x-sidebar.item href="{{ route('backend.training.index') }}" :active="request()->routeIs('backend.training.index')">รายการข้อมูลการฝึกอบรม</x-sidebar.item>
                        <x-sidebar.item href="{{ route('backend.training.applicants') }}" :active="request()->routeIs('backend.training.applicants')">จัดการผู้สมัคร</x-sidebar.item>
                    </x-sidebar.dropdown>

                    <x-sidebar.dropdown id="recruitment" title="ระบบสรรหาบุคลากร" icon="user-plus" :active="request()->routeIs('backend.recruitment.*')">
                        <x-sidebar.item href="{{ route('backend.recruitment.requests.index') }}" :active="request()->routeIs('backend.recruitment.requests.*')">คำขอเปิดรับสมัครพนักงาน</x-sidebar.item>
                        <x-sidebar.item href="{{ route('backend.recruitment.posts.index') }}" :active="request()->routeIs('backend.recruitment.posts.*')">ประกาศรับสมัครงาน</x-sidebar.item>
                        <x-sidebar.item href="{{ route('backend.recruitment.applications.index') }}" :active="request()->routeIs('backend.recruitment.applications.*')">รายชื่อผู้สมัคร</x-sidebar.item>
                        <x-sidebar.item href="{{ route('backend.recruitment.email-templates.index') }}" :active="request()->routeIs('backend.recruitment.email-templates.*')">ตั้งค่าข้อความอีเมล</x-sidebar.item>
                        <x-sidebar.item href="{{ route('backend.recruitment.mail-logs.index') }}" :active="request()->routeIs('backend.recruitment.mail-logs.*')">ประวัติการส่งอีเมล</x-sidebar.item>
                    </x-sidebar.dropdown>

                    <!-- Section Divider: System Settings -->
                    @if(Auth::user()->canManageUsers() || Auth::user()->isHrOrAdmin())
                        <div class="sidebar-text px-3 pt-5 pb-1 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                            System Settings
                        </div>

                        <x-sidebar.link title="ตั้งค่า Microsoft 365" icon="microsoft" href="{{ route('backend.settings.microsoft') }}" :active="request()->routeIs('backend.settings.microsoft*')" />
                        <x-sidebar.link title="ประวัติกิจกรรม (Audit Logs)" icon="clock-rotate-left" href="{{ route('backend.audit-logs.index') }}" :active="request()->routeIs('backend.audit-logs.*')" />
                    @endif

                @endif

            </nav>

            <div class="border-t border-slate-100 dark:border-slate-800/80 p-3 bg-white dark:bg-[#1E2129]">
                <div id="user-profile" class="flex items-center justify-between gap-3 p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all">
                    <div class="flex items-center gap-3 min-w-0">
                        <div title="{{ Auth::check() ? Auth::user()->fullname : '' }}"
                            class="relative w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold overflow-hidden shrink-0 ring-2 ring-indigo-500/20">
                            @if(Auth::check() && Auth::user()->photo_user)
                                <img src="{{ asset(Auth::user()->photo_user) }}" alt="Avatar" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                <i class="fa-solid fa-user text-xs text-slate-400 hidden"></i>
                            @else
                                <i class="fa-solid fa-user text-xs text-slate-400"></i>
                            @endif
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-1.5 ring-white dark:ring-slate-900"></span>
                        </div>

                        <div class="sidebar-text flex-1 min-w-0">
                            <p class="text-xs font-semibold text-slate-800 dark:text-slate-100 truncate leading-tight">
                                {{ Auth::check() ? Auth::user()->fullname : '' }}
                            </p>
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 truncate mt-0.5 capitalize">
                                {{ Auth::check() ? (Auth::user()->role ?? 'Admin') : '' }}
                            </p>
                        </div>
                    </div>

                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="sidebar-text flex-shrink-0">
                            @csrf
                            <button type="submit" 
                                title="ออกจากระบบ"
                                class="w-8 h-8 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 flex items-center justify-center transition-colors">
                                <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                            </button>
                        </form>
                    @endauth
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
                    var userProfile = document.getElementById('user-profile');
                    
                    var isOpen = localStorage.getItem('sidebar-open') !== 'false';
                    if (sidebar && !isOpen) {
                        sidebar.classList.remove('w-[260px]', 'sm:w-[280px]', 'xl:w-68');
                        sidebar.classList.add('w-20', 'xl:w-20', 'sm:w-20');
                        if (toggleIcon) toggleIcon.className = 'fa-regular fa-circle text-base';
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
                    }

                    var sidebarNav = document.querySelector('.sidebar-scroll');
                    var savedScrollPos = localStorage.getItem('sidebar-scroll-pos');
                    if (sidebarNav && savedScrollPos) {
                        sidebarNav.scrollTop = parseInt(savedScrollPos, 10);
                    }

                    // Instantly restore opened dropdowns before paint if sidebar is open
                    if (isOpen) {
                        var savedDropdowns = JSON.parse(localStorage.getItem('sidebar-dropdowns') || '{}');
                        Object.keys(savedDropdowns).forEach(function(id) {
                            if (savedDropdowns[id] === true) {
                                var el = document.getElementById(id);
                                if (el) {
                                    el.classList.remove('hidden');
                                    var btn = el.previousElementSibling;
                                    if (btn) {
                                        var arrow = btn.querySelector('.fa-chevron-right, .fa-chevron-down');
                                        if (arrow) arrow.classList.add('rotate-90');
                                        btn.classList.add('bg-slate-100/90', 'dark:bg-slate-800/80', 'text-indigo-600', 'dark:text-indigo-400');
                                    }
                                }
                            }
                        });
                    }
                } catch(e) {}
            })();
        </script>
