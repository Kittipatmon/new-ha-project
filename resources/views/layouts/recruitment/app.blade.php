<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Hr System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&family=Prompt:wght@300;400;500;600;700&family=Sarabun:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @font-face {
            font-family: 'THSarabunPSK';
            src: url("{{ asset('fonts/THSarabun.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'THSarabunPSK';
            src: url("{{ asset('fonts/THSarabun Bold.ttf') }}") format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        @font-face {
            font-family: 'THSarabunPSK';
            src: url("{{ asset('fonts/THSarabun Italic.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: italic;
        }

        @font-face {
            font-family: 'THSarabunPSK';
            src: url("{{ asset('fonts/THSarabun BoldItalic.ttf') }}") format('truetype');
            font-weight: bold;
            font-style: italic;
        }

        body, button, input, select, textarea, h1, h2, h3, h4, h5, h6, label, span, div, p, a, td, th {
            font-family: 'Prompt', 'Kanit', sans-serif !important;
        }

        /* Ensure FontAwesome icons preserve their font family */
        .fa, .fas, .far, .fal, .fab, .fa-solid, .fa-regular, .fa-light, .fa-thin, .fa-duotone, .fa-brands,
        i[class*="fa-"], i[class^="fa-"], span[class*="fa-"], span[class^="fa-"] {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands", "FontAwesome" !important;
        }

        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            font-feature-settings: 'liga' 1, 'calt' 1;
        }

        /* ===== Typography System ===== */
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Prompt', 'Kanit', sans-serif !important;
            font-weight: 700;
            line-height: 1.25;
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
            color: #94a3b8;
            letter-spacing: 0;
            line-height: 1.5;
        }

        /* Table typography */
        table th {
            font-family: 'Inter', 'Noto Sans Thai', sans-serif;
            font-size: 0.6875rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        table td {
            font-size: 0.8125rem;
            line-height: 1.5;
        }

        /* Badge/tag typography */
        .badge-text {
            font-family: 'Inter', 'Noto Sans Thai', sans-serif;
            font-size: 0.6875rem;
            font-weight: 600;
            letter-spacing: 0.01em;
        }

        /* Form labels */
        .form-label {
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
        }

        /* Card content */
        .card-container {
            background: white;
            border-radius: 1rem;
            border: 1px solid rgb(243 244 246);
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.04), 0 1px 2px -1px rgb(0 0 0 / 0.04);
        }

        .dark .card-container {
            background: #1e2129;
            border-color: rgb(55 65 81 / 0.5);
        }

        /* Smooth scrollbar for tables */
        .overflow-x-auto::-webkit-scrollbar {
            height: 6px;
        }
        .overflow-x-auto::-webkit-scrollbar-track {
            background: transparent;
        }
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }
        .dark .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #475569;
        }

        :root {
            --kumwell-red: #F2704E;
            --kumwell-red-dark: #d45636;
        }

        .text-kumwell-red {
            color: var(--kumwell-red);
        }

        .bg-kumwell-red {
            background-color: var(--kumwell-red);
        }

        .border-kumwell-red {
            border-color: var(--kumwell-red);
        }

        .hover\:bg-kumwell-red:hover {
            background-color: var(--kumwell-red);
        }

        .focus\:ring-kumwell-red:focus {
            --tw-ring-color: var(--kumwell-red);
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.46.0/dist/apexcharts.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Apply persisted theme ASAP to prevent flash
        (function () {
            try {
                const stored = localStorage.getItem('theme');
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = 'light';
                const root = document.documentElement;
                root.classList.remove('dark');
                root.setAttribute('data-theme', theme);
            } catch (_) { }
        })();

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

        @if(session('error'))
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'error',
                    title: 'แจ้งเตือน',
                    text: @json(session('error')),
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#dc2626'
                });
            });
        @endif

        @if(session('warning'))
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'warning',
                    title: 'ข้อควรระวัง',
                    text: @json(session('warning')),
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#f59e0b'
                });
            });
        @endif

        @if(session('info'))
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'info',
                    title: 'ข้อมูล',
                    text: @json(session('info')),
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#3b82f6'
                });
            });
        @endif

        window.confirmDisconnectMicrosoft = function(form, event) {
            if (event) event.preventDefault();
            Swal.fire({
                title: 'ยืนยันยกเลิกการเชื่อมต่อ?',
                text: 'คุณต้องการยกเลิกการเชื่อมต่อ Microsoft 365 หรือไม่? เมื่อยกเลิกแล้วระบบจะสลับไปส่งอีเมลผ่านระบบสำรอง (SMTP) แทน',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa-solid fa-arrow-right-from-bracket mr-1"></i> ยืนยันยกเลิก',
                cancelButtonText: 'ไม่ยกเลิก',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl shadow-xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        };
    </script>
</head>


<body class="min-h-screen flex flex-col bg-white dark:bg-slate-900 overflow-x-hidden">
    <div class="flex-1 flex flex-col">
        @include('layouts.recruitment.navigation')

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
        <main class="px-0 flex-1 pt-20 sm:pt-24">
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
    @yield('scripts')
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