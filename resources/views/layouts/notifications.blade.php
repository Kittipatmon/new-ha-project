<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'ศูนย์การแจ้งเตือน') - Kumwell HR</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Noto+Sans+Thai:wght@300;400;500;600;700;800;900&family=Prompt:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Noto Sans Thai', 'Inter', 'Prompt', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .page-title {
            font-size: 1.625rem;
            font-weight: 800;
            letter-spacing: -0.035em;
            line-height: 1.2;
        }

        .page-subtitle {
            font-size: 0.8125rem;
            font-weight: 400;
            letter-spacing: 0;
            line-height: 1.5;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        (function () {
            try {
                const stored = localStorage.getItem('theme');
                const theme = stored || 'light';
                const root = document.documentElement;
                if (theme === 'dark') {
                    root.classList.add('dark');
                } else {
                    root.classList.remove('dark');
                }
                root.setAttribute('data-theme', theme);
            } catch (_) { }
        })();
    </script>
</head>

<body class="min-h-screen flex flex-col bg-[#F8FAFC] dark:bg-[#0F1117] text-slate-800 dark:text-slate-100 antialiased transition-colors">

    <!-- Top Dedicated Navbar for Notifications Center -->
    <header class="sticky top-0 z-50 bg-white/95 dark:bg-[#1E2129]/95 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 shadow-2xs transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            {{-- Brand & Breadcrumb --}}
            <div class="flex items-center gap-3.5">
                <a href="{{ route('welcome') }}">
                    <span class="text-red-600 font-bold text-lg sm:text-xl xl:text-3xl">Kumwell</span>
                </a>

                <div class="hidden sm:flex items-center gap-2 text-slate-400 dark:text-slate-500 text-sm">
                    <span>/</span>
                    <div class="flex items-center gap-1.5 font-bold text-slate-700 dark:text-slate-200">
                        <i class="fa-solid fa-bell text-red-600 text-xs"></i>
                        <span>ศูนย์การแจ้งเตือน (Notifications)</span>
                    </div>
                </div>
            </div>

            {{-- Right Controls: Framed Back Button --}}
            <div class="flex items-center">
                <a href="javascript:history.back();" 
                   class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 hover:bg-slate-50 dark:hover:bg-gray-700 hover:border-slate-300 rounded-xl transition-all shadow-2xs active:scale-95">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>ย้อนกลับ</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 py-8">
        @yield('content')
    </main>

    <!-- Clean Footer -->
    <footer class="py-6 border-t border-slate-200/80 dark:border-white/10 bg-white/60 dark:bg-[#1E2129]/60 backdrop-blur-sm text-center text-xs text-slate-400 dark:text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>© {{ date('Y') }} Kumwell Corporation Public Company Limited. All rights reserved.</p>
            <p class="font-medium text-red-600/70 dark:text-red-400/70">Kumwell HR Notification System</p>
        </div>
    </footer>

    <script>
        function toggleNotificationTheme() {
            const root = document.documentElement;
            const isDark = root.classList.contains('dark');
            if (isDark) {
                root.classList.remove('dark');
                root.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
            } else {
                root.classList.add('dark');
                root.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
            }
        }
    </script>
</body>

</html>
