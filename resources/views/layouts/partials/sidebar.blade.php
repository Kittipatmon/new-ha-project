        <!-- Mobile Sidebar Backdrop -->
        <div id="sidebar-backdrop" class="fixed inset-0 bg-black/50 z-20 hidden md:hidden transition-opacity opacity-0 cursor-pointer"></div>

        <aside id="sidebar"
            class="w-[280px] sm:w-[320px] md:w-68 flex-shrink-0 flex flex-col h-full bg-kumwell-dark text-white border-r border-gray-800 shadow-xl z-30 fixed md:relative transform -translate-x-full md:translate-x-0 transition-transform duration-300">

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
                    <i id="icon-bars" class="fa-solid fa-bars-staggered text-sm hidden"></i>
                    <i id="icon-chevron" class="fa-solid fa-chevron-left text-sm"></i>
                </button>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto sidebar-scroll">

                <div class="sidebar-text px-2 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Main Menu</div>

                @if(Auth::check() && Auth::user()->isHrOrAdmin())
                    
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

                    <x-sidebar.dropdown id="request" title="Request Settings" icon="file-signature" :active="request()->routeIs('request-categories.*') || request()->routeIs('request-types.*') || request()->routeIs('request-subtypes.*')">
                        <x-sidebar.item href="{{ route('request-categories.index') }}" :active="request()->routeIs('request-categories.index')">ประเภทคำร้อง</x-sidebar.item>
                        <x-sidebar.item href="{{ route('request-types.index') }}" :active="request()->routeIs('request-types.index')">ตัวเลือกการร้องขอ</x-sidebar.item>
                        <x-sidebar.item href="{{ route('request-subtypes.index') }}" :active="request()->routeIs('request-subtypes.index')">ประเภทย่อย</x-sidebar.item>
                    </x-sidebar.dropdown>

                    <x-sidebar.dropdown id="hr" title="HR Settings" icon="users-gear" :active="request()->routeIs('users.*') || request()->routeIs('usertypes.*') || request()->routeIs('sections.*') || request()->routeIs('divisions.*') || request()->routeIs('departments.*')">
                        <x-sidebar.item href="{{ route('users.index') }}" :active="request()->routeIs('users.index')">ข้อมูลพนักงาน</x-sidebar.item>
                        <x-sidebar.item href="{{ route('usertypes.index') }}" :active="request()->routeIs('usertypes.index')">ข้อมูลประเภทพนักงาน</x-sidebar.item>
                        <x-sidebar.item href="{{ route('sections.index') }}" :active="request()->routeIs('sections.index')">ข้อมูลสายงาน</x-sidebar.item>
                        <x-sidebar.item href="{{ route('divisions.index') }}" :active="request()->routeIs('divisions.index')">ข้อมูลฝ่าย</x-sidebar.item>
                        <x-sidebar.item href="{{ route('departments.index') }}" :active="request()->routeIs('departments.index')">ข้อมูลแผนก</x-sidebar.item>
                    </x-sidebar.dropdown>

                    @php
                        $newSuggestionsCount = \App\Models\Suggestion::where('status', 'รอรับเรื่องคำร้อง')->count();
                    @endphp
                    {{-- <x-sidebar.dropdown id="suggestion" title="รายการร้องเรียน" icon="database" :active="request()->routeIs('suggestion.*')">
                        <x-sidebar.item href="{{ route('suggestion.list') }}" :active="request()->routeIs('suggestion.list')" :badge="$newSuggestionsCount > 0 ? $newSuggestionsCount : null">
                            รับเรื่องร้องเรียน
                        </x-sidebar.item>
                    </x-sidebar.dropdown> --}}

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

            <div class="border-t border-gray-800 p-4 bg-gray-950">

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
