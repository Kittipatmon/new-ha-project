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
    <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 pt-20 sm:pt-22 md:pt-24 pb-0 md:pb-2">
        <div class="relative w-full {{ $hasCustomHero ? 'aspect-[16/6] sm:aspect-[21/8] md:aspect-[24/8] min-h-[135px] max-h-[440px]' : 'h-[280px] sm:h-[350px] md:h-[450px]' }} rounded-xl sm:rounded-2xl overflow-hidden shadow-sm group">

            @if ($hasCustomHero)
                <!-- Dynamic Hero Slides from Backend (Posters) -->
                @foreach ($heroBannerPosters as $idx => $heroSlide)
                    @php
                        $heroLink = $heroSlide->action_url ? route('posters.click', $heroSlide->id) : null;
                    @endphp
                    <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out {{ $idx === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }} overflow-hidden flex items-center justify-center" data-poster-id="{{ $heroSlide->id }}">
                        @if ($heroLink)
                            <a href="{{ $heroLink }}" class="block w-full h-full relative overflow-hidden flex items-center justify-center">
                        @else
                            <div class="w-full h-full relative overflow-hidden flex items-center justify-center">
                        @endif
                            <img src="{{ asset($heroSlide->image_path) }}" alt="{{ $heroSlide->title }}" class="relative z-10 w-full h-full object-cover">
                        @if ($heroLink)
                            </a>
                        @else
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <!-- Default Image Slides (Fallback) -->
                <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-100 z-10">
                    <img src="{{ asset('images/welcome/hero_industrial.png') }}" alt="Kumwell Plant" class="w-full h-full object-cover">
                </div>
                <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0 z-0">
                    <img src="{{ asset('images/welcome/ro1.jpg') }}" alt="Plant Night" class="w-full h-full object-cover">
                </div>
                <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0 z-0">
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
            <div class="absolute bottom-4 sm:bottom-6 left-0 right-0 z-30 flex justify-center gap-2">
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
    <div class="py-6 sm:py-10 lg:py-12 bg-slate-100/100 dark:bg-[#0c1017] border-b border-slate-200 dark:border-slate-800/80 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5 sm:space-y-7">
            
            <!-- Hero Top Posters (Full-Width Grand Poster / โปสเตอร์ใหญ่หัวข้อใหญ่) -->
            @if (isset($heroTopPosters) && $heroTopPosters->count() > 0)
                <div class="space-y-4 reveal opacity-0 translate-y-8 transition-all duration-700">
                    @foreach ($heroTopPosters as $heroPoster)
                        @php
                            $heroUrl = $heroPoster->action_url ? route('posters.click', $heroPoster->id) : null;
                        @endphp
                        <div class="w-full relative rounded-none overflow-hidden group" data-poster-id="{{ $heroPoster->id }}">
                            @if ($heroUrl)
                                <a href="{{ $heroUrl }}" class="block w-full h-auto">
                            @endif
                                <img src="{{ asset($heroPoster->image_path) }}" 
                                     alt="{{ $heroPoster->title }}" 
                                     class="w-full h-auto object-contain max-h-[500px] sm:max-h-[600px] mx-auto block rounded-none shadow-sm hover:shadow-md transition-all duration-300 group-hover:scale-[1.005]">
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
            <div class="text-center mb-16 reveal opacity-0 translate-y-8 transition-all duration-700">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
                    ระบบบริการ <span class="text-red-600 dark:text-red-500">HR</span>
                </h2>
                <p class="text-slate-600 dark:text-gray-300 max-w-lg mx-auto text-sm leading-relaxed">
                    เลือกใช้งานระบบจัดการและพัฒนาทักษะ เพื่อยกระดับความสามารถในการทำงานร่วมกันอย่างมีประสิทธิภาพ
                </p>
            </div>

            @php
                $isHrOrAdmin = Auth::check() && Auth::user()->isHrOrAdmin();
            @endphp

            @if ($isHrOrAdmin)
                <!-- Bento Grid for Admin / HR (2 Columns on Mobile, 4 Columns on Desktop) -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-6">
                    
                    <!-- Card 1: HR Request -->
                    <div class="col-span-2 h-full reveal opacity-0 translate-y-8 transition-all duration-700">
                        @auth
                            <div class="group bg-white dark:bg-[#151B26]/35 border border-slate-200/60 dark:border-slate-800/80 rounded-xl p-3.5 sm:p-8 transition-all duration-300 hover:border-red-500/30 hover:shadow-md flex flex-col md:flex-row justify-between gap-3 sm:gap-6 relative overflow-hidden h-full w-full">
                        @else
                            <div class="group bg-white dark:bg-[#151B26]/35 border border-slate-200/60 dark:border-slate-800/80 rounded-xl p-3.5 sm:p-8 transition-all duration-300 hover:border-red-500/30 hover:shadow-md flex flex-col md:flex-row justify-between gap-3 sm:gap-6 w-full login-open-btn cursor-pointer relative overflow-hidden h-full">
                        @endauth
                                <div class="flex-1 flex flex-col justify-between">
                                    <div>
                                        <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 flex items-center justify-center text-sm sm:text-xl mb-2 sm:mb-4 group-hover:scale-105 transition-transform">
                                            <i class="fa-regular fa-file-lines"></i>
                                        </div>
                                        <h3 class="text-sm sm:text-xl font-bold text-slate-900 dark:text-white mb-1 sm:mb-2 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors">ระบบจัดการคำร้อง</h3>
                                        <p class="text-[11px] sm:text-xs text-slate-600 dark:text-gray-300 leading-relaxed mb-2 sm:mb-6 line-clamp-2 sm:line-clamp-none">ดำเนินการยื่นขอเอกสาร การลางาน และการปรับแก้ไขเวลาทำงาน พร้อมติดตามความคืบหน้าอย่างรวดเร็ว</p>
                                    </div>
                                    @auth
                                        <a href="{{ route('request.hr') }}" class="inline-flex items-center text-[11px] sm:text-xs font-semibold text-red-600 dark:text-red-500 group-hover:translate-x-1 transition-transform focus:outline-none">
                                            เปิดใช้งานระบบ <i class="fas fa-arrow-right ml-1.5"></i>
                                        </a>
                                    @else
                                        <span class="inline-flex items-center text-[11px] sm:text-xs font-semibold text-red-600 dark:text-red-500 group-hover:translate-x-1 transition-transform">
                                            เปิดใช้งานระบบ <i class="fas fa-arrow-right ml-1.5"></i>
                                        </span>
                                    @endauth
                                </div>
                                
                                <!-- Quick Actions Section inside the card -->
                                <div class="w-full md:w-56 shrink-0 flex flex-col gap-1 sm:gap-2.5 border-t md:border-t-0 md:border-l border-slate-100 dark:border-slate-800/80 pt-2.5 md:pt-0 md:pl-6">
                                    <span class="text-[9px] sm:text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">เมนูด่วน / Quick Links</span>
                                    @auth
                                        <a href="{{ route('request.hr') }}" class="flex items-center gap-2 px-2.5 sm:px-3 py-1.5 sm:py-2 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900/40 dark:hover:bg-slate-800 border border-slate-100 dark:border-slate-800/50 rounded-lg text-[11px] sm:text-xs text-slate-700 dark:text-slate-300 hover:text-kumwell-red dark:hover:text-red-500 font-medium transition-colors">
                                            <i class="fa-solid fa-file-invoice text-[10px] opacity-75"></i> ขอหนังสือรับรอง
                                        </a>
                                    @else
                                        <button type="button" class="login-open-btn flex items-center gap-2 px-2.5 sm:px-3 py-1.5 sm:py-2 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900/40 dark:hover:bg-slate-800 border border-slate-100 dark:border-slate-800/50 rounded-lg text-[11px] sm:text-xs text-slate-700 dark:text-slate-300 hover:text-kumwell-red dark:hover:text-red-500 font-medium transition-colors text-left w-full">
                                            <i class="fa-solid fa-file-invoice text-[10px] opacity-75"></i> ขอหนังสือรับรอง
                                        </button>
                                    @endauth
                                </div>
                            </div>
                    </div>

                    <!-- Card 2: Manpower (Col 1 on Mobile 2-col Grid) -->
                    <div class="col-span-1 h-full reveal opacity-0 translate-y-8 transition-all duration-700 delay-75">
                        <a href="{{ route('manpower.dashboard') }}" class="group bg-white dark:bg-[#151B26]/35 border border-slate-200/60 dark:border-slate-800/80 rounded-xl p-3 sm:p-8 transition-all duration-300 hover:border-red-500/30 hover:shadow-md flex flex-col justify-between h-full min-h-[150px] sm:min-h-[250px]">
                            <div>
                                <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 flex items-center justify-center text-sm sm:text-xl mb-2 sm:mb-4 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-users-gear"></i>
                                </div>
                                <h3 class="text-xs sm:text-lg font-bold text-slate-900 dark:text-white mb-1 sm:mb-2 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors">ระบบอัตรากำลังพล</h3>
                                <p class="text-[11px] sm:text-xs text-slate-600 dark:text-gray-300 leading-tight sm:leading-relaxed line-clamp-2 sm:line-clamp-none">วิเคราะห์แผนกำลังพล ความต้องการของแผนกต่างๆ และการอนุมัติสิทธิ์อัตรากำลัง</p>
                            </div>
                            <span class="inline-flex items-center text-[10px] sm:text-xs font-semibold text-red-600 dark:text-red-500 group-hover:translate-x-1 transition-transform mt-2 sm:mt-4">
                                ดูข้อมูลวิเคราะห์ <i class="fas fa-arrow-right ml-1"></i>
                            </span>
                        </a>
                    </div>

                    <!-- Card 3: Training (Col 2 on Mobile 2-col Grid) -->
                    <div class="col-span-1 h-full reveal opacity-0 translate-y-8 transition-all duration-700 delay-100">
                        @auth
                            <a href="{{ route('training.index') }}" class="group bg-white dark:bg-[#151B26]/35 border border-slate-200/60 dark:border-slate-800/80 rounded-xl p-3 sm:p-8 transition-all duration-300 hover:border-red-500/30 hover:shadow-md flex flex-col justify-between h-full min-h-[150px] sm:min-h-[250px]">
                        @else
                            <button type="button" class="group bg-white dark:bg-[#151B26]/35 border border-slate-200/60 dark:border-slate-800/80 rounded-xl p-3 sm:p-8 text-left transition-all duration-300 hover:border-red-500/30 hover:shadow-md flex flex-col justify-between h-full min-h-[150px] sm:min-h-[250px] login-open-btn w-full">
                        @endauth
                                <div>
                                    <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 flex items-center justify-center text-sm sm:text-xl mb-2 sm:mb-4 group-hover:scale-105 transition-transform">
                                        <i class="fa-solid fa-chalkboard-user"></i>
                                    </div>
                                    <h3 class="text-xs sm:text-lg font-bold text-slate-900 dark:text-white mb-1 sm:mb-2 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors">ระบบฝึกอบรม</h3>
                                    <p class="text-[11px] sm:text-xs text-slate-600 dark:text-gray-300 leading-tight sm:leading-relaxed mb-2 sm:mb-4 line-clamp-2 sm:line-clamp-none">พัฒนาศักยภาพการปฏิบัติงานผ่านหลักสูตรและการฝึกอบรมในองค์กรอย่างต่อเนื่อง</p>
                                    <!-- Course tag badges -->
                                    <div class="flex flex-wrap gap-1 mb-1 sm:mb-2">
                                        <span class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-[9px] sm:text-[10px] text-slate-500 dark:text-slate-400 rounded">#Safety</span>
                                        <span class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-[9px] sm:text-[10px] text-slate-500 dark:text-slate-400 rounded">#Skills</span>
                                    </div>
                                </div>
                                <span class="inline-flex items-center text-[10px] sm:text-xs font-semibold text-red-600 dark:text-red-500 group-hover:translate-x-1 transition-transform mt-2 sm:mt-4">
                                    เข้าชมหลักสูตร <i class="fas fa-arrow-right ml-1"></i>
                                </span>
                        @auth
                            </a>
                        @else
                            </button>
                        @endauth
                    </div>

                    <!-- Card 4: Recruitment -->
                    <div class="col-span-2 lg:col-span-1 h-full reveal opacity-0 translate-y-8 transition-all duration-700 delay-125">
                        <a href="{{ route('recruitment.index') }}" class="group bg-white dark:bg-[#151B26]/35 border border-slate-200/60 dark:border-slate-800/80 rounded-xl p-3 sm:p-8 transition-all duration-300 hover:border-red-500/30 hover:shadow-md flex flex-col justify-between h-full min-h-[150px] sm:min-h-[250px]">
                            <div>
                                <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 flex items-center justify-center text-sm sm:text-xl mb-2 sm:mb-4 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-briefcase"></i>
                                </div>
                                <h3 class="text-xs sm:text-lg font-bold text-slate-900 dark:text-white mb-1 sm:mb-2 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors">ระบบรับสมัครงาน</h3>
                                <p class="text-[11px] sm:text-xs text-slate-600 dark:text-gray-300 leading-tight sm:leading-relaxed mb-2 sm:mb-4 line-clamp-2 sm:line-clamp-none">ค้นหาตำแหน่งงานที่เปิดรับสมัคร ส่งใบสมัครออนไลน์ และติดตามสถานะ</p>
                                <!-- Job tag badges -->
                                <div class="flex flex-wrap gap-1 mb-1 sm:mb-2">
                                    <span class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-[9px] sm:text-[10px] text-slate-500 dark:text-slate-400 rounded">#Careers</span>
                                    <span class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-[9px] sm:text-[10px] text-slate-500 dark:text-slate-400 rounded">#Jobs</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center text-[10px] sm:text-xs font-semibold text-red-600 dark:text-red-500 group-hover:translate-x-1 transition-transform mt-2 sm:mt-4">
                                ดูตำแหน่งงานว่าง <i class="fas fa-arrow-right ml-1"></i>
                            </span>
                        </a>
                    </div>

                    <!-- Card 5: Data Management -->
                    <div class="col-span-2 lg:col-span-3 h-full reveal opacity-0 translate-y-8 transition-all duration-700 delay-150">
                        <a href="{{ route('request.data') }}" class="group bg-white dark:bg-[#151B26]/35 border border-slate-200/60 dark:border-slate-800/80 rounded-xl p-3.5 sm:p-8 transition-all duration-300 hover:border-red-500/30 hover:shadow-md flex flex-col md:flex-row justify-between gap-3 sm:gap-6 relative overflow-hidden h-full">
                            <div class="flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 flex items-center justify-center text-sm sm:text-xl mb-2 sm:mb-4 group-hover:scale-105 transition-transform">
                                        <i class="fa-solid fa-database"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors">ระบบจัดการข้อมูล</h3>
                                    <p class="text-xs text-slate-600 dark:text-gray-300 leading-relaxed mb-6">ตรวจสอบคำร้องจากพนักงาน ปรับปรุงข้อมูลทะเบียนประวัติ และรายงานภาพรวมระบบสารสนเทศบุคคล</p>
                                </div>
                                <span class="inline-flex items-center text-xs font-semibold text-red-600 dark:text-red-500 group-hover:translate-x-1 transition-transform">
                                    เข้าสู่ระบบข้อมูล <i class="fas fa-arrow-right ml-1.5"></i>
                                </span>
                            </div>
                            
                            <!-- Mini Data Stats/Status List inside card -->
                            <div class="w-full md:w-56 shrink-0 flex flex-col gap-2.5 border-t md:border-t-0 md:border-l border-slate-100 dark:border-slate-800/80 pt-6 md:pt-0 md:pl-6">
                                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">ภาพรวมข้อมูล / Stats Overview</span>
                                <div class="flex items-center justify-between px-3 py-2 bg-slate-50 dark:bg-slate-900/40 rounded-lg text-xs">
                                    <span class="text-slate-500">บัญชีพนักงาน</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">Active</span>
                                </div>
                                <div class="flex items-center justify-between px-3 py-2 bg-slate-50 dark:bg-slate-900/40 rounded-lg text-xs">
                                    <span class="text-slate-500">ความปลอดภัยข้อมูล</span>
                                    <span class="font-bold text-green-600 dark:text-green-500">Secure</span>
                                </div>
                                <div class="flex items-center justify-between px-3 py-2 bg-slate-50 dark:bg-slate-900/40 rounded-lg text-xs">
                                    <span class="text-slate-500">ระบบเชื่อมโยงภายนอก</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">Connected</span>
                                </div>
                            </div>
                        </a>
                    </div>

                </div>
            @else
                <!-- Normal User / Staff Layout (2 Columns on Mobile, 3 Columns on Desktop) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-8 max-w-5xl mx-auto">
                    
                    <!-- Card 1: HR Request -->
                    <div class="col-span-1 md:col-span-2 h-full reveal opacity-0 translate-y-8 transition-all duration-700">
                        @auth
                            <div class="group bg-white dark:bg-[#151B26]/35 border border-slate-200/60 dark:border-slate-800/80 rounded-xl p-3.5 sm:p-8 transition-all duration-300 hover:border-red-500/30 hover:shadow-md flex flex-col md:flex-row justify-between gap-3 sm:gap-6 relative overflow-hidden h-full w-full">
                        @else
                            <div class="group bg-white dark:bg-[#151B26]/35 border border-slate-200/60 dark:border-slate-800/80 rounded-xl p-3.5 sm:p-8 transition-all duration-300 hover:border-red-500/30 hover:shadow-md flex flex-col md:flex-row justify-between gap-3 sm:gap-6 w-full login-open-btn cursor-pointer relative overflow-hidden h-full">
                        @endauth
                                <div class="flex-1 flex flex-col justify-between">
                                    <div>
                                        <div class="w-8 h-8 sm:w-12 sm:h-12 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 flex items-center justify-center text-sm sm:text-xl mb-2 sm:mb-4 group-hover:scale-105 transition-transform">
                                            <i class="fa-regular fa-file-lines"></i>
                                        </div>
                                        <h3 class="text-sm sm:text-xl font-bold text-slate-900 dark:text-white mb-1 sm:mb-2 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors">ระบบจัดการคำร้อง</h3>
                                        <p class="text-[11px] sm:text-xs text-slate-600 dark:text-gray-300 leading-relaxed mb-2 sm:mb-6 line-clamp-2 sm:line-clamp-none">ดำเนินการยื่นขอเอกสาร การลางาน และการปรับแก้ไขเวลาทำงาน พร้อมติดตามความคืบหน้าการอนุมัติได้ง่ายๆ</p>
                                    </div>
                                    @auth
                                        <a href="{{ route('request.hr') }}" class="inline-flex items-center text-[11px] sm:text-xs font-semibold text-red-600 dark:text-red-500 group-hover:translate-x-1 transition-transform focus:outline-none">
                                            เปิดใช้งานระบบ <i class="fas fa-arrow-right ml-1.5"></i>
                                        </a>
                                    @else
                                        <span class="inline-flex items-center text-[11px] sm:text-xs font-semibold text-red-600 dark:text-red-500 group-hover:translate-x-1 transition-transform">
                                            เปิดใช้งานระบบ <i class="fas fa-arrow-right ml-1.5"></i>
                                        </span>
                                    @endauth
                                </div>
                                
                                <!-- Quick Actions Section inside the card -->
                                <div class="w-full md:w-56 shrink-0 flex flex-col gap-1 sm:gap-2.5 border-t md:border-t-0 md:border-l border-slate-100 dark:border-slate-800/80 pt-2.5 md:pt-0 md:pl-6">
                                    <span class="text-[9px] sm:text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">เมนูด่วน / Quick Links</span>
                                    @auth
                                        <a href="{{ route('request.hr') }}" class="flex items-center gap-2 px-2.5 sm:px-3 py-1.5 sm:py-2 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900/40 dark:hover:bg-slate-800 border border-slate-100 dark:border-slate-800/50 rounded-lg text-[11px] sm:text-xs text-slate-700 dark:text-slate-300 hover:text-kumwell-red dark:hover:text-red-500 font-medium transition-colors">
                                            <i class="fa-solid fa-file-invoice text-[10px] opacity-75"></i> ขอหนังสือรับรอง
                                        </a>
                                    @else
                                        <button type="button" class="login-open-btn flex items-center gap-2 px-2.5 sm:px-3 py-1.5 sm:py-2 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900/40 dark:hover:bg-slate-800 border border-slate-100 dark:border-slate-800/50 rounded-lg text-[11px] sm:text-xs text-slate-700 dark:text-slate-300 hover:text-kumwell-red dark:hover:text-red-500 font-medium transition-colors text-left w-full">
                                            <i class="fa-solid fa-file-invoice text-[10px] opacity-75"></i> ขอหนังสือรับรอง
                                        </button>
                                    @endauth
                                </div>
                            </div>
                    </div>

                    <!-- Card 2: Training -->
                    <div class="col-span-1 h-full reveal opacity-0 translate-y-8 transition-all duration-700 delay-100">
                        @auth
                            <a href="{{ route('training.index') }}" class="group bg-white dark:bg-[#151B26]/35 border border-slate-200/60 dark:border-slate-800/80 rounded-xl p-8 transition-all duration-300 hover:border-red-500/30 hover:shadow-md flex flex-col justify-between h-full min-h-[250px]">
                        @else
                            <button type="button" class="group bg-white dark:bg-[#151B26]/35 border border-slate-200/60 dark:border-slate-800/80 rounded-xl p-8 text-left transition-all duration-300 hover:border-red-500/30 hover:shadow-md flex flex-col justify-between h-full min-h-[250px] login-open-btn w-full">
                        @endauth
                                <div>
                                    <div class="w-12 h-12 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
                                        <i class="fa-solid fa-chalkboard-user"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors">ระบบฝึกอบรม</h3>
                                    <p class="text-xs text-slate-600 dark:text-gray-300 leading-relaxed mb-4">พัฒนาศักยภาพการปฏิบัติงานผ่านหลักสูตรและการฝึกอบรมในองค์กรอย่างต่อเนื่อง</p>
                                    <!-- Course tag badges -->
                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                        <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-[10px] text-slate-500 dark:text-slate-400 rounded">#Safety</span>
                                        <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-[10px] text-slate-500 dark:text-slate-400 rounded">#Skills</span>
                                        <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-[10px] text-slate-500 dark:text-slate-400 rounded">#Learning</span>
                                    </div>
                                </div>
                                <span class="inline-flex items-center text-xs font-semibold text-red-600 dark:text-red-500 group-hover:translate-x-1 transition-transform mt-4">
                                    เข้าชมหลักสูตร <i class="fas fa-arrow-right ml-1.5"></i>
                                </span>
                        @auth
                            </a>
                        @else
                            </button>
                        @endauth
                    </div>

                    <!-- Card 3: Recruitment -->
                    <div class="col-span-1 h-full reveal opacity-0 translate-y-8 transition-all duration-700 delay-150">
                        <a href="{{ route('recruitment.index') }}" class="group bg-white dark:bg-[#151B26]/35 border border-slate-200/60 dark:border-slate-800/80 rounded-xl p-8 transition-all duration-300 hover:border-red-500/30 hover:shadow-md flex flex-col justify-between h-full min-h-[250px]">
                            <div>
                                <div class="w-12 h-12 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-briefcase"></i>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors">ระบบรับสมัครงาน</h3>
                                <p class="text-xs text-slate-600 dark:text-gray-300 leading-relaxed mb-4">ค้นหาตำแหน่งงานที่เปิดรับสมัคร ส่งใบสมัครออนไลน์ และติดตามสถานะการสมัครงานของคุณ</p>
                                <!-- Job tag badges -->
                                <div class="flex flex-wrap gap-1.5 mb-2">
                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-[10px] text-slate-500 dark:text-slate-400 rounded">#Careers</span>
                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-[10px] text-slate-500 dark:text-slate-400 rounded">#Jobs</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center text-xs font-semibold text-red-600 dark:text-red-500 group-hover:translate-x-1 transition-transform mt-4">
                                ดูตำแหน่งงานว่าง <i class="fas fa-arrow-right ml-1.5"></i>
                            </span>
                        </a>
                    </div>

                </div>
            @endif
        </div>
    </div>

    <!-- ==================== BLOG / NEWS ==================== -->
    <div id="news-grid" class="py-10 sm:py-14 bg-white dark:bg-[#0B0F17] border-t border-slate-200 dark:border-slate-800 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header (PCRU / KUMWELL NEWS Style) -->
            <div class="mb-5 sm:mb-6 reveal opacity-0 translate-y-6 transition-all duration-500">
                <div class="flex items-end justify-between">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-black text-[#231F40] dark:text-white tracking-tight">
                            ข่าวประชาสัมพันธ์
                        </h2>
                    </div>
                    <a href="{{ route('news.newsAll') }}" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-600 hover:text-red-600 dark:text-slate-400 dark:hover:text-red-500 transition-colors group">
                        <span>ดูข่าวทั้งหมด</span>
                        <i class="fa-solid fa-arrow-right text-[11px] group-hover:translate-x-0.5 transition-transform"></i>
                    </a>
                </div>
            </div>

            @php
                $thai_short_months = [
                    1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
                    5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
                    9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
                ];

                $formatThaiDate = function($raw_date) use ($thai_short_months) {
                    if (!$raw_date) return '25 ส.ค. 2569';
                    $d = \Carbon\Carbon::parse($raw_date);
                    return $d->day . ' ' . ($thai_short_months[$d->month] ?? '') . ' ' . ($d->year + 543);
                };

                // Sample fallback items to complete 9 slots if DB has fewer
                $fallbackNews = [
                    (object)[
                        'news_id' => 1,
                        'title' => '“สภากาแฟ” ประจำปีงบประมาณ พ.ศ. 2569 ครั้งที่ 11',
                        'content' => '“สภากาแฟ” ประจำปีงบประมาณ พ.ศ. 2569 ครั้งที่ 11 ร่วมแลกเปลี่ยนเรียนรู้และสร้างความร่วมมือ...',
                        'published_date' => '2026-08-27',
                        'views' => 90,
                        'image_url' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=1200&q=80',
                    ],
                    (object)[
                        'news_id' => 2,
                        'title' => 'รับสมัครนักศึกษาทุนพิการ ประจำภาคการศึกษาที่ 1/2569',
                        'content' => 'ประกาศรับสมัครทุนการศึกษาสำหรับนักศึกษาผู้มีความบกพร่องทางร่างกาย...',
                        'published_date' => '2026-08-25',
                        'views' => 59,
                        'image_url' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=600&q=80',
                    ],
                    (object)[
                        'news_id' => 3,
                        'title' => 'Kumwell Market Place เปิดพื้นที่ผลิตภัณฑ์นวัตกรรม สินค้าแปรรูป และผลิตภัณฑ์ชุมชน',
                        'content' => 'เปิดพื้นที่ส่งเสริมผู้ประกอบการและบุคลากรนำสินค้าและนวัตกรรมมาจัดแสดง...',
                        'published_date' => '2026-08-25',
                        'views' => 68,
                        'image_url' => 'https://images.unsplash.com/photo-1577495508048-b635879837f1?auto=format&fit=crop&w=600&q=80',
                    ],
                    (object)[
                        'news_id' => 4,
                        'title' => 'ร่วมแลกเปลี่ยนเรียนรู้กับคณะผู้บริหารและพันธมิตรเครือข่ายความปลอดภัย',
                        'content' => 'ต้อนรับคณะผู้บริหารและตัวแทนหน่วยงานเครือข่ายเข้าศึกษาดูงานและแลกเปลี่ยน...',
                        'published_date' => '2026-08-25',
                        'views' => 45,
                        'image_url' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=600&q=80',
                    ],
                    (object)[
                        'news_id' => 5,
                        'title' => 'ขอเชิญชวนร่วมกิจกรรม "CREATOR SPARK 2026" จุดประกายพลังสร้างสรรค์ สู่โอกาสแห่งอนาคต',
                        'content' => 'เปิดเวทีสร้างสรรค์ผลงานด้านนวัตกรรมและเทคโนโลยีป้องกันฟ้าผ่าและความปลอดภัย...',
                        'published_date' => '2026-08-14',
                        'views' => 130,
                        'image_url' => 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?auto=format&fit=crop&w=600&q=80',
                    ],
                    (object)[
                        'news_id' => 6,
                        'title' => 'Kumwell CHANNEL: พิธีรับเครื่องหมายตราสัญลักษณ์และรางวัลความปลอดภัยดีเด่น หล่อหลอมความภาคภูมิใจ',
                        'content' => 'พิธีมอบรางวัลเกียรติยศและตราสัญลักษณ์เชิดชูเกียรติแก่หน่วยงานยอดเยี่ยม...',
                        'published_date' => '2026-08-14',
                        'views' => 225,
                        'image_url' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=600&q=80',
                    ],
                    (object)[
                        'news_id' => 7,
                        'title' => 'เปิดระบบรับสมัครนักศึกษาและฝึกงานวันแรก ประจำปี 2570',
                        'content' => 'เปิดรับสมัครบุคคลเข้าฝึกประสบการณ์วิชาชีพและร่วมงานกับทีมวิศวกรรม Kumwell...',
                        'published_date' => '2026-08-10',
                        'views' => 414,
                        'image_url' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=600&q=80',
                    ],
                    (object)[
                        'news_id' => 8,
                        'title' => 'เลื่อนและขยายเวลาการรับสมัครสอบวัดระดับมาตรฐานความปลอดภัย ประจำปี 2569',
                        'content' => 'ประกาศขยายเวลาเพื่ออำนวยความสะดวกแก่ผู้เข้าร่วมทดสอบมาตรฐาน...',
                        'published_date' => '2026-08-10',
                        'views' => 135,
                        'image_url' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=600&q=80',
                    ],
                    (object)[
                        'news_id' => 9,
                        'title' => 'Kumwell CHANNEL: สืบศิลป์ผ้าไทย เทิดไท้พระพันปีหลวง สานสายใยรักวันแม่แห่งชาติ ประจำปี ๒๕๖๙',
                        'content' => 'ร่วมจัดกิจกรรมเฉลิมพระเกียรติและอนุรักษ์วัฒนธรรมไทย...',
                        'published_date' => '2026-08-05',
                        'views' => 108,
                        'image_url' => 'https://images.unsplash.com/photo-1529070538774-1843cb3265df?auto=format&fit=crop&w=600&q=80',
                    ],
                ];

                // Build standardized collection of 9 items
                $displayItems = collect();
                if (isset($newsItems)) {
                    foreach ($newsItems as $item) {
                        $img = !empty($item->image_path) ? asset(is_array($item->image_path) ? $item->image_path[0] : $item->image_path) : null;
                        $displayItems->push((object)[
                            'news_id' => $item->news_id,
                            'title' => $item->title,
                            'content' => strip_tags($item->content),
                            'published_date' => $item->published_date ?? $item->created_at,
                            'views' => $item->views ?? 0,
                            'image_url' => $img ?: 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=800&q=80',
                            'is_real' => true,
                        ]);
                    }
                }

                // Append fallbacks until we have 9 items
                $fbIndex = 0;
                while ($displayItems->count() < 9 && $fbIndex < count($fallbackNews)) {
                    $fb = $fallbackNews[$fbIndex];
                    $fb->is_real = false;
                    $displayItems->push($fb);
                    $fbIndex++;
                }

                $mainNews = $displayItems->first();
                $sideNews = $displayItems->slice(1, 5);
                $bottomNews = $displayItems->slice(6, 3);
            @endphp

            <div class="space-y-4 sm:space-y-5 reveal opacity-0 translate-y-6 transition-all duration-500 delay-100">
                
                <!-- TOP ROW: Main Large Card (Left 8 cols) + 5 Compact Stacked Cards (Right 4 cols) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-3.5 sm:gap-4 items-stretch">
                    
                    <!-- Left: 1 Large Card (8 Cols) -->
                    @if ($mainNews)
                        <div class="lg:col-span-8 flex flex-col">
                            <a href="{{ $mainNews->is_real ? route('news.detail', $mainNews->news_id) : route('news.newsAll') }}" 
                               class="group bg-white dark:bg-[#151B26] rounded-sm border border-slate-200 dark:border-slate-800 shadow-xs hover:border-slate-300 dark:hover:border-slate-700 transition-all flex flex-col h-full overflow-hidden">
                                
                                <!-- Main Image (16:9) -->
                                <div class="relative aspect-[16/9.2] w-full overflow-hidden bg-slate-900">
                                    <img src="{{ $mainNews->image_url }}" 
                                         alt="{{ $mainNews->title }}" 
                                         loading="lazy" 
                                         class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-500">
                                </div>
                                
                                <!-- Meta & Content -->
                                <div class="p-4 sm:p-5 flex-1 flex flex-col justify-start">
                                    <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 mb-2">
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fa-regular fa-calendar text-slate-400 text-[11px]"></i>
                                            {{ $formatThaiDate($mainNews->published_date) }}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fa-regular fa-eye text-slate-400 text-[11px]"></i>
                                            {{ number_format($mainNews->views) }} ครั้ง
                                        </span>
                                    </div>
                                    <h3 class="text-base sm:text-lg md:text-xl font-bold text-slate-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors leading-snug line-clamp-2 mb-1.5">
                                        {{ $mainNews->title }}
                                    </h3>
                                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 line-clamp-1 leading-relaxed">
                                        {{ $mainNews->content }}
                                    </p>
                                </div>
                            </a>
                        </div>
                    @endif

                    <!-- Right: 5 Stacked Cards (4 Cols) -->
                    <div class="lg:col-span-4 flex flex-col justify-between gap-2 sm:gap-2.5">
                        @foreach ($sideNews as $sItem)
                            <a href="{{ $sItem->is_real ? route('news.detail', $sItem->news_id) : route('news.newsAll') }}" 
                               class="group bg-white dark:bg-[#151B26] p-2 sm:p-2.5 rounded-sm border border-slate-200 dark:border-slate-800 shadow-xs hover:border-slate-300 dark:hover:border-slate-700 transition-all flex items-center gap-3 h-full">
                                
                                <!-- Thumbnail (Compact Rectangle) -->
                                <div class="w-24 h-16 sm:w-28 sm:h-16 overflow-hidden shrink-0 bg-slate-900 relative rounded-xs">
                                    <img src="{{ $sItem->image_url }}" 
                                         alt="{{ $sItem->title }}" 
                                         loading="lazy" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </div>
                                
                                <!-- Details -->
                                <div class="flex-1 min-w-0 pr-1">
                                    <div class="flex items-center gap-2.5 text-[10.5px] text-slate-400 mb-1">
                                        <span class="inline-flex items-center gap-1">
                                            <i class="fa-regular fa-calendar text-[9.5px]"></i>
                                            {{ $formatThaiDate($sItem->published_date) }}
                                        </span>
                                        <span class="inline-flex items-center gap-1">
                                            <i class="fa-regular fa-eye text-[9.5px]"></i>
                                            {{ number_format($sItem->views) }}
                                        </span>
                                    </div>
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors line-clamp-2 leading-snug">
                                        {{ $sItem->title }}
                                    </h4>
                                </div>
                            </a>
                        @endforeach
                    </div>

                </div>

                <!-- BOTTOM ROW: 3 Equal Vertical Cards (3 Columns) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 sm:gap-4">
                    @foreach ($bottomNews as $bItem)
                        <a href="{{ $bItem->is_real ? route('news.detail', $bItem->news_id) : route('news.newsAll') }}" 
                           class="group bg-white dark:bg-[#151B26] rounded-sm border border-slate-200 dark:border-slate-800 shadow-xs hover:border-slate-300 dark:hover:border-slate-700 transition-all flex flex-col h-full overflow-hidden">
                            
                            <!-- Thumbnail (16:9) -->
                            <div class="relative aspect-[16/8.5] w-full overflow-hidden bg-slate-900">
                                <img src="{{ $bItem->image_url }}" 
                                     alt="{{ $bItem->title }}" 
                                     loading="lazy" 
                                     class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-500">
                            </div>
                            
                            <!-- Content -->
                            <div class="p-3.5 sm:p-4 flex-1 flex flex-col justify-start">
                                <div class="flex items-center gap-2.5 text-[10.5px] text-slate-400 mb-1.5">
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fa-regular fa-calendar text-[9.5px]"></i>
                                        {{ $formatThaiDate($bItem->published_date) }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fa-regular fa-eye text-[9.5px]"></i>
                                        {{ number_format($bItem->views) }}
                                    </span>
                                </div>
                                <h4 class="text-xs sm:text-[13px] font-bold text-slate-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors line-clamp-2 leading-snug">
                                    {{ $bItem->title }}
                                </h4>
                            </div>
                        </a>
                    @endforeach
                </div>

            </div>

        </div>
    </div>


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

    @include('layouts.footer');
@endsection
