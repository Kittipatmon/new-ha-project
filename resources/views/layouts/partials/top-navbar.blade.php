<header class="w-full mb-4 sm:mb-6">
    <div class="w-full bg-white dark:bg-[#1E2129] rounded-xl sm:rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-800/80 px-2.5 sm:px-6 h-14 sm:h-16 flex items-center justify-between gap-1.5 sm:gap-4 transition-all">
        
        <!-- Left Side: Mobile Menu Button & Quick App Icons -->
        <div class="flex items-center gap-1 sm:gap-3 min-w-0">
            <!-- Mobile Sidebar Hamburger -->
            <button id="mobile-sidebar-open" class="xl:hidden w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg sm:rounded-xl transition-colors shrink-0">
                <i class="fa-solid fa-bars text-sm sm:text-base"></i>
            </button>

            <!-- Quick Action Shortcut Icons (Vuexy style) - Hide on smaller mobile screens, show from md up or single essential -->
            <div class="hidden md:flex items-center gap-1 sm:gap-2">
                <a href="{{ route('news.index') }}" title="ข่าวสาร / ประกาศ" class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors">
                    <i class="fa-regular fa-envelope text-sm sm:text-base"></i>
                </a>
                <a href="{{ route('request.data') }}" title="ข้อมูลระบบ / Master Data" class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors">
                    <i class="fa-regular fa-comment-dots text-sm sm:text-base"></i>
                </a>
                <a href="{{ route('manpower-request.index') }}" title="คำขอ HR" class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors">
                    <i class="fa-regular fa-square-check text-sm sm:text-base"></i>
                </a>
                <a href="{{ route('leavereports.dashboard') }}" title="ปฏิทิน ขาด ลา" class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors">
                    <i class="fa-regular fa-calendar text-sm sm:text-base"></i>
                </a>
                @if(Auth::check() && Auth::user()->canManageUsers())
                <a href="{{ route('users.index') }}" title="พนักงานที่ติดดาว / บุคลากร" class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg flex items-center justify-center text-amber-500 hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors">
                    <i class="fa-regular fa-star text-sm sm:text-base"></i>
                </a>
                @endif
            </div>
        </div>

        <!-- Right Side: Language, Dark Mode, Search, Cart/Counter, Notifications, User Profile Dropdown -->
        <div class="flex items-center gap-1 sm:gap-2 shrink-0">
            
            <!-- Language / Country Flag (Hidden on mobile) -->
            <div class="hidden md:flex items-center gap-1.5 px-2 py-1.5 rounded-lg text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60 cursor-pointer transition-colors">
                <span class="text-base leading-none">🇹🇭</span>
                <span>ไทย</span>
            </div>

            <!-- Dark / Light Mode Toggle Button -->
            <button type="button" 
                    id="top-dark-mode-btn"
                    title="สลับโหมด มืด/สว่าง"
                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 flex items-center justify-center transition-colors">
                <i id="theme-toggle-moon-icon" class="fa-regular fa-moon text-sm sm:text-base"></i>
                <i id="theme-toggle-sun-icon" class="fa-regular fa-sun text-sm sm:text-base text-amber-400 hidden"></i>
            </button>

            @if(Auth::check() && Auth::user()->canManageUsers())
            <!-- Search Quick Button (Hidden on very small mobile, visible sm+) -->
            <a href="{{ route('users.index') }}" 
               title="ค้นหา"
               class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hidden xs:flex items-center justify-center transition-colors">
                <i class="fa-solid fa-magnifying-glass text-sm sm:text-base"></i>
            </a>
            @endif

            <!-- Cart / Pending Count Shortcut Icon (Hidden on small mobile) -->
            <a href="{{ route('manpower-request.index') }}" 
               title="รายการคำขอที่รออนุมัติ"
               class="relative w-8 h-8 sm:w-9 sm:h-9 rounded-lg text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800/60 hidden sm:flex items-center justify-center transition-colors">
                <i class="fa-solid fa-cart-shopping text-sm sm:text-base"></i>
                @php
                    $pendingMpCount = \App\Models\ManpowerRequest::whereNotIn('status', ['approved', 'rejected', 'draft'])->count();
                @endphp
                @if($pendingMpCount > 0)
                    <span class="absolute -top-1 -right-1 flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-indigo-600 text-white text-[10px] font-bold shadow-sm ring-2 ring-white dark:ring-[#1E2129]">
                        {{ $pendingMpCount > 99 ? '99+' : $pendingMpCount }}
                    </span>
                @endif
            </a>

            <!-- Notification Bell Component -->
            @include('layouts.partials.notification-bell')

            <!-- User Profile Avatar & Dropdown -->
            @auth
            <div class="relative ml-1 sm:ml-2" id="navbar-user-dropdown-wrapper">
                <button type="button" 
                        onclick="toggleNavbarUserDropdown(event)"
                        class="flex items-center gap-3 p-1 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors focus:outline-none cursor-pointer">
                    <div class="hidden md:flex flex-col text-right leading-tight">
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate max-w-[130px]">
                            {{ Auth::user()->fullname ?: Auth::user()->name }}
                        </span>
                        <span class="text-[11px] text-slate-400 dark:text-slate-500 capitalize">
                            {{ Auth::user()->role ?: 'Admin' }}
                        </span>
                    </div>
                    <div class="relative w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold overflow-hidden ring-2 ring-indigo-500/20 shrink-0">
                        @if(Auth::user()->photo_user)
                            <img src="{{ asset(Auth::user()->photo_user) }}" alt="Avatar" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                            <i class="fa-solid fa-user text-xs text-slate-400 hidden"></i>
                        @else
                            <i class="fa-solid fa-user text-xs text-slate-400"></i>
                        @endif
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-1.5 ring-white dark:ring-[#1E2129]"></span>
                    </div>
                </button>

                <!-- User Dropdown Menu -->
                <div id="navbar-user-dropdown-menu" 
                     class="absolute right-0 top-full mt-2 w-56 rounded-2xl bg-white dark:bg-[#1E2129] shadow-xl border border-slate-200/80 dark:border-slate-800/80 py-2 z-50 transition-all duration-200 opacity-0 scale-95 pointer-events-none transform origin-top-right">
                    
                    <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800/80">
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">
                            {{ Auth::user()->fullname ?: Auth::user()->name }}
                        </p>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 truncate mt-0.5">
                            #{{ Auth::user()->employee_code }} • {{ Auth::user()->role ?: 'Admin' }}
                        </p>
                    </div>

                    <div class="py-1">
                        <a href="{{ route('users.profile', Auth::user()->id) }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <i class="fa-regular fa-user text-sm w-4 text-center"></i>
                            <span>โปรไฟล์ส่วนตัว</span>
                        </a>
                        <a href="{{ route('users.edit', Auth::user()->id) }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <i class="fa-solid fa-gear text-sm w-4 text-center"></i>
                            <span>แก้ไขข้อมูลพนักงาน</span>
                        </a>
                    </div>

                    <div class="border-t border-slate-100 dark:border-slate-800/80 pt-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition-colors text-left">
                                <i class="fa-solid fa-arrow-right-from-bracket text-sm w-4 text-center"></i>
                                <span>ออกจากระบบ</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endauth

        </div>
    </div>
</header>

<script>
    function toggleNavbarUserDropdown(e) {
        if (e) e.stopPropagation();
        const menu = document.getElementById('navbar-user-dropdown-menu');
        if (!menu) return;
        const isOpen = menu.classList.contains('opacity-100');
        if (isOpen) {
            menu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
            menu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            menu.style.transform = '';
        } else {
            menu.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
            menu.classList.add('opacity-100', 'scale-100', 'pointer-events-auto');

            const rect = menu.getBoundingClientRect();
            const viewportWidth = window.innerWidth;
            const margin = 12;

            if (rect.left < margin) {
                const shiftX = margin - rect.left;
                menu.style.transform = `translateX(${shiftX}px)`;
            } else if (rect.right > (viewportWidth - margin)) {
                const shiftX = (viewportWidth - margin) - rect.right;
                menu.style.transform = `translateX(${shiftX}px)`;
            } else {
                menu.style.transform = '';
            }
        }
    }

    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('navbar-user-dropdown-wrapper');
        const menu = document.getElementById('navbar-user-dropdown-menu');
        if (menu && wrapper && !wrapper.contains(e.target)) {
            menu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
            menu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
            menu.style.transform = '';
        }
    });

    // Dark Mode Toggle from Top Navbar
    function syncNavbarThemeIcons() {
        const moonIcon = document.getElementById('theme-toggle-moon-icon');
        const sunIcon = document.getElementById('theme-toggle-sun-icon');
        if (!moonIcon || !sunIcon) return;

        const isDark = document.documentElement.classList.contains('dark');
        if (isDark) {
            moonIcon.classList.add('hidden');
            sunIcon.classList.remove('hidden');
        } else {
            moonIcon.classList.remove('hidden');
            sunIcon.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        syncNavbarThemeIcons();

        const topDarkBtn = document.getElementById('top-dark-mode-btn');
        if (topDarkBtn) {
            topDarkBtn.addEventListener('click', function() {
                const isDark = document.documentElement.classList.contains('dark');
                if (isDark) {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.setAttribute('data-theme', 'light');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-theme', 'dark');
                    localStorage.setItem('theme', 'dark');
                }
                syncNavbarThemeIcons();
            });
        }
    });
</script>
