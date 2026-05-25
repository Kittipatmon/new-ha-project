<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Hr System</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;400;600&family=Prompt:wght@200;400;600&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Prompt', 'Kanit', sans-serif;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.46.0/dist/apexcharts.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Apply persisted theme ASAP to prevent flash
        (function () {
            try {
                const stored = localStorage.getItem('theme');
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = stored || (prefersDark ? 'dark' : 'light');
                const root = document.documentElement;
                root.classList.toggle('dark', theme === 'dark');
                root.setAttribute('data-theme', theme);
            } catch (_) { }
        })();


        @if(session('approve_success'))
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: @json(session('approve_success')),
                    timer: 2500,
                    showConfirmButton: false
                });
            });
        @endif
    </script>
</head>


<body class="min-h-screen flex flex-col">
    <div class="flex-1 flex flex-col">
        @include('layouts.hrrequest.navigation')

        <!-- Page Heading -->
        @if(isset($breadcrumbs) && is_array($breadcrumbs))
            <!-- <div class="max-w-8xl mx-auto py-2 px-4 sm:px-6 lg:px-8">
                                <div class="breadcrumbs text-sm">
                                    <ul>
                                        @foreach($breadcrumbs as $breadcrumb)
                                        <li>
                                            @if(isset($breadcrumb['url']) && $breadcrumb['url'])
                                            <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
                                            @else
                                            {{ $breadcrumb['label'] }}
                                            @endif
                                        </li>
                                        @endforeach
                                    </ul>

                                </div>
                            </div> -->
        @endif

        <!-- Page Content -->
        <main class="mb-4 px-6 flex-1 pt-16">
            <!-- <div class="px-2">
                <div class="container max-w-8xl mx-auto sm:px-6 lg:px-4 card bg-base-100 shadow mt-4 border"> -->
            @yield('content')
            <!-- </div>
            </div> -->
        </main>
    </div>
    @include('layouts.footer')
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    @stack('scripts')
    <script>
        // Wire up dark/light toggles
        (function () {
            const root = document.documentElement;
            function applyTheme(theme) {
                const isDark = theme === 'dark';
                root.classList.toggle('dark', isDark);
                root.setAttribute('data-theme', isDark ? 'dark' : 'light');
                try { localStorage.setItem('theme', isDark ? 'dark' : 'light'); } catch (_) { }
                // sync all toggles
                document.querySelectorAll('[data-theme-toggle]')
                    .forEach(el => { if ('checked' in el) el.checked = isDark; });
            }
            // initialize toggles
            const initialTheme = root.classList.contains('dark') ? 'dark' : 'light';
            document.querySelectorAll('[data-theme-toggle]').forEach(el => {
                if ('checked' in el) el.checked = (initialTheme === 'dark');
                el.addEventListener('change', () => applyTheme(el.checked ? 'dark' : 'light'));
            });
            // optional buttons
            document.querySelectorAll('[data-set-theme]')
                .forEach(btn => btn.addEventListener('click', () => applyTheme(btn.dataset.setTheme)));
        })();
    </script>
</body>

</html>