@extends('layouts.app')

@section('content')
    <title>Welcome to Human Assetment</title>

    <!-- External Resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        html {
            scroll-behavior: smooth;
        }

        /* Font Setting */
        body {
            font-family: 'Prompt', sans-serif;
        }

        /* Smooth Transition for Theme Switch */
        .theme-transition,
        .theme-transition * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Custom Scrollbar */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Hero Animation */
        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        /* Fade In Animation */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-up {
            opacity: 0;
            animation: fadeUp 0.8s ease-out forwards;
        }

        .delay-100 {
            animation-delay: 100ms;
        }

        .delay-200 {
            animation-delay: 200ms;
        }

        .delay-300 {
            animation-delay: 300ms;
        }

        .delay-400 {
            animation-delay: 400ms;
        }

        .delay-500 {
            animation-delay: 500ms;
        }

        /* Scroll Reveal Animation */
        .reveal {
            position: relative;
            opacity: 0;
            transition: all 1.2s ease;
        }

        .reveal.reveal-left {
            transform: translateX(-50px);
        }

        .reveal.reveal-right {
            transform: translateX(50px);
        }

        .reveal.active {
            opacity: 1;
            transform: translateX(0);
        }

        /* Hero Slider & Blurred BG */
        .hero-bg-blur {
            position: absolute;
            top: -10%;
            left: -10%;
            width: 120%;
            height: 120%;
            z-index: 0;
            filter: blur(40px) brightness(1.7);
            opacity: 0.95;
            transition: opacity 2.5s ease-in-out;
            object-fit: cover;
            transform: scale(1.1);
        }

        .hero-slider-item {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 1s ease-in-out, transform 1s ease-in-out;
            transform: scale(1.1);
        }

        .hero-slider-item.active {
            opacity: 1;
            transform: scale(1);
        }

        .dark .hero-bg-blur {
            filter: blur(80px) brightness(0.3);
            opacity: 0.4;
        }
    </style>

    <!-- Theme Toggle Script (Simple Vanilla JS) -->
    <script>
        // On page load or when changing themes, best to add inline in the head to avoid FOUC
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
        }

        // Scroll Reveal logic
        document.addEventListener('DOMContentLoaded', function() {
            const reveals = document.querySelectorAll('.reveal');

            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        // Optional: stop observing once revealed
                        // revealObserver.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1 // Reveal when 10% of the element is visible
            });

            reveals.forEach(reveal => {
                revealObserver.observe(reveal);
            });

            // Hero Slider logic
            const slides = document.querySelectorAll('.hero-slider-item');
            const bgBlurs = document.querySelectorAll('.hero-bg-blur');
            let currentSlide = 0;

            if (slides.length > 1) {
                setInterval(() => {
                    slides[currentSlide].classList.remove('active');
                    bgBlurs[currentSlide].style.opacity = '0';

                    currentSlide = (currentSlide + 1) % slides.length;

                    slides[currentSlide].classList.add('active');
                    bgBlurs[currentSlide].style.opacity = '0.95';
                }, 5000);
            }
        });
    </script>

    <div class="min-h-screen  text-slate-800 dark:text-gray-200 theme-transition relative overflow-x-hidden">


        <!-- ================= HERO SECTION ================= -->
        <div class="relative min-h-screen flex items-center pt-12 pb-20 overflow-hidden theme-transition">

            <!-- Blurred Backgrounds -->
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=2070&auto=format&fit=crop"
                    class="hero-bg-blur object-cover w-full h-full" style="opacity: 0.95">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=2070&auto=format&fit=crop"
                    class="hero-bg-blur object-cover w-full h-full" style="opacity: 0">
                <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=2070&auto=format&fit=crop"
                    class="hero-bg-blur object-cover w-full h-full" style="opacity: 0">

            </div>

            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 relative w-full z-30">
                <div class="grid grid-cols-1 lg:grid-cols-2 items-center gap-10 lg:gap-16 py-12 lg:py-32">

                    <!-- Welcome Content -->
                    <div
                        class="max-w-4xl mx-auto lg:mx-0 flex flex-col items-center lg:items-start text-center lg:text-left relative z-30">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-red-50 dark:bg-red-500/10 mb-8 border border-red-100 dark:border-red-500/20 shadow-sm w-max animate-fade-up">
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                            </span>
                            <span class="text-red-500 dark:text-red-400 text-[11px] font-bold tracking-widest uppercase">HR
                                PLATFORM 2.0</span>
                        </div>

                        <h1 class="text-[2.75rem] sm:text-6xl md:text-7xl lg:text-[5rem] font-bold mb-6 leading-[1.05] tracking-tight text-slate-900 dark:text-white animate-fade-up delay-100"
                            style="text-shadow: 0 2px 10px rgba(0,0,0,0.05)">
                            <span class="block mb-2 sm:mb-4">Welcome to</span>
                            <span class="text-red-500 block">Human Assetment</span>
                        </h1>

                        <p class="text-slate-800 dark:text-gray-100 text-base sm:text-lg lg:text-xl max-w-2xl mb-12 font-medium leading-relaxed animate-fade-up delay-200"
                            style="text-shadow: 0 1px 2px rgba(255,255,255,0.8)">
                            ยกระดับการบริหารทรัพยากรบุคคล มุ่งเน้นการประเมินที่มีประสิทธิภาพ และวัฒนธรรมการเรียนรู้ตลอดชีวิต
                            (Life Long Learning) เพื่อขับเคลื่อนองค์กรสู่อนาคต
                        </p>

                        <div class="flex flex-wrap justify-center lg:justify-start gap-6 animate-fade-up delay-300">
                            <a href="#"
                                class="group px-10 py-4 bg-red-600 hover:bg-red-700 text-white text-base font-bold rounded-2xl shadow-xl shadow-red-600/30 hover:shadow-red-600/40 hover:-translate-y-1 transition-all flex items-center gap-3">
                                <span>นโยบายองค์กร</span>
                                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                            </a>
                            <a href="#"
                                class="px-10 py-4 bg-white/80 dark:bg-white/5 backdrop-blur-md border border-slate-200 dark:border-white/10 hover:border-red-500 dark:hover:border-red-500 text-slate-700 dark:text-slate-300 text-base font-bold rounded-2xl hover:bg-white dark:hover:bg-white/10 shadow-lg hover:shadow-xl transition-all">
                                พันธกิจ (Mission)
                            </a>
                        </div>
                    </div>

                    <!-- Hero Image: Only show on Large Screens -->
                    <div class="hidden lg:flex relative items-center justify-center w-full animate-fade-up delay-400 z-10">
                        <div
                            class="relative w-full aspect-[5/4] rounded-[2rem] overflow-hidden shadow-2xl animate-float bg-gray-100 dark:bg-gray-800">
                            <!-- Slider Images -->
                            <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=2070&auto=format&fit=crop"
                                alt="Human Connection 1" class="hero-slider-item active w-full h-full object-cover">
                            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=2070&auto=format&fit=crop"
                                alt="Human Connection 2" class="hero-slider-item w-full h-full object-cover">
                            <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=2070&auto=format&fit=crop"
                                alt="Human Connection 3" class="hero-slider-item w-full h-full object-cover">

                            <!-- Soft Gradient Overlay -->
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-gray-50/80 via-transparent to-transparent dark:from-[#0d1117] dark:to-transparent z-10">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Transition Gradient (Refined Smooth) -->
            <div class="absolute bottom-0 left-0 w-full h-64 z-20 pointer-events-none transition-all duration-1000"
                style="background: linear-gradient(to top, 
                                                        var(--bg-color, #ffffff) 0%, 
                                                        var(--bg-color, #ffffff) 10%, 
                                                        rgba(255,255,255,0.8) 30%, 
                                                        rgba(255,255,255,0.4) 60%, 
                                                        rgba(255,255,255,0) 100%);">
            </div>

            <style>
                :root {
                    --bg-color: #ffffff;
                }

                .dark :root {
                    --bg-color: #0d1117;
                }

                /* Overwriting the style for dark mode because inline styles are hard to target with CSS classes alone */
                .dark .hero-transition-overlay {
                    background: linear-gradient(to top,
                            #0d1117 0%,
                            #0d1117 10%,
                            rgba(13, 17, 23, 0.8) 30%,
                            rgba(13, 17, 23, 0.4) 60%,
                            rgba(13, 17, 23, 0) 100%) !important;
                }
            </style>
            <div class="absolute bottom-0 left-0 w-full h-64 z-20 pointer-events-none hero-transition-overlay"></div>
        </div>

        <!-- ================= SERVICES GRID ================= -->
        <div id="services-grid" class="py-12 border-y border-slate-100 dark:border-white/5 scroll-mt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4 reveal reveal-left">
                    <div>
                        <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">ระบบบริการ <span
                                class="text-red-500">Service</span></h2>
                        <p class="text-slate-500 dark:text-slate-400 text-sm">เลือกใช้งานระบบต่างๆ ตามความต้องการของคุณ</p>
                    </div>
                </div>

                @php
                    $cardClass =
                        'group relative flex flex-col p-5 bg-gray-50 dark:bg-[#1E2129] border border-slate-200 dark:border-white/5 rounded-2xl hover:shadow-xl hover:shadow-slate-200/50 dark:hover:shadow-black/40 hover:-translate-y-1 transition-all duration-300 overflow-hidden';
                    $imgWrapperClass =
                        'w-full aspect-video rounded-xl overflow-hidden mb-4 bg-gray-200 dark:bg-gray-800 relative';
                    $imgClass = 'w-full h-full object-cover transition-transform duration-700 group-hover:scale-110';
                    $badgeOpen =
                        'px-2.5 py-1 rounded-md bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400 text-[10px] font-bold uppercase tracking-wide';
                    $badgeSoon =
                        'px-2.5 py-1 rounded-md bg-gray-100 text-gray-500 dark:bg-white/5 dark:text-gray-400 text-[10px] font-bold uppercase tracking-wide';
                    $btnActive =
                        'mt-auto inline-flex items-center gap-2 text-sm font-semibold text-red-600 dark:text-red-400 group-hover:gap-3 transition-all';
                    $btnDisabled =
                        'mt-auto inline-flex items-center gap-2 text-sm font-semibold text-gray-400 cursor-not-allowed';
                @endphp

                <!-- Section 1: Employee Services -->
                <div class="mb-12">
                    <div class="flex items-center gap-3 mb-6 reveal reveal-left">
                        <div class="h-8 w-1 bg-red-600 rounded-full"></div>
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white">ระบบบริการผู้ใช้งาน <span
                                class="text-slate-400 font-medium text-sm ml-2">Employee Services</span></h3>
                    </div>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
                        <!-- 1. HR Requests -->
                        @auth
                            <a href="{{ route('request.hr') }}" class="{{ $cardClass }} reveal reveal-right"
                                style="transition-delay: 100ms">
                                <div class="{{ $imgWrapperClass }}">
                                    <img src="/images/welcome/reporting.jpg"
                                        onerror="this.src='https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&q=80&w=600'"
                                        class="{{ $imgClass }}" alt="HR Request">
                                </div>
                                <div class="flex justify-between items-start mb-2">
                                    <div class="p-2 rounded-lg bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400">
                                        <i class="fa-regular fa-file-lines text-lg"></i>
                                    </div>
                                    <span class="{{ $badgeOpen }}">Open</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">HR Request</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">คำร้องทุกประเภท
                                    แก้ไขเวลา
                                    ใบรับรอง ฯลฯ</p>
                                <span class="{{ $btnActive }}">เข้าสู่ระบบ <i class="fa-solid fa-arrow-right"></i></span>
                            </a>
                        @else
                            <div class="{{ $cardClass }} cursor-pointer login-open-btn reveal reveal-right"
                                style="transition-delay: 100ms">
                                <div class="{{ $imgWrapperClass }}">
                                    <img src="/images/welcome/reporting.jpg"
                                        onerror="this.src='https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&q=80&w=600'"
                                        class="{{ $imgClass }}" alt="HR Request">
                                </div>
                                <div class="flex justify-between items-start mb-2">
                                    <div class="p-2 rounded-lg bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400">
                                        <i class="fa-regular fa-file-lines text-lg"></i>
                                    </div>
                                    <span class="{{ $badgeOpen }}">Login</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">HR Request</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">
                                    เข้าสู่ระบบเพื่อใช้งานคำร้องต่างๆ</p>
                                <span class="{{ $btnActive }}">Login to Open <i class="fa-solid fa-arrow-right"></i></span>
                            </div>
                        @endauth

                        <!-- 3. Training -->
                        @auth
                            <a href="{{ route('training.index') }}" class="{{ $cardClass }} reveal reveal-right"
                                style="transition-delay: 200ms">
                                <div class="{{ $imgWrapperClass }}">
                                    <img src="/images/welcome/training.jpg"
                                        onerror="this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&q=80&w=600'"
                                        class="{{ $imgClass }}" alt="Training">
                                </div>
                                <div class="flex justify-between items-start mb-2">
                                    <div
                                        class="p-2 rounded-lg bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400">
                                        <i class="fa-solid fa-chalkboard-user text-lg"></i>
                                    </div>
                                    <span class="{{ $badgeOpen }}">Open</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">Training</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">
                                    ระบบฝึกอบรมและพัฒนาทักษะ
                                </p>
                                <span class="{{ $btnActive }}">เข้าสู่ระบบ <i class="fa-solid fa-arrow-right"></i></span>
                            </a>
                        @else
                            <div class="{{ $cardClass }} cursor-pointer login-open-btn reveal reveal-right"
                                style="transition-delay: 200ms">
                                <div class="{{ $imgWrapperClass }}">
                                    <img src="/images/welcome/training.jpg"
                                        onerror="this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&q=80&w=600'"
                                        class="{{ $imgClass }}" alt="Training">
                                </div>
                                <div class="flex justify-between items-start mb-2">
                                    <div
                                        class="p-2 rounded-lg bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400">
                                        <i class="fa-solid fa-chalkboard-user text-lg"></i>
                                    </div>
                                    <span class="{{ $badgeOpen }}">Login</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">Training</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">
                                    ระบบฝึกอบรมและพัฒนาทักษะ
                                </p>
                                <span class="{{ $btnActive }}">Login to Open <i
                                        class="fa-solid fa-arrow-right"></i></span>
                            </div>
                        @endauth

                        <!-- 4. Recruitment -->
                        <a href="{{ route('recruitment.index') }}" class="{{ $cardClass }} reveal reveal-right"
                            style="transition-delay: 300ms">
                            <div class="{{ $imgWrapperClass }}">
                                <img src="/images/welcome/job-hiring.jpg"
                                    onerror="this.src='https://images.unsplash.com/photo-1586281380349-632531db7ed4?auto=format&fit=crop&q=80&w=600'"
                                    class="{{ $imgClass }}" alt="Hiring">
                            </div>
                            <div class="flex justify-between items-start mb-2">
                                <div
                                    class="p-2 rounded-lg bg-purple-100 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400">
                                    <i class="fa-solid fa-briefcase text-lg"></i>
                                </div>
                                <span class="{{ $badgeOpen }}">Open</span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">Recruitment</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">
                                ตำแหน่งงานว่างและการรับสมัคร
                            </p>
                            <span class="{{ $btnActive }}">ดูงานที่เปิดรับ <i
                                    class="fa-solid fa-arrow-right"></i></span>
                        </a>

                        <!-- 6. Suggestion -->
                        @auth
                            <a href="{{ route('suggestion.index') }}" class="{{ $cardClass }} reveal reveal-right"
                                style="transition-delay: 400ms">
                                <div class="{{ $imgWrapperClass }}">
                                    <img src="/images/welcome/suggestion.png"
                                        onerror="this.src='https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&q=80&w=600'"
                                        class="{{ $imgClass }}" alt="Suggestion">
                                </div>
                                <div class="flex justify-between items-start mb-2">
                                    <div
                                        class="p-2 rounded-lg bg-pink-100 dark:bg-pink-500/10 text-pink-600 dark:text-pink-400">
                                        <i class="fa-regular fa-comment-dots text-lg"></i>
                                    </div>
                                    <span class="{{ $badgeOpen }}">Open</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">Suggestion</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">
                                    กล่องรับข้อเสนอแนะและร้องเรียน</p>
                                <span class="{{ $btnActive }}">ร้องเรียน / เสนอแนะ <i
                                        class="fa-solid fa-arrow-right"></i></span>
                            </a>
                        @else
                            <div class="{{ $cardClass }} cursor-pointer login-open-btn reveal reveal-right"
                                style="transition-delay: 400ms">
                                <div class="{{ $imgWrapperClass }}">
                                    <img src="/images/welcome/suggestion.png"
                                        onerror="this.src='https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&q=80&w=600'"
                                        class="{{ $imgClass }}" alt="Suggestion">
                                </div>
                                <div class="flex justify-between items-start mb-2">
                                    <div
                                        class="p-2 rounded-lg bg-pink-100 dark:bg-pink-500/10 text-pink-600 dark:text-pink-400">
                                        <i class="fa-regular fa-comment-dots text-lg"></i>
                                    </div>
                                    <span class="{{ $badgeOpen }}">Login</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">Suggestion</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">
                                    กล่องรับข้อเสนอแนะและร้องเรียน</p>
                                <span class="{{ $btnActive }}">Login to Open <i
                                        class="fa-solid fa-arrow-right"></i></span>
                            </div>
                        @endauth
                    </div>
                </div>

                <!-- Section 2: Management Dashboards (HA Only) -->
                @if (Auth::check() && (Auth::user()->hr_status == '0' || Auth::user()->employee_code == '11648'))
                    <div class="pt-8 border-t border-slate-100 dark:border-white/5">
                        <div class="flex items-center gap-3 mb-6 reveal reveal-left">
                            <div class="h-8 w-1 bg-blue-600 rounded-full"></div>
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white">ระบบจัดการและวิเคราะห์ <span
                                    class="text-slate-400 font-medium text-sm ml-2">Management Dashboards</span></h3>
                        </div>
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
                            <!-- 2. Manpower -->
                            <a href="{{ route('manpower.dashboard') }}" class="{{ $cardClass }} reveal reveal-left"
                                style="transition-delay: 100ms">
                                <div class="{{ $imgWrapperClass }}">
                                    <img src="/images/welcome/manpower.jpg"
                                        onerror="this.src='https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&q=80&w=600'"
                                        class="{{ $imgClass }}" alt="Manpower">
                                </div>
                                <div class="flex justify-between items-start mb-2">
                                    <div
                                        class="p-2 rounded-lg bg-blue-100 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                                        <i class="fa-solid fa-briefcase text-lg"></i>
                                    </div>
                                    <span class="{{ $badgeOpen }}">Open</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">Manpower</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">
                                    ระบบจัดการอัตรากำลังพล
                                </p>
                                <span class="{{ $btnActive }}">เข้าสู่ระบบ <i
                                        class="fa-solid fa-arrow-right"></i></span>
                            </a>

                            <!-- 7. Data Management -->
                            <a href="{{ route('request.data') }}" class="{{ $cardClass }} reveal reveal-left"
                                style="transition-delay: 200ms">
                                <div class="{{ $imgWrapperClass }}">
                                    <img src="/images/welcome/data-management.jpg"
                                        onerror="this.src='https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=600'"
                                        class="{{ $imgClass }}" alt="Data">
                                </div>
                                <div class="flex justify-between items-start mb-2">
                                    <div
                                        class="p-2 rounded-lg bg-indigo-100 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                                        <i class="fa-solid fa-database text-lg"></i>
                                    </div>
                                    <span class="{{ $badgeOpen }}">Admin</span>
                                </div>
                                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">Data Management</h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 line-clamp-2">
                                    จัดการข้อมูลพนักงานและฐานข้อมูล</p>
                                <span class="{{ $btnActive }}">จัดการข้อมูล <i
                                        class="fa-solid fa-arrow-right"></i></span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- ================= NEWS SECTION ================= -->
        <div id="news-grid" class="py-16 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-end mb-8 reveal reveal-right">
                    <div>
                        <h2 class="text-4xl font-bold mb-2">
                            <span class="text-red-600 dark:text-red-400">ข่าวสาร</span>
                            <span class="text-slate-900 dark:text-white">& ประชาสัมพันธ์</span>
                        </h2>
                        <p class="text-slate-500 dark:text-slate-400 text-sm">ติดตามความเคลื่อนไหวล่าสุดขององค์กร</p>
                    </div>
                    <a href="{{ route('news.newsAll') }}"
                        class="hidden md:flex items-center gap-2 text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-700 transition-colors">
                        ดูทั้งหมด <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                @if ($highlight)
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
                        <!-- Main Highlight -->
                        <div
                            class="lg:col-span-7 group relative h-[400px] lg:h-[500px] rounded-3xl overflow-hidden shadow-2xl shadow-slate-200 dark:shadow-black/50 cursor-pointer reveal reveal-left">
                            <img src="{{ $highlight->image_path ? asset(is_array($highlight->image_path) ? $highlight->image_path[0] : $highlight->image_path) : 'https://placehold.co/1200x800/121418/FFF?text=News' }}"
                                alt="{{ $highlight->title }}"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">

                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

                            <div class="absolute bottom-0 left-0 p-8 w-full">
                                <span
                                    class="inline-block px-3 py-1 bg-red-600 text-white text-[10px] font-bold rounded-md uppercase tracking-wider mb-3 shadow-lg shadow-red-600/40">
                                    {{ $highlight->newto ?? 'Highlight' }}
                                </span>
                                <div class="text-gray-300 text-xs mb-2 flex items-center gap-2">
                                    <i class="far fa-calendar"></i>
                                    {{ optional($highlight->published_date)->format('d M Y') }}
                                </div>
                                <h3 class="text-2xl lg:text-4xl font-bold text-white mb-3 leading-tight line-clamp-2">
                                    {{ $highlight->title }}
                                </h3>
                                <p class="text-gray-300 text-sm mb-6 line-clamp-2 font-light opacity-90 lg:w-3/4">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($highlight->content), 120) }}
                                </p>

                                <a href="{{ $highlight->link_news ?? route('news.detail', $highlight->news_id) }}"
                                    {!! $highlight->link_news ? 'target="_blank"' : '' !!}
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 rounded-full text-white text-sm font-medium transition-all">
                                    อ่านต่อ <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Side News List -->
                        <div
                            class="lg:col-span-5 flex flex-col gap-4 h-[500px] overflow-y-auto no-scrollbar pr-2 reveal reveal-right">
                            @forelse($otherNews->take(4) as $item)
                                <a href="{{ route('news.detail', $item->news_id) }}"
                                    class="group flex gap-4 p-4 bg-white dark:bg-[#1E2129] rounded-2xl border border-slate-100 dark:border-white/5 dark:hover:border-red-500/30 transition-all hover:shadow-lg dark:hover:shadow-none">
                                    <div
                                        class="w-28 h-24 rounded-xl overflow-hidden flex-shrink-0 bg-gray-200 dark:bg-gray-800">
                                        <img src="{{ $item->image_path ? asset(is_array($item->image_path) ? $item->image_path[0] : $item->image_path) : 'https://placehold.co/200x200/252933/FFF?text=News' }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                                            alt="{{ $item->title }}">
                                    </div>
                                    <div class="flex-1 flex flex-col justify-center">
                                        <div class="text-slate-400 text-[10px] mb-1 flex items-center gap-1">
                                            <i class="far fa-calendar-alt"></i>
                                            {{ optional($item->published_date)->format('d M Y') }}
                                        </div>
                                        <h4
                                            class="text-slate-800 dark:text-white font-semibold text-sm mb-1 line-clamp-2 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                                            {{ $item->title }}
                                        </h4>
                                        <p class="text-slate-500 dark:text-slate-400 text-xs line-clamp-1">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($item->content), 60) }}
                                        </p>
                                    </div>
                                </a>
                            @empty
                                <div class="flex items-center justify-center h-full text-slate-400 text-sm">ไม่มีข่าวอื่นๆ
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Horizontal Scroll Mobile/Tablet -->
                    <div class="block lg:hidden">
                        <div class="flex gap-4 overflow-x-auto no-scrollbar pb-4">
                            @foreach ($otherNews->skip(4)->take(5) as $item)
                                <a href="{{ route('news.detail', $item->news_id) }}"
                                    class="min-w-[260px] bg-white dark:bg-[#1E2129] rounded-xl p-3 border border-slate-100 dark:border-white/5 shadow-sm">
                                    <div class="h-32 rounded-lg overflow-hidden mb-3 bg-gray-200 dark:bg-gray-800">
                                        <img src="{{ $item->image_path ? asset(is_array($item->image_path) ? $item->image_path[0] : $item->image_path) : 'https://placehold.co/300x200/252933/FFF?text=News' }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <h5 class="text-slate-800 dark:text-white text-sm font-medium line-clamp-2">
                                        {{ $item->title }}
                                    </h5>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div
                        class="bg-white dark:bg-[#1E2129] rounded-3xl p-12 text-center border border-dashed border-slate-300 dark:border-gray-700">
                        <i class="fa-regular fa-newspaper text-4xl text-slate-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-slate-500 dark:text-slate-400">ยังไม่มีข่าวประชาสัมพันธ์ในขณะนี้</p>
                    </div>
                @endif

                <!-- Mobile 'View All' Button -->
                <div class="mt-6 text-center md:hidden">
                    <a href="{{ route('news.index') }}"
                        class="inline-block px-6 py-2 border border-slate-300 dark:border-gray-700 rounded-full text-sm text-slate-600 dark:text-gray-300">
                        ดูข่าวทั้งหมด
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
