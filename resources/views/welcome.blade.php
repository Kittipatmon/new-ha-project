@extends('layouts.app')

@section('content')
    @include('layouts.navigation')
    <title>Human Assetment — Kumwell Group</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Prompt:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Theme toggle & Reveal Script -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' ||
            (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
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

        document.addEventListener('DOMContentLoaded', function() {
            // Scroll reveal with Tailwind classes
            const revealObs = new IntersectionObserver(entries => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.classList.remove('opacity-0', 'translate-y-8', '-translate-x-8', 'translate-x-8');
                        e.target.classList.add('opacity-100', 'translate-y-0', 'translate-x-0');
                        revealObs.unobserve(e.target);
                    }
                });
            }, {
                threshold: 0.1
            });
            document.querySelectorAll('.reveal').forEach(el => revealObs.observe(el));

            // Hero slider
            const slides = document.querySelectorAll('.hero-slide');
            const dots = document.querySelectorAll('.hero-dot');
            let cur = 0;
            let timer;

            if (slides.length > 0 && dots.length > 0) {
                function goSlide(n) {
                    slides[cur].classList.remove('opacity-100', 'z-10');
                    slides[cur].classList.add('opacity-0', 'z-0');
                    dots[cur].classList.remove('bg-white', 'bg-red-600', 'w-6');
                    dots[cur].classList.add('bg-white/50');
                    
                    cur = n % slides.length;
                    
                    slides[cur].classList.remove('opacity-0', 'z-0');
                    slides[cur].classList.add('opacity-100', 'z-10');
                    dots[cur].classList.remove('bg-white/50');
                    dots[cur].classList.add('bg-red-600', 'w-6');
                }
                dots.forEach((d, i) => d.addEventListener('click', () => {
                    clearInterval(timer);
                    goSlide(i);
                    timer = setInterval(() => goSlide(cur + 1), 5500);
                }));
                timer = setInterval(() => goSlide(cur + 1), 5500);
            }

            // Page background auto-rotate
            const pageBgSlides = document.querySelectorAll('.page-bg-slide');
            if (pageBgSlides.length > 1) {
                let bgCur = 0;
                setInterval(() => {
                    pageBgSlides[bgCur].classList.remove('opacity-100');
                    pageBgSlides[bgCur].classList.add('opacity-0');
                    bgCur = (bgCur + 1) % pageBgSlides.length;
                    pageBgSlides[bgCur].classList.remove('opacity-0');
                    pageBgSlides[bgCur].classList.add('opacity-100');
                }, 8000);
            }
        });
    </script>

    <style>
        /* ลบกรอบเขียว (Padding/Margin ของ main ใน DevTools) เพื่อให้คอนเทนต์เต็มหน้าจอ */
        body main,
        main,
        main.mb-4,
        main.px-3,
        main.flex-1 {
            padding-left: 0 !important;
            padding-right: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            margin-bottom: 0 !important;
            margin-top: 0 !important;
        }

        /* Keep minimal base settings if needed, otherwise rely on Tailwind */
        html { scroll-behavior: smooth; }
        body { font-family: 'Prompt', sans-serif; }
    </style>

    @php
        $hasCustomHero = isset($heroBannerPosters) && $heroBannerPosters->count() > 0;
        $hasCustomBg = isset($heroBackgrounds) && $heroBackgrounds->count() > 0;
    @endphp

    <!-- ==================== PAGE BACKGROUND ==================== -->
    @if ($hasCustomBg)
        <div class="fixed inset-0 z-0 pointer-events-none">
            @foreach ($heroBackgrounds as $idx => $bg)
                <div class="page-bg-slide absolute inset-0 transition-opacity duration-[2000ms] ease-in-out {{ $idx === 0 ? 'opacity-100' : 'opacity-0' }}">
                    <img src="{{ asset($bg->image_path) }}" alt="{{ $bg->title }}" class="w-full h-full object-cover">
                </div>
            @endforeach
            <!-- Subtle overlay to keep content readable -->
            <div class="absolute inset-0 bg-white/92 dark:bg-gray-900/90 backdrop-blur-[1px]"></div>
        </div>
    @endif

    <!-- ==================== HERO ==================== -->
    <div class="w-full px-0 pt-20 sm:pt-24 pb-0">
        <div class="relative w-full {{ $hasCustomHero ? 'aspect-[1269/362]' : 'h-[260px] sm:h-[320px] md:h-[420px]' }} rounded-none overflow-hidden group bg-slate-100 dark:bg-slate-900" style="{{ $hasCustomHero ? 'aspect-ratio: 1269 / 362;' : '' }}">

            @if ($hasCustomHero)
                <!-- Dynamic Hero Slides from Backend (Posters) -->
                @foreach ($heroBannerPosters as $idx => $heroSlide)
                    @php
                        $heroLink = $heroSlide->action_url ? route('posters.click', $heroSlide->id) : null;
                    @endphp
                    <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out {{ $idx === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }} overflow-hidden flex items-center justify-center bg-slate-100 dark:bg-slate-900 rounded-none" data-poster-id="{{ $heroSlide->id }}">
                        @if ($heroLink)
                            <a href="{{ $heroLink }}" class="block w-full h-full relative overflow-hidden flex items-center justify-center rounded-none">
                        @else
                            <div class="w-full h-full relative overflow-hidden flex items-center justify-center rounded-none">
                        @endif
                            <img src="{{ asset($heroSlide->image_path) }}" alt="{{ $heroSlide->title }}" class="relative z-10 w-full h-full object-cover object-center">
                        @if ($heroLink)
                            </a>
                        @else
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <!-- Default Image Slides (Fallback) -->
                <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-100 z-10 rounded-none">
                    <img src="{{ asset('images/welcome/hero_industrial.png') }}" alt="Kumwell Plant" class="w-full h-full object-cover">
                </div>
                <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0 z-0 rounded-none">
                    <img src="{{ asset('images/welcome/ro1.jpg') }}" alt="Plant Night" class="w-full h-full object-cover">
                </div>
                <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0 z-0 rounded-none">
                    <img src="https://image.makewebeasy.net/makeweb/m_1920x0/0etpaXZ92/Corporate/Banner_ab_1_.webp?v=202405291424" alt="Team" class="w-full h-full object-cover">
                </div>
            @endif

            <!-- Flat uniform overlay (only when showing background images, not posters) -->
            @if (!$hasCustomHero)
                <div class="absolute inset-0 z-20 bg-slate-900/60 mix-blend-multiply"></div>

                <!-- Centered content -->
                <div class="relative z-30 flex flex-col justify-center items-center h-full text-center px-6 md:px-12 py-12">
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white mb-4 uppercase tracking-tight opacity-0 translate-y-8 transition-all duration-700 delay-100 reveal">
                        Human Assetment
                    </h1>
                    <p class="text-xs md:text-sm text-slate-100 max-w-xl mx-auto mb-6 md:mb-8 leading-relaxed opacity-0 translate-y-8 transition-all duration-700 delay-200 reveal">
                        ยกระดับการบริหารทรัพยากรบุคคล มุ่งเน้นการประเมินที่มีประสิทธิภาพ
                        และวัฒนธรรมการเรียนรู้ตลอดชีวิต (Life Long Learning)
                        เพื่อขับเคลื่อนองค์กรสู่อนาคต
                    </p>
                    <div class="opacity-0 translate-y-8 transition-all duration-700 delay-300 reveal">
                        <a href="#services-grid" class="inline-flex items-center gap-2 bg-white text-kumwell-red font-semibold text-xs px-6 py-2.5 rounded-xl hover:bg-slate-50 transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-slate-900">
                            View More <i class="fas fa-arrow-down ml-1"></i>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Slider Dots -->
            <div class="absolute bottom-3 sm:bottom-4 left-0 right-0 z-30 flex justify-center gap-2">
                @if ($hasCustomHero)
                    @foreach ($heroBannerPosters as $idx => $heroSlide)
                        <button class="hero-dot w-2.5 h-2.5 rounded-full transition-all duration-300 {{ $idx === 0 ? 'bg-red-600 w-6' : 'bg-white/70 hover:bg-white' }} shadow-sm focus:outline-none" aria-label="Go to Slide {{ $idx + 1 }}"></button>
                    @endforeach
                @else
                    <button class="hero-dot w-2.5 h-2.5 rounded-full bg-white transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-slate-900" aria-label="Go to Slide 1"></button>
                    <button class="hero-dot w-2.5 h-2.5 rounded-full bg-white/50 hover:bg-white/80 transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-slate-900" aria-label="Go to Slide 2"></button>
                    <button class="hero-dot w-2.5 h-2.5 rounded-full bg-white/50 hover:bg-white/80 transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-slate-900" aria-label="Go to Slide 3"></button>
                @endif
            </div>
        </div>
    </div>

    <!-- ==================== ANNOUNCEMENT & POSTER SHOWCASE ==================== -->
    <div class="py-4 sm:py-6 lg:py-8 bg-slate-100/100 dark:bg-[#0c1017] border-b border-slate-200 dark:border-slate-800/80 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4 sm:space-y-6">
            
            <!-- Hero Top Posters (โปสเตอร์หัวข้อหลัก แสดงรูปภาพจริงเต็มขนาด ไม่ตัดขอบ) -->
            @if (isset($heroTopPosters) && $heroTopPosters->count() > 0)
                <div class="space-y-4">
                    @foreach ($heroTopPosters as $heroPoster)
                        @php
                            $heroUrl = $heroPoster->action_url ? route('posters.click', $heroPoster->id) : null;
                        @endphp
                        <div class="w-full relative rounded-none overflow-hidden shadow-sm group bg-transparent" data-poster-id="{{ $heroPoster->id }}">
                            @if ($heroUrl)
                                <a href="{{ $heroUrl }}" class="block w-full rounded-none">
                            @endif
                                <img src="{{ asset($heroPoster->image_path) }}" 
                                     alt="{{ $heroPoster->title }}" 
                                     class="w-full h-auto block rounded-none hover:scale-[1.002] transition-transform duration-300">
                            @if ($heroUrl)
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6 items-stretch">
                
                <!-- Left Column: Interactive Carousel Poster (Col-span 8) -->
                <div class="lg:col-span-8 reveal opacity-0 translate-y-8 transition-all duration-700">
                    <div class="relative w-full h-full min-h-[220px] sm:min-h-[300px] lg:min-h-[380px] flex flex-col justify-center rounded-none overflow-hidden shadow-sm group">
                        
                        <!-- Slide Items Container -->
                        <div id="poster-carousel-track" class="w-full h-full relative flex items-center justify-center rounded-none overflow-hidden bg-slate-900/5 dark:bg-slate-900/40">
                            
                            @if (isset($mainPosters) && $mainPosters->count() > 0)
                                @foreach ($mainPosters as $idx => $poster)
                                    @php
                                        $destUrl = $poster->action_url ? route('posters.click', $poster->id) : null;
                                    @endphp
                                    <div class="poster-slide {{ $idx === 0 ? 'block' : 'hidden' }} w-full h-full relative transition-all duration-500 overflow-hidden flex items-center justify-center rounded-none" data-poster-id="{{ $poster->id }}">
                                        @if ($destUrl)
                                            <a href="{{ $destUrl }}" class="block w-full h-full relative group flex items-center justify-center overflow-hidden">
                                        @else
                                            <div class="block w-full h-full relative flex items-center justify-center overflow-hidden">
                                        @endif
                                                <img src="{{ asset($poster->image_path) }}" 
                                                     alt="{{ $poster->title }}" 
                                                     class="w-full h-auto max-h-[460px] object-contain rounded-none">
                                        @if ($destUrl)
                                            </a>
                                        @else
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <!-- Default Fallback Slide 1: แบบประเมินความผูกพัน -->
                                <div class="poster-slide block w-full h-full p-4 sm:p-6 bg-[#515273] dark:bg-[#202438] flex flex-col justify-between rounded-none">
                                    <div class="bg-[#fdfaf2] text-[#2d3047] rounded-none px-4 py-3 sm:px-6 sm:py-3.5 flex items-center justify-center gap-3 shadow-md mb-4 border border-amber-200/50">
                                        <span class="text-2xl sm:text-3xl text-amber-500 animate-bounce">
                                            <i class="fa-solid fa-bullhorn"></i>
                                        </span>
                                        <div class="text-center">
                                            <h3 class="text-base sm:text-2xl font-black tracking-tight text-slate-800">
                                                แบบประเมินความผูกพันต่อองค์กร
                                            </h3>
                                            <p class="text-[10px] sm:text-xs text-slate-500 font-medium hidden sm:block">
                                                Kumwell Corporation PCL. — Human Assetment & Engagement Survey
                                            </p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div class="bg-[#1f2839]/90 border border-slate-700/60 rounded-none p-3 text-center flex flex-col items-center justify-between shadow-lg">
                                            <div class="text-[11px] sm:text-xs font-semibold leading-snug mb-2 min-h-[36px] flex items-center justify-center text-slate-200">
                                                แบบประเมินความผูกพันต่อองค์กร Kumwell “สายบริหาร”
                                            </div>
                                            <div class="bg-white p-2 rounded-none shadow mb-2">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data=https://hams.appkumwell.com" alt="QR สายบริหาร" class="w-20 h-20 sm:w-22 sm:h-22 object-contain">
                                            </div>
                                            <span class="w-full py-1 px-2 rounded-none bg-slate-800 text-[10px] sm:text-xs font-bold text-slate-200 border border-slate-700">
                                                สายบริหาร
                                            </span>
                                        </div>

                                        <div class="bg-[#1b212f]/90 border border-slate-700/60 rounded-none p-3 text-center flex flex-col items-center justify-between shadow-lg">
                                            <div class="text-[11px] sm:text-xs font-semibold leading-snug mb-2 min-h-[36px] flex items-center justify-center text-slate-200">
                                                แบบประเมินความผูกพันต่อองค์กร Kumwell “สายวิชาการ”
                                            </div>
                                            <div class="bg-white p-2 rounded-none shadow mb-2">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data=https://hams.appkumwell.com" alt="QR สายวิชาการ" class="w-20 h-20 sm:w-22 sm:h-22 object-contain">
                                            </div>
                                            <span class="w-full py-1 px-2 rounded-none bg-red-900/60 text-[10px] sm:text-xs font-bold text-red-200 border border-red-800/50">
                                                สายวิชาการ
                                            </span>
                                        </div>

                                        <div class="bg-[#23314b]/90 border border-slate-700/60 rounded-none p-3 text-center flex flex-col items-center justify-between shadow-lg">
                                            <div class="text-[11px] sm:text-xs font-semibold leading-snug mb-2 min-h-[36px] flex items-center justify-center text-slate-200">
                                                แบบประเมินความผูกพันต่อองค์กร Kumwell “สายสนับสนุน”
                                            </div>
                                            <div class="bg-white p-2 rounded-none shadow mb-2">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data=https://hams.appkumwell.com" alt="QR สายสนับสนุน" class="w-20 h-20 sm:w-22 sm:h-22 object-contain">
                                            </div>
                                            <span class="w-full py-1 px-2 rounded-none bg-blue-900/60 text-[10px] sm:text-xs font-bold text-blue-200 border border-blue-800/50">
                                                สายสนับสนุน
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Default Fallback Slide 2: Kumwell Academy -->
                                <div class="poster-slide hidden w-full h-full p-6 sm:p-8 bg-gradient-to-r from-red-900/90 via-slate-900/95 to-slate-950 flex flex-col justify-center rounded-none">
                                    <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                                        <div class="flex-1 text-left">
                                            <span class="inline-block px-3 py-1 bg-red-600 text-white text-[10px] font-bold rounded-full uppercase tracking-wider mb-3">
                                                Kumwell Academy 2026
                                            </span>
                                            <h4 class="text-lg sm:text-2xl font-bold text-white mb-2">
                                                โครงการฝึกอบรมและพัฒนาทักษะวิชาชีพ
                                            </h4>
                                            <p class="text-xs sm:text-sm text-slate-300 mb-4 leading-relaxed">
                                                เสริมสร้างสมรรถนะการทำงาน มาตรฐานความปลอดภัยสากล และนวัตกรรมระบบต่อลงดินอย่างยั่งยืน
                                            </p>
                                            <a href="{{ route('training.index') }}" class="inline-flex items-center gap-2 bg-white text-red-600 hover:bg-slate-100 font-bold text-xs px-5 py-2.5 rounded-xl shadow transition-all">
                                                ตรวจสอบหลักสูตรอบรม <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                        <div class="bg-white p-3 rounded-xl shadow-lg shrink-0 flex flex-col items-center">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=https://hams.appkumwell.com" alt="QR Kumwell Academy" class="w-24 h-24 sm:w-28 sm:h-28 object-contain">
                                            <span class="text-[10px] text-slate-600 font-bold mt-1.5">สแกนลงทะเบียน</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>

                        <!-- Carousel Nav Prev Button -->
                        <button type="button" id="poster-prev" aria-label="ก่อนหน้า" class="absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-black/40 hover:bg-black/70 text-white backdrop-blur-xs border border-white/20 flex items-center justify-center transition-all shadow-md hover:scale-105 active:scale-95 z-20">
                            <i class="fa-solid fa-chevron-left text-xs sm:text-sm"></i>
                        </button>
                        <!-- Carousel Nav Next Button -->
                        <button type="button" id="poster-next" aria-label="ถัดไป" class="absolute right-3 sm:right-4 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-black/40 hover:bg-black/70 text-white backdrop-blur-xs border border-white/20 flex items-center justify-center transition-all shadow-md hover:scale-105 active:scale-95 z-20">
                            <i class="fa-solid fa-chevron-right text-xs sm:text-sm"></i>
                        </button>

                        <!-- Bottom Slide Dots (Floating subtle glass pill) -->
                        @php
                            $posterCount = (isset($mainPosters) && $mainPosters->count() > 0) ? $mainPosters->count() : 2;
                        @endphp
                        @if ($posterCount > 1)
                            <div class="absolute bottom-3 left-0 right-0 z-20 flex items-center justify-center pointer-events-none">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-black/35 backdrop-blur-xs border border-white/15 pointer-events-auto">
                                    @for ($i = 0; $i < $posterCount; $i++)
                                        <button type="button" class="poster-dot h-1.5 rounded-full transition-all duration-300 {{ $i === 0 ? 'w-5 bg-white' : 'w-1.5 bg-white/50 hover:bg-white' }}" data-slide="{{ $i }}" aria-label="สไลด์ {{ $i + 1 }}"></button>
                                    @endfor
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                <!-- Right Column: 2 Stacked Announcement Banners (Col-span 4 on desktop, 2-column grid on iPad / tablet) -->
                <div class="lg:col-span-4 grid grid-cols-1 md:grid-cols-2 lg:flex lg:flex-col gap-4 sm:gap-5 justify-between reveal opacity-0 translate-y-8 transition-all duration-700 delay-150">
                    
                    <!-- Top Small Banner -->
                    @if (isset($sideTopPoster) && $sideTopPoster)
                        @php $topUrl = $sideTopPoster->action_url ? route('posters.click', $sideTopPoster->id) : null; @endphp
                        @if ($topUrl)
                            <a href="{{ $topUrl }}" class="group relative rounded-none overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 flex-1 flex flex-col min-h-[140px] sm:min-h-[170px]" data-poster-id="{{ $sideTopPoster->id }}">
                        @else
                            <div class="relative rounded-none overflow-hidden shadow-md flex-1 flex flex-col min-h-[140px] sm:min-h-[170px]" data-poster-id="{{ $sideTopPoster->id }}">
                        @endif
                                <img src="{{ asset($sideTopPoster->image_path) }}" alt="{{ $sideTopPoster->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 rounded-none">
                        @if ($topUrl)
                            </a>
                        @else
                            </div>
                        @endif
                    @else
                        <!-- Default Top Banner Fallback: Kumwell Standards -->
                        <a href="https://www.kumwell.com" class="group relative bg-white dark:bg-[#151B26] rounded-none p-4 sm:p-5 shadow-md hover:shadow-lg transition-all duration-300 flex-1 flex flex-col justify-between overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-red-600 via-red-500 to-amber-500"></div>
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 flex items-center justify-center text-sm font-black">
                                        <i class="fa-solid fa-award"></i>
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-bold text-red-600 dark:text-red-400 uppercase tracking-widest block">มาตรฐานองค์กร</span>
                                        <h5 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white leading-tight">Kumwell Global Standards</h5>
                                    </div>
                                </div>
                                <span class="text-[10px] text-slate-400 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </span>
                            </div>
                            <p class="text-[11px] sm:text-xs text-slate-600 dark:text-slate-400 leading-relaxed mb-3">
                                ยึดมั่นนโยบายคุณภาพ สิ่งแวดล้อม และอาชีวอนามัย มุ่งสู่อุตสาหกรรมสีเขียวเพื่อความยั่งยืน
                            </p>
                            <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-800/80 pt-2.5 text-[11px] text-slate-500 dark:text-slate-400">
                                <span class="inline-flex items-center gap-1 font-semibold text-red-600 dark:text-red-500">
                                    <i class="fa-solid fa-shield-halved text-[10px]"></i> CCSV Core Values
                                </span>
                                <span>kumwell.com</span>
                            </div>
                        </a>
                    @endif

                    <!-- Bottom Small Banner -->
                    @if (isset($sideBottomPoster) && $sideBottomPoster)
                        @php $bottomUrl = $sideBottomPoster->action_url ? route('posters.click', $sideBottomPoster->id) : null; @endphp
                        @if ($bottomUrl)
                            <a href="{{ $bottomUrl }}" class="group relative rounded-none overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 flex-1 flex flex-col min-h-[140px] sm:min-h-[170px]" data-poster-id="{{ $sideBottomPoster->id }}">
                        @else
                            <div class="relative rounded-none overflow-hidden shadow-md flex-1 flex flex-col min-h-[140px] sm:min-h-[170px]" data-poster-id="{{ $sideBottomPoster->id }}">
                        @endif
                                <img src="{{ asset($sideBottomPoster->image_path) }}" alt="{{ $sideBottomPoster->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 rounded-none">
                        @if ($bottomUrl)
                            </a>
                        @else
                            </div>
                        @endif
                    @else
                        <!-- Default Bottom Banner Fallback: Recruitment -->
                        <a href="{{ route('recruitment.index') }}" class="group relative bg-gradient-to-br from-white via-white to-red-50/40 dark:from-[#151B26] dark:via-[#151B26] dark:to-red-950/20 rounded-none p-4 sm:p-5 shadow-md hover:shadow-lg transition-all duration-300 flex-1 flex flex-col justify-between overflow-hidden">
                            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-red-600 to-red-700"></div>
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-red-600 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                                        <i class="fa-solid fa-user-plus"></i>
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-bold text-red-600 dark:text-red-400 uppercase tracking-widest block">ร่วมงานกับเรา</span>
                                        <h5 class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white leading-tight">เปิดรับสมัครบุคลากรใหม่</h5>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400 text-[10px] font-bold">
                                    Jobs Opening
                                </span>
                            </div>
                            <div class="flex items-center gap-3 my-2">
                                <div class="flex-1">
                                    <p class="text-[11px] sm:text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                                        ร่วมเป็นส่วนหนึ่งในทีมงานผู้เชี่ยวชาญระดับสากล ดูตำแหน่งงานว่างและส่งใบสมัครออนไลน์
                                    </p>
                                </div>
                                <div class="bg-white p-1 rounded-lg border border-slate-200 dark:border-slate-700 shadow-xs shrink-0">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=https://hams.appkumwell.com/recruitment" alt="QR รับสมัครงาน" class="w-14 h-14 object-contain">
                                </div>
                            </div>
                            <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-800/80 pt-2.5 text-[11px]">
                                <span class="text-slate-500 dark:text-slate-400 font-medium">ฝ่ายทรัพยากรมนุษย์ (HA)</span>
                                <span class="font-bold text-red-600 dark:text-red-500 group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                                    สมัครงานเลย <i class="fas fa-arrow-right text-[10px]"></i>
                                </span>
                            </div>
                        </a>
                    @endif

                </div>

            </div>
        </div>
    </div>

    <!-- ==================== SERVICES STRIP ==================== -->
    <div class="relative z-40 max-w-6xl mx-auto px-4 sm:px-6 pt-6 sm:pt-16 pb-4 sm:pb-8 mb-6 sm:mb-12">
        <!-- Title & Subtitle Section -->
        <div class="text-center mb-5 sm:mb-10 reveal opacity-0 translate-y-8 transition-all duration-700">
            <h2 class="text-lg sm:text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white mb-1.5 sm:mb-3">
                คุณค่าร่วมองค์กร <span class="text-kumwell-red">(CCSV)</span>
            </h2>
            <p class="text-slate-500 dark:text-slate-400 max-w-2xl mx-auto text-xs md:text-sm leading-relaxed">
                วัฒนธรรมร่วมที่พวกเรายึดถือปฏิบัติ เพื่อส่งเสริมความร่วมมือ สร้างสรรค์นวัตกรรม แบ่งปันคุณค่า และยกระดับขีดความสามารถการทำงานร่วมกันสู่สากล
            </p>
        </div>

        <!-- Red Container with White Cards (2 Columns on Mobile, 4 Columns on Desktop) -->
        <div class="bg-kumwell-red p-2.5 sm:p-5 rounded-xl sm:rounded-2xl shadow-xl reveal opacity-0 translate-y-8 transition-all duration-700 delay-100">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-5">
                <!-- Card 1 -->
                <div class="group bg-white p-3.5 sm:p-8 rounded-lg flex flex-col items-center text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-10 h-10 sm:w-20 sm:h-20 rounded-full bg-kumwell-red text-white flex items-center justify-center text-lg sm:text-3xl mb-2 sm:mb-6 group-hover:scale-105 transition-transform shrink-0">
                        <i class="fa-solid fa-handshake"></i>
                    </div>
                    <h3 class="text-kumwell-red font-extrabold text-xs sm:text-base mb-1 sm:mb-3 uppercase tracking-wider">C (Corporation)</h3>
                    <p class="text-slate-600 text-[10px] sm:text-xs md:text-sm leading-tight sm:leading-relaxed">ร่วมมือกับผู้มีส่วนได้เสีย บูรณาการการส่งมอบผลิตภัณฑ์และบริการอย่างมืออาชีพ</p>
                </div>
                <!-- Card 2 -->
                <a href="https://hams.appkumwell.com/" class="group bg-white p-3.5 sm:p-8 rounded-lg flex flex-col items-center text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-10 h-10 sm:w-20 sm:h-20 rounded-full bg-kumwell-red text-white flex items-center justify-center text-lg sm:text-3xl mb-2 sm:mb-6 group-hover:scale-105 transition-transform shrink-0">
                        <i class="fa-solid fa-network-wired"></i>
                    </div>
                    <h3 class="text-kumwell-red font-extrabold text-xs sm:text-base mb-1 sm:mb-3 uppercase tracking-wider">C (Creating)</h3>
                    <p class="text-slate-600 text-[10px] sm:text-xs md:text-sm leading-tight sm:leading-relaxed">สร้างสรรค์งานวิจัยและพัฒนาร่วมกับพันธมิตร เพื่อส่งมอบนวัตกรรมความปลอดภัย</p>
                </a>
                <!-- Card 3 -->
                <div class="group bg-white p-3.5 sm:p-8 rounded-lg flex flex-col items-center text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-10 h-10 sm:w-20 sm:h-20 rounded-full bg-kumwell-red text-white flex items-center justify-center text-lg sm:text-3xl mb-2 sm:mb-6 group-hover:scale-105 transition-transform shrink-0">
                        <i class="fa-solid fa-chalkboard-teacher"></i>
                    </div>
                    <h3 class="text-kumwell-red font-extrabold text-xs sm:text-base mb-1 sm:mb-3 uppercase tracking-wider">S (Shared)</h3>
                    <p class="text-slate-600 text-[10px] sm:text-xs md:text-sm leading-tight sm:leading-relaxed">แบ่งปันและเพิ่มคุณค่าร่วมกับทุกภาคส่วน มุ่งเน้นการส่งต่อความปลอดภัยสู่สังคม</p>
                </div>
                <!-- Card 4 -->
                <div class="group bg-white p-3.5 sm:p-8 rounded-lg flex flex-col items-center text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-10 h-10 sm:w-20 sm:h-20 rounded-full bg-kumwell-red text-white flex items-center justify-center text-lg sm:text-3xl mb-2 sm:mb-6 group-hover:scale-105 transition-transform shrink-0">
                        <i class="fa-solid fa-people-carry-box"></i>
                    </div>
                    <h3 class="text-kumwell-red font-extrabold text-xs sm:text-base mb-1 sm:mb-3 uppercase tracking-wider">V (Value)</h3>
                    <p class="text-slate-600 text-[10px] sm:text-xs md:text-sm leading-tight sm:leading-relaxed">เพิ่มคุณค่าและพัฒนาศักยภาพทุนมนุษย์ ก้าวสู่องค์กรนวัตกรรมระดับสากลสู่ความยั่งยืน</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== ABOUT US ==================== -->
    <div class="relative bg-white dark:bg-[#0B0F17] overflow-hidden border-b border-slate-200 dark:border-slate-800 md:min-h-[500px] xl:min-h-[600px] md:flex md:items-center">
        <!-- Background decorative grid / glow -->
        <div class="absolute inset-0 z-0 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:14px_24px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]"></div>
        <div class="absolute left-1/4 top-1/4 w-96 h-96 bg-red-600/10 dark:bg-red-600/5 rounded-full blur-[128px] pointer-events-none z-0"></div>

        <!-- Peeking Mascot (Anchored to true left edge of screen) -->
        <div class="absolute left-0 top-[120px] xl:top-[140px] w-[130px] lg:w-[160px] xl:w-[220px] z-20 pointer-events-none hidden md:block select-none reveal opacity-0 -translate-x-8 transition-all duration-700">
            <img src="{{ asset('images/welcome/mascot_peeking_cropped.png') }}" alt="Mascot" class="w-full h-auto">
        </div>

        <div class="max-w-7xl mx-auto px-6 w-full relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 md:gap-8 lg:gap-16 items-center">
                <!-- Left: Content -->
                <div class="md:col-span-7 lg:col-span-6 py-12 md:py-16 xl:py-24 md:pl-[140px] lg:pl-[160px] xl:pl-[220px] 2xl:pl-16 reveal opacity-0 -translate-x-8 transition-all duration-700">
                    
                    <!-- Eyebrow text -->
                    <span class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-widest mb-3">
                        ABOUT US
                    </span>
                    
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white leading-tight tracking-tight mb-6">
                        เกี่ยวกับ <span class="text-red-600 dark:text-red-500">Kumwell Group</span>
                    </h2>
                    
                    <p class="text-gray-600 dark:text-slate-400 text-base leading-relaxed mb-8 max-w-xl">
                        บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน) เป็นผู้ผลิตและจัดจำหน่ายผลิตภัณฑ์ในระบบต่อลงดินอย่างครบวงจรตามมาตรฐานสากล 
                        ภายใต้ตราสินค้าแบรนด์ Kumwell ที่ได้รับการยอมรับในระดับสากล มีการส่งออกและตัวแทนจำหน่ายครอบคลุมกว่า 40 ประเทศทั่วโลก
                    </p>

                    <!-- Stats/Highlights Grid -->
                    <div class="grid grid-cols-3 gap-4 sm:gap-6 mb-8 max-w-2xl border-t border-gray-100 dark:border-gray-800 pt-8">
                        {{-- <div>
                            <div class="text-3xl font-black text-red-600 dark:text-red-500">25+</div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-1">ปีแห่งประสบการณ์</div>
                        </div>
                        <div>
                            <div class="text-3xl font-black text-gray-900 dark:text-white">40+</div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-1">ประเทศส่งออก</div>
                        </div>
                        <div>
                            <div class="text-3xl font-black text-gray-900 dark:text-white">100%</div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-1">มาตรฐานสากล</div>
                        </div> --}}
                    </div>

                    <div class="flex flex-wrap gap-4">
                        <a href="https://www.kumwell.com/" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center justify-center border border-gray-900 dark:border-white text-gray-900 dark:text-white hover:bg-gray-900 hover:text-white dark:hover:bg-white dark:hover:text-gray-900 text-sm font-medium px-8 py-3 transition-colors duration-300 rounded-none">
                            View Website
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Full-bleed Image (Absolute on large screens, block on mobile) -->
        <div class="w-full md:absolute md:top-0 md:right-0 md:bottom-0 md:w-[38%] lg:w-[42%] xl:w-[46%] h-[250px] sm:h-[350px] md:h-auto overflow-hidden group reveal opacity-0 translate-x-8 transition-all duration-700">
            <img src="{{ asset('images/welcome/kmlhq.jpg') }}" alt="About Kumwell" loading="lazy" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
            <!-- Floating trust card overlay -->
            {{-- <div class="absolute bottom-6 left-6 right-6 backdrop-blur-md bg-white/95 dark:bg-[#1E2129]/95 border border-white/20 dark:border-white/5 shadow-xl rounded-xl p-4 flex items-center gap-4 transition-transform duration-300 group-hover:-translate-y-1">
                <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-500/10 flex items-center justify-center text-red-600 dark:text-red-500">
                    <i class="fa-solid fa-shield-halved text-lg"></i>
                </div>
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">KUMWELL HQ</span>
                    <span class="block text-sm font-bold text-gray-900 dark:text-white">ผู้นำระบบป้องกันฟ้าผ่าและต่อลงดิน</span>
                </div>
            </div> --}}
        </div>
    </div>

    <!-- ==================== HR SERVICES ==================== -->
    <div id="services-grid" class="py-24 bg-slate-50 dark:bg-[#0B0F17] border-y border-slate-200 dark:border-slate-800/80 relative overflow-hidden scroll-mt-20">
        <!-- Optional Watermark Background (Muted) -->
        <div class="absolute inset-0 z-0 opacity-5 dark:opacity-10 bg-cover bg-bottom bg-fixed bg-no-repeat pointer-events-none"
             style="background-image: url('{{ asset('images/welcome/plant_night.png') }}');">
        </div>

        <div class="max-w-6xl mx-auto px-6 relative z-10">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                    ระบบบริการงานบุคคล
                </h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-2 max-w-md mx-auto">
                    ศูนย์รวมระบบงานและบริการสำหรับพนักงานและฝ่ายบริหาร
                </p>
            </div>

            @php
                $isHrOrAdmin = Auth::check() && Auth::user()->isHrOrAdmin();
            @endphp

            @if ($isHrOrAdmin)
                <!-- Admin & HR Layout: 5 Services (Balanced & Fully Clickable) -->
                <div class="flex flex-wrap justify-center gap-5 sm:gap-6">
                    
                    <!-- Card 1: HR Request -->
                    <div class="relative group cursor-pointer w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] bg-white dark:bg-[#151B26] border border-slate-200 dark:border-slate-800 hover:border-red-500 dark:hover:border-red-500 hover:shadow-xl hover:shadow-slate-300/60 dark:hover:shadow-black/70 hover:ring-2 hover:ring-red-500/20 dark:hover:ring-red-500/25 hover:-translate-y-1.5 rounded-xl p-6 flex flex-col justify-between transition-all duration-200 ease-out overflow-hidden">
                        <div class="absolute top-0 inset-x-0 h-1 bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                        <div>
                            <div class="w-11 h-11 rounded-lg bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-red-600 dark:group-hover:text-white group-hover:scale-105 group-hover:shadow-md group-hover:shadow-red-600/30 flex items-center justify-center text-lg mb-4 transition-all duration-200">
                                <i class="fa-regular fa-file-lines"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors mb-1.5">
                                ระบบจัดการคำร้อง
                            </h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed min-h-[38px]">
                                ยื่นเอกสารขอรับรอง ลางาน และปรับเวลาทำงาน พร้อมติดตามผลการอนุมัติ
                            </p>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 group-hover:border-slate-200 dark:group-hover:border-slate-700/80 flex items-center justify-between text-xs transition-colors">
                            <a href="{{ route('request.hr') }}" class="font-semibold text-red-600 dark:text-red-400 group-hover:text-red-700 dark:group-hover:text-red-300 inline-flex items-center gap-2 after:absolute after:inset-0 focus:outline-none">
                                <span>เปิดใช้งานระบบ</span>
                                <span class="w-5 h-5 rounded-full bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-red-600 dark:group-hover:text-white flex items-center justify-center text-[10px] transition-all duration-200 group-hover:translate-x-1">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </span>
                            </a>
                            <a href="{{ route('request.hr') }}" class="relative z-10 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 hover:underline transition-colors">
                                ขอหนังสือรับรอง
                            </a>
                        </div>
                    </div>

                    <!-- Card 2: Manpower -->
                    <div class="relative group cursor-pointer w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] bg-white dark:bg-[#151B26] border border-slate-200 dark:border-slate-800 hover:border-red-500 dark:hover:border-red-500 hover:shadow-xl hover:shadow-slate-300/60 dark:hover:shadow-black/70 hover:ring-2 hover:ring-red-500/20 dark:hover:ring-red-500/25 hover:-translate-y-1.5 rounded-xl p-6 flex flex-col justify-between transition-all duration-200 ease-out overflow-hidden">
                        <div class="absolute top-0 inset-x-0 h-1 bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                        <div>
                            <div class="w-11 h-11 rounded-lg bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-red-600 dark:group-hover:text-white group-hover:scale-105 group-hover:shadow-md group-hover:shadow-red-600/30 flex items-center justify-center text-lg mb-4 transition-all duration-200">
                                <i class="fa-solid fa-users-gear"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors mb-1.5">
                                ระบบอัตรากำลังพล
                            </h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed min-h-[38px]">
                                วางแผนและวิเคราะห์อัตรากำลังคน ตรวจสอบโควต้า และจัดทำคำขออัตรากำลัง
                            </p>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 group-hover:border-slate-200 dark:group-hover:border-slate-700/80 flex items-center justify-between text-xs transition-colors">
                            <a href="{{ route('manpower.dashboard') }}" class="font-semibold text-red-600 dark:text-red-400 group-hover:text-red-700 dark:group-hover:text-red-300 inline-flex items-center gap-2 after:absolute after:inset-0 focus:outline-none">
                                <span>เข้าสู่ระบบกำลังพล</span>
                                <span class="w-5 h-5 rounded-full bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-red-600 dark:group-hover:text-white flex items-center justify-center text-[10px] transition-all duration-200 group-hover:translate-x-1">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </span>
                            </a>
                            <a href="{{ route('manpower-request.index') }}" class="relative z-10 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 hover:underline transition-colors">
                                ขออัตรากำลัง (QF-HR-13)
                            </a>
                        </div>
                    </div>

                    <!-- Card 3: Training -->
                    <div class="relative group cursor-pointer w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] bg-white dark:bg-[#151B26] border border-slate-200 dark:border-slate-800 hover:border-red-500 dark:hover:border-red-500 hover:shadow-xl hover:shadow-slate-300/60 dark:hover:shadow-black/70 hover:ring-2 hover:ring-red-500/20 dark:hover:ring-red-500/25 hover:-translate-y-1.5 rounded-xl p-6 flex flex-col justify-between transition-all duration-200 ease-out overflow-hidden">
                        <div class="absolute top-0 inset-x-0 h-1 bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                        <div>
                            <div class="w-11 h-11 rounded-lg bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-red-600 dark:group-hover:text-white group-hover:scale-105 group-hover:shadow-md group-hover:shadow-red-600/30 flex items-center justify-center text-lg mb-4 transition-all duration-200">
                                <i class="fa-solid fa-chalkboard-user"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors mb-1.5">
                                ระบบฝึกอบรม
                            </h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed min-h-[38px]">
                                หลักสูตรฝึกอบรมภายในและภายนอกองค์กร พร้อมบันทึกประวัติการพัฒนาทักษะ
                            </p>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 group-hover:border-slate-200 dark:group-hover:border-slate-700/80 flex items-center justify-between text-xs transition-colors">
                            <a href="{{ route('training.index') }}" class="font-semibold text-red-600 dark:text-red-400 group-hover:text-red-700 dark:group-hover:text-red-300 inline-flex items-center gap-2 after:absolute after:inset-0 focus:outline-none">
                                <span>เข้าชมหลักสูตร</span>
                                <span class="w-5 h-5 rounded-full bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-red-600 dark:group-hover:text-white flex items-center justify-center text-[10px] transition-all duration-200 group-hover:translate-x-1">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </span>
                            </a>
                            <a href="{{ route('training.dashboard') }}" class="relative z-10 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 hover:underline transition-colors">
                                แดชบอร์ดฝึกอบรม
                            </a>
                        </div>
                    </div>

                    <!-- Card 4: Recruitment -->
                    <div class="relative group cursor-pointer w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] bg-white dark:bg-[#151B26] border border-slate-200 dark:border-slate-800 hover:border-red-500 dark:hover:border-red-500 hover:shadow-xl hover:shadow-slate-300/60 dark:hover:shadow-black/70 hover:ring-2 hover:ring-red-500/20 dark:hover:ring-red-500/25 hover:-translate-y-1.5 rounded-xl p-6 flex flex-col justify-between transition-all duration-200 ease-out overflow-hidden">
                        <div class="absolute top-0 inset-x-0 h-1 bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                        <div>
                            <div class="w-11 h-11 rounded-lg bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-red-600 dark:group-hover:text-white group-hover:scale-105 group-hover:shadow-md group-hover:shadow-red-600/30 flex items-center justify-center text-lg mb-4 transition-all duration-200">
                                <i class="fa-solid fa-briefcase"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors mb-1.5">
                                ระบบรับสมัครงาน
                            </h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed min-h-[38px]">
                                ประกาศรับสมัครงาน ส่งใบสมัครออนไลน์ และติดตามผลการคัดเลือกบุคลากร
                            </p>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 group-hover:border-slate-200 dark:group-hover:border-slate-700/80 flex items-center justify-between text-xs transition-colors">
                            <a href="{{ route('recruitment.index') }}" class="font-semibold text-red-600 dark:text-red-400 group-hover:text-red-700 dark:group-hover:text-red-300 inline-flex items-center gap-2 after:absolute after:inset-0 focus:outline-none">
                                <span>ดูตำแหน่งงานว่าง</span>
                                <span class="w-5 h-5 rounded-full bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-red-600 dark:group-hover:text-white flex items-center justify-center text-[10px] transition-all duration-200 group-hover:translate-x-1">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </span>
                            </a>
                            <a href="{{ route('recruitment.track') }}" class="relative z-10 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 hover:underline transition-colors">
                                ติดตามการสมัคร
                            </a>
                        </div>
                    </div>

                    <!-- Card 5: Data Management -->
                    <div class="relative group cursor-pointer w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] bg-white dark:bg-[#151B26] border border-slate-200 dark:border-slate-800 hover:border-red-500 dark:hover:border-red-500 hover:shadow-xl hover:shadow-slate-300/60 dark:hover:shadow-black/70 hover:ring-2 hover:ring-red-500/20 dark:hover:ring-red-500/25 hover:-translate-y-1.5 rounded-xl p-6 flex flex-col justify-between transition-all duration-200 ease-out overflow-hidden">
                        <div class="absolute top-0 inset-x-0 h-1 bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                        <div>
                            <div class="w-11 h-11 rounded-lg bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-red-600 dark:group-hover:text-white group-hover:scale-105 group-hover:shadow-md group-hover:shadow-red-600/30 flex items-center justify-center text-lg mb-4 transition-all duration-200">
                                <i class="fa-solid fa-database"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors mb-1.5">
                                ระบบจัดการข้อมูล
                            </h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed min-h-[38px]">
                                ฐานข้อมูลสารสนเทศพนักงาน โครงสร้างฝ่าย แผนก และการกำหนดค่าแบบฟอร์ม
                            </p>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 group-hover:border-slate-200 dark:group-hover:border-slate-700/80 flex items-center justify-between text-xs transition-colors">
                            <a href="{{ route('request.data') }}" class="font-semibold text-red-600 dark:text-red-400 group-hover:text-red-700 dark:group-hover:text-red-300 inline-flex items-center gap-2 after:absolute after:inset-0 focus:outline-none">
                                <span>เข้าสู่ระบบข้อมูล</span>
                                <span class="w-5 h-5 rounded-full bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-red-600 dark:group-hover:text-white flex items-center justify-center text-[10px] transition-all duration-200 group-hover:translate-x-1">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </span>
                            </a>
                            <a href="{{ route('request-types.index') }}" class="relative z-10 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 hover:underline transition-colors">
                                ประเภทคำร้อง
                            </a>
                        </div>
                    </div>

                </div>
            @else
                <!-- Normal User / Staff Layout: 3 Services (Equal 3 Columns & Fully Clickable) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6 max-w-6xl mx-auto">
                    
                    <!-- Card 1: HR Request -->
                    <div class="relative group cursor-pointer bg-white dark:bg-[#151B26] border border-slate-200 dark:border-slate-800 hover:border-red-500 dark:hover:border-red-500 hover:shadow-xl hover:shadow-slate-300/60 dark:hover:shadow-black/70 hover:ring-2 hover:ring-red-500/20 dark:hover:ring-red-500/25 hover:-translate-y-1.5 rounded-xl p-6 flex flex-col justify-between transition-all duration-200 ease-out overflow-hidden">
                        <div class="absolute top-0 inset-x-0 h-1 bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                        <div>
                            <div class="w-11 h-11 rounded-lg bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-red-600 dark:group-hover:text-white group-hover:scale-105 group-hover:shadow-md group-hover:shadow-red-600/30 flex items-center justify-center text-lg mb-4 transition-all duration-200">
                                <i class="fa-regular fa-file-lines"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors mb-1.5">
                                ระบบจัดการคำร้อง
                            </h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed min-h-[38px]">
                                ดำเนินการยื่นขอเอกสาร การลางาน และการปรับแก้ไขเวลาทำงาน พร้อมติดตามผลอนุมัติ
                            </p>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 group-hover:border-slate-200 dark:group-hover:border-slate-700/80 flex items-center justify-between text-xs transition-colors">
                            @auth
                                <a href="{{ route('request.hr') }}" class="font-semibold text-red-600 dark:text-red-400 group-hover:text-red-700 dark:group-hover:text-red-300 inline-flex items-center gap-2 after:absolute after:inset-0 focus:outline-none">
                                    <span>เปิดใช้งานระบบ</span>
                                    <span class="w-5 h-5 rounded-full bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-red-600 dark:group-hover:text-white flex items-center justify-center text-[10px] transition-all duration-200 group-hover:translate-x-1">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </span>
                                </a>
                                <a href="{{ route('request.hr') }}" class="relative z-10 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 hover:underline transition-colors">
                                    ขอหนังสือรับรอง
                                </a>
                            @else
                                <button type="button" class="login-open-btn font-semibold text-red-600 dark:text-red-400 group-hover:text-red-700 dark:group-hover:text-red-300 inline-flex items-center gap-2 after:absolute after:inset-0 focus:outline-none text-left">
                                    <span>เปิดใช้งานระบบ</span>
                                    <span class="w-5 h-5 rounded-full bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-red-600 dark:group-hover:text-white flex items-center justify-center text-[10px] transition-all duration-200 group-hover:translate-x-1">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </span>
                                </button>
                                <button type="button" class="login-open-btn relative z-10 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 hover:underline transition-colors">
                                    ขอหนังสือรับรอง
                                </button>
                            @endauth
                        </div>
                    </div>

                    <!-- Card 2: Training -->
                    <div class="relative group cursor-pointer bg-white dark:bg-[#151B26] border border-slate-200 dark:border-slate-800 hover:border-red-500 dark:hover:border-red-500 hover:shadow-xl hover:shadow-slate-300/60 dark:hover:shadow-black/70 hover:ring-2 hover:ring-red-500/20 dark:hover:ring-red-500/25 hover:-translate-y-1.5 rounded-xl p-6 flex flex-col justify-between transition-all duration-200 ease-out overflow-hidden">
                        <div class="absolute top-0 inset-x-0 h-1 bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                        <div>
                            <div class="w-11 h-11 rounded-lg bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-red-600 dark:group-hover:text-white group-hover:scale-105 group-hover:shadow-md group-hover:shadow-red-600/30 flex items-center justify-center text-lg mb-4 transition-all duration-200">
                                <i class="fa-solid fa-chalkboard-user"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors mb-1.5">
                                ระบบฝึกอบรม
                            </h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed min-h-[38px]">
                                พัฒนาศักยภาพการปฏิบัติงานผ่านหลักสูตรและการฝึกอบรมในองค์กรอย่างต่อเนื่อง
                            </p>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 group-hover:border-slate-200 dark:group-hover:border-slate-700/80 flex items-center justify-between text-xs transition-colors">
                            @auth
                                <a href="{{ route('training.index') }}" class="font-semibold text-red-600 dark:text-red-400 group-hover:text-red-700 dark:group-hover:text-red-300 inline-flex items-center gap-2 after:absolute after:inset-0 focus:outline-none">
                                    <span>เข้าชมหลักสูตร</span>
                                    <span class="w-5 h-5 rounded-full bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-red-600 dark:group-hover:text-white flex items-center justify-center text-[10px] transition-all duration-200 group-hover:translate-x-1">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </span>
                                </a>
                                <a href="{{ route('training.dashboard') }}" class="relative z-10 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 hover:underline transition-colors">
                                    แดชบอร์ดฝึกอบรม
                                </a>
                            @else
                                <button type="button" class="login-open-btn font-semibold text-red-600 dark:text-red-400 group-hover:text-red-700 dark:group-hover:text-red-300 inline-flex items-center gap-2 after:absolute after:inset-0 focus:outline-none text-left">
                                    <span>เข้าชมหลักสูตร</span>
                                    <span class="w-5 h-5 rounded-full bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-red-600 dark:group-hover:text-white flex items-center justify-center text-[10px] transition-all duration-200 group-hover:translate-x-1">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </span>
                                </button>
                                <button type="button" class="login-open-btn relative z-10 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 hover:underline transition-colors">
                                    แดชบอร์ดฝึกอบรม
                                </button>
                            @endauth
                        </div>
                    </div>

                    <!-- Card 3: Recruitment -->
                    <div class="relative group cursor-pointer bg-white dark:bg-[#151B26] border border-slate-200 dark:border-slate-800 hover:border-red-500 dark:hover:border-red-500 hover:shadow-xl hover:shadow-slate-300/60 dark:hover:shadow-black/70 hover:ring-2 hover:ring-red-500/20 dark:hover:ring-red-500/25 hover:-translate-y-1.5 rounded-xl p-6 flex flex-col justify-between transition-all duration-200 ease-out overflow-hidden">
                        <div class="absolute top-0 inset-x-0 h-1 bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                        <div>
                            <div class="w-11 h-11 rounded-lg bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-red-600 dark:group-hover:text-white group-hover:scale-105 group-hover:shadow-md group-hover:shadow-red-600/30 flex items-center justify-center text-lg mb-4 transition-all duration-200">
                                <i class="fa-solid fa-briefcase"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors mb-1.5">
                                ระบบรับสมัครงาน
                            </h3>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed min-h-[38px]">
                                ค้นหาตำแหน่งงานที่เปิดรับสมัคร ส่งใบสมัครออนไลน์ และติดตามสถานะ
                            </p>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 group-hover:border-slate-200 dark:group-hover:border-slate-700/80 flex items-center justify-between text-xs transition-colors">
                            <a href="{{ route('recruitment.index') }}" class="font-semibold text-red-600 dark:text-red-400 group-hover:text-red-700 dark:group-hover:text-red-300 inline-flex items-center gap-2 after:absolute after:inset-0 focus:outline-none">
                                <span>ดูตำแหน่งงานว่าง</span>
                                <span class="w-5 h-5 rounded-full bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 group-hover:bg-red-600 group-hover:text-white dark:group-hover:bg-red-600 dark:group-hover:text-white flex items-center justify-center text-[10px] transition-all duration-200 group-hover:translate-x-1">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </span>
                            </a>
                            <a href="{{ route('recruitment.track') }}" class="relative z-10 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-200 hover:underline transition-colors">
                                ตรวจสอบสถานะ
                            </a>
                        </div>
                    </div>

                </div>
            @endif
        </div>
    </div>

    <!-- ==================== BLOG / NEWS ==================== -->
    <div id="news-grid" class="py-12 sm:py-16 bg-white dark:bg-[#0B0F17] border-t border-slate-200 dark:border-slate-800 relative scroll-mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header (Centered Title like Reference) -->
            <div class="text-center mb-6 sm:mb-8 reveal opacity-0 translate-y-6 transition-all duration-500">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight">
                    ข่าวสาร/กิจกรรม
                </h2>
            </div>

            @php
                $thai_full_months = [
                    1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
                    5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
                    9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
                ];

                $formatThaiFullDate = function($raw_date) use ($thai_full_months) {
                    if (!$raw_date) return '1 ตุลาคม 2569';
                    try {
                        $d = \Carbon\Carbon::parse($raw_date);
                        return $d->day . ' ' . ($thai_full_months[$d->month] ?? '') . ' ' . ($d->year + 543);
                    } catch (\Exception $e) {
                        return '1 ตุลาคม 2569';
                    }
                };

                // Sample fallback items to complete categories
                $fallbackNewsList = [
                    [
                        'news_id' => 1,
                        'title' => 'ผลการประกวดออกแบบ นวัตกรรมระบบความปลอดภัย Kumwell SafeTech 2026',
                        'subtitle' => 'บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)',
                        'published_date' => '2026-10-01',
                        'category' => 'all events ccsv',
                        'image_url' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'news_id' => 2,
                        'title' => 'พิธีมอบเกียรติบัตรและทุนพัฒนาศักยภาพบุคลากร ประจำปี 2569',
                        'subtitle' => 'วันที่ 5-8 ตุลาคม 2569',
                        'published_date' => '2026-08-05',
                        'category' => 'all training',
                        'image_url' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'news_id' => 3,
                        'title' => 'คู่มือการเข้าใช้งานระบบบริการบุคคล Mobile App รูปแบบใหม่ เพิ่มความปลอดภัย',
                        'subtitle' => 'เริ่มวันที่ 30 กันยายน 2569',
                        'published_date' => '2026-09-28',
                        'category' => 'all training ccsv',
                        'image_url' => 'https://images.unsplash.com/photo-1551650975-87deedd944c3?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'news_id' => 4,
                        'title' => 'ขอเชิญร่วมงาน Kumwell Innovation & Safety Tech Expo 2026 เข้าชมฟรี',
                        'subtitle' => 'วันที่ 5–8 พฤศจิกายน 2569',
                        'published_date' => '2026-10-01',
                        'category' => 'all events',
                        'image_url' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'news_id' => 5,
                        'title' => 'สิทธิประโยชน์และบริการสวัสดิการด้านสุขอนามัยสำหรับบุคลากร Kumwell Group',
                        'subtitle' => 'ฝ่ายทรัพยากรมนุษย์ (HA)',
                        'published_date' => '2026-09-23',
                        'category' => 'all jobs',
                        'image_url' => 'https://images.unsplash.com/photo-1577495508048-b635879837f1?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'news_id' => 6,
                        'title' => 'เปิดรับสมัครบุคลากรใหม่หลายตำแหน่ง ร่วมงานกับครอบครัว Kumwell',
                        'subtitle' => 'รับสมัครด่วน ฝ่ายวิศวกรรมและการผลิต',
                        'published_date' => '2026-09-20',
                        'category' => 'all jobs',
                        'image_url' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'news_id' => 7,
                        'title' => 'ประกาศผลการจัดซื้อจัดจ้างวัสดุอุปกรณ์ห้องปฏิบัติการทดสอบ ประจำไตรมาส 3/2569',
                        'subtitle' => 'ฝ่ายจัดซื้อและคลังสินค้า',
                        'published_date' => '2026-09-18',
                        'category' => 'all procure',
                        'image_url' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=600&q=80',
                    ],
                    [
                        'news_id' => 8,
                        'title' => 'สัมมนาวิชาการมาตรฐานระบบต่อลงดินและป้องกันฟ้าผ่าสู่อาเซียน',
                        'subtitle' => 'ศูนย์ฝึกอบรม Kumwell Academy',
                        'published_date' => '2026-09-15',
                        'category' => 'all events training',
                        'image_url' => 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?auto=format&fit=crop&w=600&q=80',
                    ],
                ];

                // Build unified collection with DB items first
                $newsCardList = collect();
                if (isset($newsItems) && $newsItems->count() > 0) {
                    foreach ($newsItems as $n) {
                        $img = !empty($n->image_path) ? asset(is_array($n->image_path) ? $n->image_path[0] : $n->image_path) : null;
                        $newsCardList->push((object)[
                            'news_id' => $n->news_id,
                            'title' => $n->title,
                            'subtitle' => 'บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)',
                            'formatted_date' => $formatThaiFullDate($n->published_date ?? $n->created_at),
                            'category' => 'all ' . ($n->newto ?: 'events'),
                            'image_url' => $img ?: 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=600&q=80',
                            'is_real' => true,
                        ]);
                    }
                }

                // Fill from fallback list
                foreach ($fallbackNewsList as $fb) {
                    if ($newsCardList->count() < 10) {
                        $newsCardList->push((object)[
                            'news_id' => $fb['news_id'],
                            'title' => $fb['title'],
                            'subtitle' => $fb['subtitle'],
                            'formatted_date' => $formatThaiFullDate($fb['published_date']),
                            'category' => $fb['category'],
                            'image_url' => $fb['image_url'],
                            'is_real' => false,
                        ]);
                    }
                }
            @endphp

            <!-- Category Tabs Navigation (KU-Style Tab Bar) -->
            <div class="mb-7 border-b border-slate-300 dark:border-slate-800 flex justify-center overflow-x-auto scrollbar-none reveal opacity-0 translate-y-6 transition-all duration-500 delay-75">
                <div class="flex items-center gap-1 min-w-max">
                    <button type="button" 
                            class="news-category-tab px-4 sm:px-6 py-2.5 text-xs sm:text-sm font-semibold transition-all rounded-none bg-[#1e242b] text-white border-b-2 border-[#1e242b] active" 
                            data-filter="all">
                        ข่าวประชาสัมพันธ์
                    </button>
                    <button type="button" 
                            class="news-category-tab px-4 sm:px-6 py-2.5 text-xs sm:text-sm font-medium transition-all rounded-none text-slate-600 dark:text-slate-300 hover:text-black dark:hover:text-white border-b-2 border-transparent hover:bg-slate-50 dark:hover:bg-slate-800" 
                            data-filter="events">
                        กิจกรรม/สัมมนา
                    </button>
                    <button type="button" 
                            class="news-category-tab px-4 sm:px-6 py-2.5 text-xs sm:text-sm font-medium transition-all rounded-none text-slate-600 dark:text-slate-300 hover:text-black dark:hover:text-white border-b-2 border-transparent hover:bg-slate-50 dark:hover:bg-slate-800" 
                            data-filter="training">
                        การศึกษา/ฝึกอบรม
                    </button>
                    <button type="button" 
                            class="news-category-tab px-4 sm:px-6 py-2.5 text-xs sm:text-sm font-medium transition-all rounded-none text-slate-600 dark:text-slate-300 hover:text-black dark:hover:text-white border-b-2 border-transparent hover:bg-slate-50 dark:hover:bg-slate-800" 
                            data-filter="procure">
                        จัดซื้อจัดจ้าง
                    </button>
                    <button type="button" 
                            class="news-category-tab px-4 sm:px-6 py-2.5 text-xs sm:text-sm font-medium transition-all rounded-none text-slate-600 dark:text-slate-300 hover:text-black dark:hover:text-white border-b-2 border-transparent hover:bg-slate-50 dark:hover:bg-slate-800" 
                            data-filter="jobs">
                        รับสมัครบุคลากร
                    </button>
                    <button type="button" 
                            class="news-category-tab px-4 sm:px-6 py-2.5 text-xs sm:text-sm font-medium transition-all rounded-none text-slate-600 dark:text-slate-300 hover:text-black dark:hover:text-white border-b-2 border-transparent hover:bg-slate-50 dark:hover:bg-slate-800" 
                            data-filter="ccsv">
                        นวัตกรรม CCSV
                    </button>
                </div>
            </div>

            <!-- News Cards Grid (Single Horizontal Row of 5 Cards with Landscape Images) -->
            <div id="news-grid-cards" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 sm:gap-5 items-stretch reveal opacity-0 translate-y-6 transition-all duration-500 delay-100">
                @foreach ($newsCardList as $idx => $card)
                    <a href="{{ $card->is_real ? route('news.detail', $card->news_id) : route('news.newsAll') }}" 
                       class="news-card-item group bg-white dark:bg-[#151B26] border border-slate-200 dark:border-slate-800 rounded-none shadow-xs hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 transition-all flex flex-col h-full overflow-hidden {{ $idx >= 5 ? 'hidden' : '' }}"
                       style="{{ $idx >= 5 ? 'display: none !important;' : '' }}"
                       data-categories="{{ $card->category }}">
                        
                        <!-- Landscape Thumbnail Image Top (แนวนอน 16:10 / 16:9) -->
                        <div class="relative aspect-[16/10] sm:aspect-[16/9] w-full overflow-hidden bg-slate-100 dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800/80">
                            <img src="{{ $card->image_url }}" 
                                 alt="{{ $card->title }}" 
                                 loading="lazy" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                        
                        <!-- Content Body -->
                        <div class="p-3 sm:p-3.5 flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors line-clamp-2 leading-snug mb-1.5 min-h-[2.5rem]">
                                    {{ $card->title }}
                                </h3>
                                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 line-clamp-1">
                                    {{ $card->subtitle }}
                                </p>
                            </div>
                            
                            <!-- Date at bottom left -->
                            <div class="mt-3 pt-2 border-t border-slate-100/90 dark:border-slate-800/70">
                                <span class="text-[11px] sm:text-xs text-slate-400">
                                    {{ $card->formatted_date }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Centered Bottom Button 'ข่าวทั้งหมด >' (Square Dark Button) -->
            <div class="text-center mt-8 sm:mt-10 reveal opacity-0 translate-y-6 transition-all duration-500 delay-150">
                <a href="{{ route('news.newsAll') }}" 
                   class="inline-flex items-center gap-2 bg-[#212529] hover:bg-black text-white text-xs sm:text-sm font-medium px-6 py-2.5 rounded-none shadow-xs hover:shadow transition-colors">
                    ข่าวทั้งหมด <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>

        </div>
    </div>

    <!-- Script for Category Tabs Filtering -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.news-category-tab');
            const cards = document.querySelectorAll('.news-card-item');

            tabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    const filter = this.getAttribute('data-filter');

                    // Update Tab active styles
                    tabs.forEach(t => {
                        t.classList.remove('bg-[#1e242b]', 'text-white', 'border-[#1e242b]', 'active', 'font-semibold');
                        t.classList.add('text-slate-600', 'dark:text-slate-300', 'border-transparent', 'font-medium');
                    });
                    this.classList.add('bg-[#1e242b]', 'text-white', 'border-[#1e242b]', 'active', 'font-semibold');
                    this.classList.remove('text-slate-600', 'dark:text-slate-300', 'border-transparent', 'font-medium');

                    // Filter Cards: show up to 5 matching cards in 1 row
                    let matchCount = 0;
                    cards.forEach(card => {
                        const categories = card.getAttribute('data-categories') || '';
                        const matches = filter === 'all' || categories.includes(filter);

                        if (matches && matchCount < 5) {
                            card.classList.remove('hidden');
                            card.style.setProperty('display', 'flex', 'important');
                            matchCount++;
                        } else {
                            card.classList.add('hidden');
                            card.style.setProperty('display', 'none', 'important');
                        }
                    });
                });
            });

            // Initialize initial display (show first 5 cards only)
            let initCount = 0;
            cards.forEach(card => {
                if (initCount < 5) {
                    card.classList.remove('hidden');
                    card.style.setProperty('display', 'flex', 'important');
                    initCount++;
                } else {
                    card.classList.add('hidden');
                    card.style.setProperty('display', 'none', 'important');
                }
            });
        });
    </script>



    <!-- Scroll to Top Button -->
    <button id="scroll-to-top" 
        aria-label="เลื่อนขึ้นบนสุด"
        class="fixed bottom-8 right-8 z-50 flex items-center justify-center w-11 h-11 rounded-full bg-gray-100/90 dark:bg-slate-800/90 text-red-600 dark:text-red-500 border border-gray-200 dark:border-gray-700 shadow-md hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white transition-all duration-300 translate-y-16 opacity-0 pointer-events-none hover:scale-110 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500">
        <i class="fa-solid fa-chevron-up text-base"></i>
    </button>

    <style>
        @media (prefers-reduced-motion: reduce) {
            #scroll-to-top {
                transition: none !important;
                transform: none !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const scrollToTopBtn = document.getElementById('scroll-to-top');

            window.addEventListener('scroll', function () {
                if (window.scrollY > 300) {
                    scrollToTopBtn.classList.remove('translate-y-16', 'opacity-0', 'pointer-events-none');
                    scrollToTopBtn.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
                } else {
                    scrollToTopBtn.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
                    scrollToTopBtn.classList.add('translate-y-16', 'opacity-0', 'pointer-events-none');
                }
            });

            scrollToTopBtn.addEventListener('click', function () {
                const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                window.scrollTo({
                    top: 0,
                    behavior: prefersReducedMotion ? 'auto' : 'smooth'
                });
            });

            // Poster Showcase Carousel Script
            const posterSlides = document.querySelectorAll('.poster-slide');
            const posterDots = document.querySelectorAll('.poster-dot');
            const posterPrevBtn = document.getElementById('poster-prev');
            const posterNextBtn = document.getElementById('poster-next');
            const posterCounter = document.getElementById('poster-counter');
            let currentPosterSlide = 0;
            let posterAutoTimer = null;

            function updatePosterUI(index) {
                if (posterCounter && posterSlides.length > 0) {
                    posterCounter.textContent = `${index + 1} / ${posterSlides.length}`;
                }
                posterDots.forEach((dot, i) => {
                    if (i === index) {
                        dot.classList.remove('w-1.5', 'bg-white/50');
                        dot.classList.add('w-5', 'bg-white');
                    } else {
                        dot.classList.remove('w-5', 'bg-white');
                        dot.classList.add('w-1.5', 'bg-white/50');
                    }
                });
            }

            // Track Impressions / Views when poster is actually displayed
            function trackPosterImpression(posterId) {
                if (!posterId) return;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                fetch('{{ route("posters.track-views") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    },
                    body: JSON.stringify({ poster_ids: [posterId] })
                }).catch(() => {});
            }

            const recordedViewIds = new Set();
            function trackVisiblePoster(element) {
                if (!element) return;
                const pid = element.getAttribute('data-poster-id');
                if (pid && !recordedViewIds.has(pid)) {
                    recordedViewIds.add(pid);
                    trackPosterImpression(pid);
                }
            }

            if (posterSlides.length > 0) {
                function showPosterSlide(index) {
                    posterSlides.forEach((slide, i) => {
                        if (i === index) {
                            slide.classList.remove('hidden');
                            slide.classList.add('block');
                            trackVisiblePoster(slide);
                        } else {
                            slide.classList.add('hidden');
                            slide.classList.remove('block');
                        }
                    });
                    currentPosterSlide = index;
                    updatePosterUI(index);
                }

                function nextPosterSlide() {
                    let next = (currentPosterSlide + 1) % posterSlides.length;
                    showPosterSlide(next);
                }

                function prevPosterSlide() {
                    let prev = (currentPosterSlide - 1 + posterSlides.length) % posterSlides.length;
                    showPosterSlide(prev);
                }

                if (posterNextBtn) {
                    posterNextBtn.addEventListener('click', () => {
                        clearInterval(posterAutoTimer);
                        nextPosterSlide();
                        startPosterAutoPlay();
                    });
                }

                if (posterPrevBtn) {
                    posterPrevBtn.addEventListener('click', () => {
                        clearInterval(posterAutoTimer);
                        prevPosterSlide();
                        startPosterAutoPlay();
                    });
                }

                posterDots.forEach((dot, i) => {
                    dot.addEventListener('click', () => {
                        clearInterval(posterAutoTimer);
                        showPosterSlide(i);
                        startPosterAutoPlay();
                    });
                });

                // Touch swipe support for mobile & tablet
                const trackEl = document.getElementById('poster-carousel-track');
                if (trackEl) {
                    let touchStartX = 0;
                    let touchEndX = 0;
                    trackEl.addEventListener('touchstart', (e) => {
                        touchStartX = e.changedTouches[0].screenX;
                    }, { passive: true });
                    trackEl.addEventListener('touchend', (e) => {
                        touchEndX = e.changedTouches[0].screenX;
                        const diffX = touchEndX - touchStartX;
                        if (Math.abs(diffX) > 40) {
                            clearInterval(posterAutoTimer);
                            if (diffX < 0) {
                                nextPosterSlide();
                            } else {
                                prevPosterSlide();
                            }
                            startPosterAutoPlay();
                        }
                    }, { passive: true });
                }

                function startPosterAutoPlay() {
                    posterAutoTimer = setInterval(nextPosterSlide, 6000);
                }

                // Initial track for first visible slide
                trackVisiblePoster(posterSlides[0]);
                updatePosterUI(0);
                startPosterAutoPlay();
            }

            // Track banners (hero top posters and side banners) that are displayed directly on screen
            document.querySelectorAll('[data-poster-id]').forEach(el => {
                // If it's not a hidden slide, track it
                if (!el.classList.contains('poster-slide') || el.classList.contains('block')) {
                    trackVisiblePoster(el);
                }
            });
        });
    </script>

    @include('layouts.footer')
@endsection