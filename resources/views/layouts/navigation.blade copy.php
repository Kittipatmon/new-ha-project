<style>
    .navbar-link {
        transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 8px 0 rgba(220, 38, 38, 0.08);
        color: #fff;
        border: 1px solid transparent;
        background: linear-gradient(90deg, rgb(220, 38, 38) 0%, rgb(168, 12, 12) 100%);
        font-size: 14px;
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
        top: 100%; /* ปรับตำแหน่งตามต้องการ */
    }

    /* Select2 overrides for modal */
    .select2-container { width: 100% !important; }
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 6px 8px;
        border: 1px solid #dc2626; /* red-600 */
        border-radius: 0.375rem; /* rounded-md */
        background: linear-gradient(90deg, rgb(220,38,38) 0%, rgb(168,12,12) 100%);
        color: #fff;
        box-shadow: 0 2px 6px rgba(220,38,38,0.25);
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered { color:#fff; }
    .select2-container--default .select2-selection--single .select2-selection__placeholder { color: #f9fafb; opacity: .8; }
    .dark .select2-container--default .select2-selection--single {
        background: linear-gradient(90deg, #991b1b 0%, #7f1d1d 100%);
        border-color: #7f1d1d;
        color: #fff;
        box-shadow: 0 2px 6px rgba(153,27,27,0.35);
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    /* Dropdown z-index so it appears above modal */
    .select2-container .select2-dropdown { z-index: 100000; }

    /* Custom Select2 Dropdown Item Colors */
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: linear-gradient(90deg, rgb(220,38,38) 0%, rgb(168,12,12) 100%) !important;
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
<nav class="border-b border-gray-100  dark:border-gray-700 shadow-xl">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('welcome') }}">
                        <span class="text-red-600 font-bold text-lg sm:text-xl lg:text-3xl ml-2">Kumwell</span>
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">

                <div class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="theme-toggle" data-theme-toggle class="toggle theme-controller" />
                        
                        <span class="text-sm" id="icon-sun">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sun-icon lucide-sun">
                                <circle cx="12" cy="12" r="4" />
                                <path d="M12 2v2" />
                                <path d="M12 20v2" />
                                <path d="m4.93 4.93 1.41 1.41" />
                                <path d="m17.66 17.66 1.41 1.41" />
                                <path d="M2 12h2" />
                                <path d="M20 12h2" />
                                <path d="m6.34 17.66-1.41 1.41" />
                                <path d="m19.07 4.93-1.41 1.41" />
                            </svg>
                        </span>
                        <span class="text-sm hidden-custom" id="icon-moon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-moon-icon lucide-moon">
                                <path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401" />
                            </svg>
                        </span>
                    </label>

                    <ul class="hidden lg:flex space-x-1 items-center">
                        <li>
                            <a class="navbar-link px-6 py-2 text-base text-black rounded-xl shadow transition" href="{{ route('welcome') }}">หน้าหลัก</a>
                        </li>
                        <!-- <li>
                            <a class="navbar-link px-6 py-2 text-base text-black rounded-xl shadow transition" href="{{ route('welcome.system') }}">บริการ</a>
                        </li> -->
                        <li>
                            <a class="navbar-link px-6 py-2 text-base text-black rounded-xl shadow transition" href="{{ route('dashboard') }}">HA Guide Book</a>
                        </li>
                        <li>
                            <a class="navbar-link px-6 py-2 text-base text-black rounded-xl shadow transition" href="{{ route('dashboard') }}">Calendar</a>
                        </li>
                        <!-- <li>
                            <a class="navbar-link px-6 py-2 text-base text-black rounded-xl shadow transition" href="{{ route('dashboard') }}">รับสมัครงาน</a>
                        </li>
                        <li>
                            <a class="navbar-link px-6 py-2 text-base text-black rounded-xl shadow transition" href="{{ route('dashboard') }}">อบรม / สัมมนา</a>
                        </li> -->
                        
                        <!-- <li class="relative group">
                            <button type="button" id="about-btn"
                                class="navbar-link px-4 py-1 text-base text-black rounded-xl shadow flex items-center transition dropdown-toggle mr-1">
                                เกี่ยวกับเรา
                                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <ul id="about-menu"
                                class="py-2 dropdown-menu absolute left-0 top-full mt-2 w-60 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 opacity-0 invisible transition-all duration-200">
                                <li><a class="block px-4 py-2 text-red-700 hover:bg-red-100 rounded-lg" href="#">
                                    <i class="fa-solid fa-newspaper mr-1"></i> นโยบาย/การดำเนินงาน</a>
                                </li>
                                <li><a class="block px-4 py-2 text-red-700 hover:bg-red-100 rounded-lg" href="#">
                                    <i class="fa fa-phone mr-2"></i>เบอร์ติดต่อภายใน</a>
                                </li>
                                <li><a class="block px-4 py-2 text-red-700 hover:bg-red-100 rounded-b-lg" href="#">
                                    <i class="fa fa-comment mr-2"></i>แสดงความคิดเห็น</a>
                                </li>
                            </ul>
                        </li> -->
                    </ul>
                </div>

                <div class="relative ml-1">
                    @guest
                    <button type="button" id="login-open-btn" class="navbar-link px-4 py-2 text-base text-black rounded-xl shadow transition">
                        <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i>Login
                    </button>
                    @endguest
                    @auth

                    <button type="button" id="profile-btn" class="navbar-link px-3 py-2 text-black rounded-xl shadow flex items-center transition">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-user-round-icon lucide-circle-user-round">
                                <path d="M18 20a6 6 0 0 0-12 0" />
                                <circle cx="12" cy="10" r="4" />
                                <circle cx="12" cy="12" r="10" />
                            </svg>
                        </div>
                        <div class="ms-1">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                    <div id="profile-menu" class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden-custom">
                        <a href="{{ route('users.profile', ['id' => auth()->id()]) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            {{ __('Profile') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </a>
                        </form>
                    </div>
                    @endauth
                </div>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button id="mobile-menu-btn" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg id="icon-hamburger" class="h-6 w-6 block" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="icon-close-menu" class="h-6 w-6 hidden-custom" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div id="mobile-menu" class="hidden-custom sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 border-indigo-400 text-base font-medium text-indigo-700 bg-indigo-50 focus:outline-none focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700 transition duration-150 ease-in-out">
                {{ __('Dashboard') }}
            </a>
        </div>

        @auth
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->first_name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                    {{ __('Profile') }}
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </div>
        @endauth

        @guest
        <div class="pt-4 pb-4 border-t border-gray-200">
            <div class="px-4">
                <button type="button" id="login-open-btn" class="w-full navbar-link px-4 py-2 text-base rounded-xl shadow transition">
                    <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i>Login
                </button>
            </div>
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
            <button type="button" id="login-close-btn" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('login') }}" class="px-6 pb-6 pt-4 space-y-4">
            @csrf
            <div>
                <label for="employee_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">รหัสพนักงาน</label>
                <select id="employee_code" name="employee_code" required
                       class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-900 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="" disabled selected>-- เลือกรหัสพนักงาน --</option>
                    @isset($employees)
                        @foreach($employees as $employee)
                            <option value="{{ $employee->employee_code }}" {{ old('employee_code') == $employee->employee_code ? 'selected' : '' }}>
                                {{ $employee->employee_code }} - {{ $employee->first_name }} {{ $employee->last_name }}
                            </option>
                        @endforeach
                    @endisset
                </select>
                <!-- <input id="employee_code" name="employee_code" type="text" autocomplete="username" required
                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-red-500 focus:ring-red-500" value="{{ old('employee_code') }}"> -->
                @error('employee_code')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">รหัสผ่าน</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required
                       class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-red-500 focus:ring-red-500">
                @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    จดจำฉัน
                </label>
                <!-- Placeholder for forgot password route if available -->
                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs text-red-600 hover:text-red-700">ลืมรหัสผ่าน?</a>
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
    // Load jQuery & Select2 via CDN if not already loaded
    (function injectSelect2(){
        const hasJQuery = typeof window.jQuery !== 'undefined';
        function addScript(src, cb){ const s=document.createElement('script'); s.src=src; s.onload=cb; document.head.appendChild(s); }
        function addStyle(href){ const l=document.createElement('link'); l.rel='stylesheet'; l.href=href; document.head.appendChild(l); }
        addStyle('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
        if(!hasJQuery){
            addScript('https://code.jquery.com/jquery-3.6.0.min.js', function(){
                addScript('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', initSelect2);
            });
        } else {
            addScript('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', initSelect2);
        }
        function initSelect2(){
            if(!window.jQuery) return; // safety
            jQuery(function($){
                const $emp = $('#employee_code');
                if($emp.length && !$emp.hasClass('select2-initialized')){
                    $emp.addClass('select2-initialized').select2({
                        placeholder: '-- เลือกรหัสพนักงาน --',
                        width: '100%',
                        dropdownParent: $('#login-modal'),
                        language: {
                            noResults: function(){ return 'ไม่พบข้อมูล'; }
                        }
                    });
                }
            });
        }
    })();

    document.addEventListener('DOMContentLoaded', function () {
        // Theme icons sync (global script already handles persistence)
        const themeToggle = document.getElementById('theme-toggle');
        const iconSun = document.getElementById('icon-sun');
        const iconMoon = document.getElementById('icon-moon');
        function syncIcons(isDark){
            iconSun.classList.toggle('hidden-custom', isDark);
            iconMoon.classList.toggle('hidden-custom', !isDark);
        }
        syncIcons(document.documentElement.classList.contains('dark'));
        themeToggle.addEventListener('change', () => syncIcons(themeToggle.checked));

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
            aboutBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if(aboutMenu.classList.contains('invisible')) {
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
            function openLoginModal(){
                loginModal.classList.remove('hidden-custom');
            }
            function closeLoginModal(){
                loginModal.classList.add('hidden-custom');
            }
            loginOpenBtn.addEventListener('click', openLoginModal);
            loginCloseBtn.addEventListener('click', closeLoginModal);
            loginModal.addEventListener('click', (e) => { if(e.target === loginModal) closeLoginModal(); });
            document.addEventListener('keydown', (e) => { if(e.key === 'Escape') closeLoginModal(); });
            // Auto-open if validation errors exist
            const hasErrors = {{ ($errors->has('employee_code') || $errors->has('password')) ? 'true' : 'false' }};
            if(hasErrors) openLoginModal();
        }
    });
</script>