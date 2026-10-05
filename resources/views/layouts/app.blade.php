<!DOCTYPE html>
<html lang="en">

<head>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HR System</title>

    <!-- Immediate Theme Restoration to prevent flash and keep state across pages -->
    <script>
        (function() {
            try {
                var storedTheme = localStorage.getItem('theme');
                var isDark = storedTheme === 'dark' || (!storedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches);
                if (isDark) {
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.setAttribute('data-theme', 'light');
                }
            } catch(e) {}
        })();
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap');

        body, button, input, select, textarea, .font-sans, h1, h2, h3, h4, h5, h6, label, span, div {
            font-family: 'Prompt', 'Kanit', sans-serif !important;
        }

        /* Ensure dropdown select icons never touch or overlap text */
        select:not([multiple]):not([size]) {
            padding-right: 2.5rem !important;
        }

        /* Scale down entire UI for Notebooks (1024px - 1600px) */
        @media (min-width: 1024px) and (max-width: 1600px) {
            html {
                font-size: 13.5px;
            }
        }

        /* Hide Scrollbar */
        .sidebar-scroll::-webkit-scrollbar {
            display: none;
            width: 0px;
            height: 0px;
        }

        .sidebar-scroll {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }

        /* Helper class to hide elements via JS */
        .hidden-force,
        .sidebar-text.hidden {
            display: none !important;
        }

        /* Smooth transition for sidebar width */
        #sidebar {
            transition: width 0.3s ease;
        }

        /* Ensure SweetAlert2 toast & popups stay on top of all headers/navbars across all pages */
        .swal2-container,
        .swal2-toast-container {
            z-index: 9999999 !important;
        }
        .swal2-container.swal2-top-end,
        .swal2-container.swal2-top-right {
            top: 15px !important;
            right: 15px !important;
        }

        /* ========================================================
           Flatpickr Mobile & iPad Responsive Optimization
           ======================================================== */
        input.flatpickr-input,
        .datepicker-th {
            font-size: 16px !important;
        }

        @media (max-width: 640px) {
            .flatpickr-calendar {
                position: fixed !important;
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                margin: 0 !important;
                width: 92vw !important;
                max-width: 360px !important;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4) !important;
                border-radius: 18px !important;
                padding: 12px 8px !important;
                z-index: 999999 !important;
            }

            .flatpickr-calendar.open::before {
                content: "";
                position: fixed;
                top: -100vh;
                left: -100vw;
                width: 300vw;
                height: 300vh;
                background: rgba(15, 23, 42, 0.5);
                backdrop-filter: blur(2px);
                -webkit-backdrop-filter: blur(2px);
                z-index: -1;
            }

            .flatpickr-calendar::before,
            .flatpickr-calendar::after {
                display: none !important;
            }

            .flatpickr-day {
                max-width: none !important;
                height: 42px !important;
                line-height: 42px !important;
                font-size: 15px !important;
                font-weight: 500 !important;
                border-radius: 10px !important;
                margin: 2px 0 !important;
            }

            .flatpickr-current-month {
                font-size: 110% !important;
            }

            .flatpickr-current-month .cur-month {
                font-weight: 600 !important;
            }
        }

        @media (min-width: 641px) and (max-width: 1024px) {
            .flatpickr-calendar {
                width: 350px !important;
                border-radius: 14px !important;
                padding: 10px !important;
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15) !important;
            }

            .flatpickr-day {
                height: 40px !important;
                line-height: 40px !important;
                font-size: 14px !important;
            }

            .flatpickr-current-month {
                font-size: 105% !important;
            }
        }
        /* Ensure dropdown select icons never touch or overlap text */
        select:not([multiple]):not([size]) {
            padding-right: 2.5rem !important;
        }
    </style>

    <script>
        // Ensure Flatpickr globally defaults to disableMobile: true
        if (typeof window !== 'undefined') {
            window.addEventListener('DOMContentLoaded', function() {
                if (typeof flatpickr !== 'undefined' && flatpickr.setDefaults) {
                    flatpickr.setDefaults({ disableMobile: true });
                }
            });
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 dark:bg-kumwell-dark text-gray-800 dark:text-gray-200 antialiased">

    @if(request()->routeIs('welcome') || request()->routeIs('news.newsAll') || request()->routeIs('news.detail') || request()->routeIs('users.profile'))
        <div class="min-h-screen">
            @yield('content')
            {{ $slot ?? '' }}
        </div>
    @elseif(request()->routeIs('manpower-request.*') || request()->routeIs('probation-evaluation.*') || request()->routeIs('interview-evaluation.*'))
        {{-- HR Forms layout: ใช้ layouts.manpower --}}
        <div class="min-h-screen flex flex-col bg-gray-100 dark:bg-gray-900">
            @include('layouts.manpower.navigation')

            <div class="pt-16 sm:pt-20 flex-1 flex flex-col">
                @isset($header)
                    <header class="bg-white dark:bg-gray-800 shadow">
                        <div class="max-w-[1700px] mx-auto py-5 px-4 sm:px-6 lg:px-8 xl:px-10">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="flex-1 mb-8 px-3 sm:px-6">
                    @yield('content')
                    {{ $slot ?? '' }}
                </main>
            </div>

            @include('layouts.footer')
        </div>
    @else
        <div class="flex h-screen overflow-hidden">

            @include('layouts.partials.sidebar')

            <main class="flex-1 bg-slate-100/70 dark:bg-kumwell-dark text-gray-900 dark:text-gray-100 overflow-y-auto relative w-full overflow-x-hidden">
                <div class="p-2.5 sm:p-6">
                    {{-- Floating Vuexy Top Navbar --}}
                    @include('layouts.partials.top-navbar')

                    {{-- Optional Page Title Bar if defined --}}
                    @hasSection('title')
                    <div class="flex items-center justify-between mb-4 gap-3">
                        <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 dark:text-white truncate">
                            @yield('title')
                        </h1>
                        @hasSection('header_actions')
                            <div class="shrink-0">
                                @yield('header_actions')
                            </div>
                        @endif
                    </div>
                    @endif

                    @isset($header)
                        <div class="mb-4 pb-2">
                            {{ $header }}
                        </div>
                    @endisset

                    @yield('content')
                    {{ $slot ?? '' }}
                </div>
            </main>
        </div>
    @endif

    <script>
        // === 1. Sidebar Logic ===
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebar-toggle-btn');
        const toggleIcon = document.getElementById('sidebar-toggle-icon');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');
        const sidebarLogo = document.getElementById('sidebar-logo');
        const tooltips = document.querySelectorAll('.tooltip');
        const dropdownSubmenus = document.querySelectorAll('[id^="dropdown-"]');
        const userProfile = document.getElementById('user-profile');

        let isSidebarOpen = localStorage.getItem('sidebar-open') !== 'false';

        // Initial State Restoration
        document.addEventListener('DOMContentLoaded', () => {
            if (sidebar) {
                restoreSidebarStates();
            }
        });

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                isSidebarOpen = !isSidebarOpen;
                localStorage.setItem('sidebar-open', isSidebarOpen);
                updateSidebarState();
            });
        }

        function restoreSidebarStates() {
            // Restore Sidebar Width
            updateSidebarState();

            // Restore dropdown states from localStorage
            if (isSidebarOpen) {
                try {
                    const savedDropdowns = JSON.parse(localStorage.getItem('sidebar-dropdowns') || '{}');
                    Object.keys(savedDropdowns).forEach(id => {
                        if (savedDropdowns[id] === true) {
                            performToggle(id, true);
                        }
                    });
                } catch(e) {}
            }

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
            if (!sidebar) return;
            const sidebarNav = document.querySelector('.sidebar-scroll');
            if (isSidebarOpen) {
                // Expand Sidebar
                sidebar.classList.remove('w-20', 'xl:w-20', 'sm:w-20');
                sidebar.classList.add('w-[260px]', 'sm:w-[280px]', 'xl:w-68');

                if (toggleIcon) toggleIcon.className = 'fa-regular fa-circle-dot text-base';

                sidebarLogo.classList.remove('opacity-0', 'w-0', 'hidden');
                sidebarLogo.classList.add('opacity-100');

                sidebarTexts.forEach(el => el.classList.remove('hidden'));

                userProfile.classList.remove('justify-center');

                if (sidebarNav) {
                    sidebarNav.classList.remove('overflow-visible');
                    sidebarNav.classList.add('overflow-y-auto');
                }

            } else {
                sidebar.classList.remove('w-68', 'xl:w-68', 'w-[260px]', 'sm:w-[280px]', 'w-[280px]', 'sm:w-[320px]');
                sidebar.classList.add('w-20', 'xl:w-20', 'sm:w-20');

                if (toggleIcon) toggleIcon.className = 'fa-regular fa-circle text-base';

                sidebarLogo.classList.remove('opacity-100');
                sidebarLogo.classList.add('opacity-0', 'w-0');

                sidebarTexts.forEach(el => el.classList.add('hidden'));

                dropdownSubmenus.forEach(d => d.classList.add('hidden'));
                document.querySelectorAll('.fa-chevron-right, .fa-chevron-down').forEach(i => i.classList.remove('rotate-90', 'rotate-180'));

                userProfile.classList.add('justify-center');

                if (sidebarNav) {
                    sidebarNav.classList.remove('overflow-y-auto');
                    sidebarNav.classList.add('overflow-visible');
                }
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
            const arrow = btn.querySelector('.fa-chevron-right, .fa-chevron-down');

            const currentDropdowns = JSON.parse(localStorage.getItem('sidebar-dropdowns') || '{}');

            if (content.classList.contains('hidden') || forceOpen) {
                content.classList.remove('hidden');
                if (arrow) arrow.classList.add('rotate-90');
                btn.classList.add('bg-slate-100/90', 'dark:bg-slate-800/80', 'text-indigo-600', 'dark:text-indigo-400');
                currentDropdowns[dropdownId] = true;
            } else {
                content.classList.add('hidden');
                if (arrow) arrow.classList.remove('rotate-90');
                btn.classList.remove('bg-slate-100/90', 'dark:bg-slate-800/80', 'text-indigo-600', 'dark:text-indigo-400');
                currentDropdowns[dropdownId] = false;
            }

            localStorage.setItem('sidebar-dropdowns', JSON.stringify(currentDropdowns));
        }

        const darkModeToggle = document.getElementById('dark-mode-toggle');

        if (darkModeToggle) {
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
        }

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
            if (!sidebar) return;
            isMobileSidebarOpen = false;
            sidebar.classList.add('-translate-x-full');
            if (sidebarBackdrop) {
                sidebarBackdrop.classList.add('opacity-0');
                setTimeout(() => sidebarBackdrop.classList.add('hidden'), 300);
            }
        }
        
        if (mobileSidebarOpen) {
            mobileSidebarOpen.addEventListener('click', () => {
                if (!sidebar) return;
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

        @if(session('error') || session('swal_error'))
            Swal.fire({
                icon: 'error',
                title: 'ไม่มีสิทธิ์เข้าถึง',
                text: @json(session('swal_error') ?? session('error')),
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'ตกลง'
            });
        @endif

        @if(session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'แจ้งเตือน',
                text: @json(session('warning')),
                confirmButtonColor: '#f59e0b',
                confirmButtonText: 'รับทราบ'
            });
        @endif

        @if(session('info'))
            Swal.fire({
                icon: 'info',
                title: 'ข้อมูล',
                text: @json(session('info')),
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'ตกลง'
            });
        @endif
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('js/backend-datatable.js') }}"></script>
    @yield('scripts')
    @stack('scripts')

</body>

</html>