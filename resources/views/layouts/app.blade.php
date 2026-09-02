<!DOCTYPE html>
<html lang="en">

<head>
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HR System</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Prompt', sans-serif;
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
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 dark:bg-kumwell-dark text-gray-800 dark:text-gray-200 antialiased">

    @if(request()->routeIs('welcome') || request()->routeIs('news.newsAll') || request()->routeIs('news.detail') || request()->routeIs('users.profile'))
        <div class="min-h-screen">
            @yield('content')
            {{ $slot ?? '' }}
        </div>
    @elseif(request()->routeIs('manpower-request.*') || request()->routeIs('probation-evaluation.*') || request()->routeIs('interview-evaluation.*'))
        {{-- HR Forms layout: top navigation bar (no sidebar) using layouts.m_p --}}
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.m_p.navigation')

            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                @yield('content')
                {{ $slot ?? '' }}
            </main>
        </div>
    @else
        <div class="flex h-screen overflow-hidden">

            @include('layouts.partials.sidebar')

            <main class="flex-1 bg-gray-50 dark:bg-kumwell-dark text-gray-900 dark:text-gray-100 overflow-y-auto relative w-full overflow-x-hidden">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4 border-b border-gray-300/30 pb-3 gap-3">
                        <div class="flex items-center gap-2 sm:gap-4 min-w-0">
                            <button id="mobile-sidebar-open" class="lg:hidden p-2 -ml-1 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-colors shrink-0">
                                <i class="fa-solid fa-bars text-lg"></i>
                            </button>
                            <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 dark:text-white truncate">
                                @yield('title', 'Dashboard')
                            </h1>
                            @hasSection('header_actions')
                                <div class="hidden sm:block shrink-0">
                                    @yield('header_actions')
                                </div>
                            @endif
                        </div>
                        <div class="text-sm shrink-0 flex items-center gap-3">
                            <span id="current-date" class="hidden sm:inline-block text-xs font-semibold text-red-500"></span>
                            @include('layouts.partials.notification-bell')
                        </div>
                    </div>
                    @isset($header)
                        <div class="mb-4 border-b border-gray-300/30 pb-3">
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

            // Restore Dropdown States ONLY if sidebar is expanded
            if (isSidebarOpen) {
                const savedDropdowns = JSON.parse(localStorage.getItem('sidebar-dropdowns') || '{}');
                Object.keys(savedDropdowns).forEach(id => {
                    if (savedDropdowns[id]) {
                        const content = document.getElementById(id);
                        if (content) {
                            performToggle(id, true);
                        }
                    }
                });
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
                sidebar.classList.remove('w-20', 'lg:w-20', 'sm:w-20');
                sidebar.classList.add('w-[280px]', 'sm:w-[320px]', 'lg:w-68');

                if (toggleIcon) toggleIcon.className = 'fa-solid fa-chevron-left text-sm';

                sidebarLogo.classList.remove('opacity-0', 'w-0', 'hidden');
                sidebarLogo.classList.add('opacity-100');

                sidebarTexts.forEach(el => el.classList.remove('hidden'));

                userProfile.classList.remove('justify-center');

                if (sidebarNav) {
                    sidebarNav.classList.remove('overflow-visible');
                    sidebarNav.classList.add('overflow-y-auto');
                }

            } else {
                sidebar.classList.remove('w-68', 'lg:w-68', 'w-[280px]', 'sm:w-[320px]');
                sidebar.classList.add('w-20', 'lg:w-20', 'sm:w-20');

                if (toggleIcon) toggleIcon.className = 'fa-solid fa-bars-staggered text-sm';

                sidebarLogo.classList.remove('opacity-100');
                sidebarLogo.classList.add('opacity-0', 'w-0');

                sidebarTexts.forEach(el => el.classList.add('hidden'));

                dropdownSubmenus.forEach(d => d.classList.add('hidden'));
                document.querySelectorAll('.fa-chevron-down').forEach(i => i.classList.remove('rotate-180'));

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
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('js/backend-datatable.js') }}"></script>
    @yield('scripts')
    @stack('scripts')

</body>

</html>