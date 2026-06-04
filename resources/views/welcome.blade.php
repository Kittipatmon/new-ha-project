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
                    dots[cur].classList.remove('bg-white');
                    dots[cur].classList.add('bg-white/50');
                    
                    cur = n % slides.length;
                    
                    slides[cur].classList.remove('opacity-0', 'z-0');
                    slides[cur].classList.add('opacity-100', 'z-10');
                    dots[cur].classList.remove('bg-white/50');
                    dots[cur].classList.add('bg-white');
                }
                dots.forEach((d, i) => d.addEventListener('click', () => {
                    clearInterval(timer);
                    goSlide(i);
                    timer = setInterval(() => goSlide(cur + 1), 5500);
                }));
                timer = setInterval(() => goSlide(cur + 1), 5500);
            }
        });
    </script>

    <style>
        /* Keep minimal base settings if needed, otherwise rely on Tailwind */
        html { scroll-behavior: smooth; }
        body { font-family: 'Prompt', sans-serif; }
    </style>

    <!-- ==================== HERO ==================== -->
    <div class="max-w-7xl mx-auto px-6 pt-24 pb-4">
        <div class="relative w-full h-[340px] md:h-[440px] rounded-2xl overflow-hidden shadow-lg bg-slate-900">
            <!-- Slides -->
            <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-100 z-10">
                <img src="{{ asset('images/welcome/hero_industrial.png') }}" alt="Kumwell Plant" class="w-full h-full object-cover">
            </div>
            <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0 z-0">
                <img src="{{ asset('images/welcome/ro1.jpg') }}" alt="Plant Night" class="w-full h-full object-cover">
            </div>
            <div class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out opacity-0 z-0">
                <img src="https://image.makewebeasy.net/makeweb/m_1920x0/0etpaXZ92/Corporate/Banner_ab_1_.webp?v=202405291424" alt="Team" class="w-full h-full object-cover">
            </div>

            <!-- Flat uniform overlay instead of gradient -->
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
                    <a href="#services-grid" class="inline-flex items-center gap-2 bg-white text-kumwell-red font-semibold text-xs px-6 py-2.5 rounded-full hover:bg-slate-50 transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-slate-900">
                        View More <i class="fas fa-arrow-down ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Slider Dots -->
            <div class="absolute bottom-6 left-0 right-0 z-30 flex justify-center gap-2.5">
                <button class="hero-dot w-2.5 h-2.5 rounded-full bg-white transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-slate-900" aria-label="Go to Slide 1"></button>
                <button class="hero-dot w-2.5 h-2.5 rounded-full bg-white/50 hover:bg-white/80 transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-slate-900" aria-label="Go to Slide 2"></button>
                <button class="hero-dot w-2.5 h-2.5 rounded-full bg-white/50 hover:bg-white/80 transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-slate-900" aria-label="Go to Slide 3"></button>
            </div>
        </div>
    </div>

    <!-- ==================== SERVICES STRIP ==================== -->
    <div class="relative z-40 max-w-6xl mx-auto px-6 pt-16 pb-8 mb-12">
        <!-- Title & Subtitle Section -->
        <div class="text-center mb-10 reveal opacity-0 translate-y-8 transition-all duration-700">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold tracking-wider bg-red-50 text-red-600 dark:bg-red-950/30 dark:text-red-400 border border-red-200/30 dark:border-red-800/30 mb-3">
                KUMWELL IDENTITY
            </span>
            <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 dark:text-white mb-3">
                คุณค่าร่วมองค์กร <span class="text-kumwell-red">(CCSV)</span>
            </h2>
            <p class="text-slate-500 dark:text-slate-400 max-w-2xl mx-auto text-xs md:text-sm leading-relaxed">
                วัฒนธรรมร่วมที่พวกเรายึดถือปฏิบัติ เพื่อส่งเสริมความร่วมมือ สร้างสรรค์นวัตกรรม แบ่งปันคุณค่า และยกระดับขีดความสามารถการทำงานร่วมกันสู่สากล
            </p>
        </div>

        <!-- Red Container with White Cards -->
        <div class="bg-kumwell-red p-4 md:p-5 shadow-xl reveal opacity-0 translate-y-8 transition-all duration-700 delay-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
                <!-- Card 1 -->
                <div class="group bg-white p-8 flex flex-col items-center text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-20 h-20 rounded-full bg-kumwell-red text-white flex items-center justify-center text-3xl mb-6 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-handshake"></i>
                    </div>
                    <h3 class="text-kumwell-red font-extrabold text-base mb-3 uppercase tracking-wider">C (Corporation)</h3>
                    <p class="text-slate-600 text-xs md:text-sm leading-relaxed">ร่วมมือกับผู้มีส่วนได้เสีย บูรณาการการส่งมอบผลิตภัณฑ์และบริการอย่างมืออาชีพ</p>
                </div>
                <!-- Card 2 -->
                <div class="group bg-white p-8 flex flex-col items-center text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-20 h-20 rounded-full bg-kumwell-red text-white flex items-center justify-center text-3xl mb-6 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-network-wired"></i>
                    </div>
                    <h3 class="text-kumwell-red font-extrabold text-base mb-3 uppercase tracking-wider">C (Creating)</h3>
                    <p class="text-slate-600 text-xs md:text-sm leading-relaxed">สร้างสรรค์งานวิจัยและพัฒนาร่วมกับพันธมิตร เพื่อส่งมอบนวัตกรรมความปลอดภัย</p>
                </div>
                <!-- Card 3 -->
                <div class="group bg-white p-8 flex flex-col items-center text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-20 h-20 rounded-full bg-kumwell-red text-white flex items-center justify-center text-3xl mb-6 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-chalkboard-teacher"></i>
                    </div>
                    <h3 class="text-kumwell-red font-extrabold text-base mb-3 uppercase tracking-wider">S (Shared)</h3>
                    <p class="text-slate-600 text-xs md:text-sm leading-relaxed">แบ่งปันและเพิ่มคุณค่าร่วมกับทุกภาคส่วน มุ่งเน้นการส่งต่อความปลอดภัยสู่สังคม</p>
                </div>
                <!-- Card 4 -->
                <div class="group bg-white p-8 flex flex-col items-center text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="w-20 h-20 rounded-full bg-kumwell-red text-white flex items-center justify-center text-3xl mb-6 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-people-carry-box"></i>
                    </div>
                    <h3 class="text-kumwell-red font-extrabold text-base mb-3 uppercase tracking-wider">V (Value)</h3>
                    <p class="text-slate-600 text-xs md:text-sm leading-relaxed">เพิ่มคุณค่าและพัฒนาศักยภาพทุนมนุษย์ ก้าวสู่องค์กรนวัตกรรมระดับสากลสู่ความยั่งยืน</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== ABOUT US ==================== -->
    <div class="relative bg-white dark:bg-[#0B0F17] overflow-hidden border-b border-slate-200 dark:border-slate-800 lg:min-h-[600px] flex items-center">
        <!-- Background decorative grid / glow -->
        <div class="absolute inset-0 z-0 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:14px_24px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]"></div>
        <div class="absolute left-1/4 top-1/4 w-96 h-96 bg-red-600/10 dark:bg-red-600/5 rounded-full blur-[128px] pointer-events-none z-0"></div>

        <div class="max-w-7xl mx-auto px-6 w-full relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                <!-- Left: Content -->
                <div class="lg:col-span-6 py-20 lg:py-24 reveal opacity-0 -translate-x-8 transition-all duration-700">
                    <!-- Premium Badge -->
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-red-50 text-red-600 dark:bg-red-950/30 dark:text-red-400 border border-red-200/50 dark:border-red-800/30 mb-5">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-600 dark:bg-red-400 animate-pulse"></span>
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
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8 max-w-2xl border-t border-gray-100 dark:border-gray-800 pt-8">
                        <div>
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
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4">
                        <a href="https://www.kumwell.com/" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 bg-gray-900 hover:bg-gray-800 dark:bg-red-600 dark:hover:bg-red-700 text-white font-bold text-sm px-6 py-3 rounded-xl transition-all duration-200 hover:-translate-y-0.5 shadow-md shadow-gray-950/10 focus:outline-none focus:ring-2 focus:ring-red-500">
                            View Website <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Full-bleed Image (Absolute on large screens, block on mobile) -->
        <div class="w-full lg:absolute lg:top-0 lg:right-0 lg:bottom-0 lg:w-1/2 h-[350px] lg:h-auto overflow-hidden group reveal opacity-0 translate-x-8 transition-all duration-700">
            <img src="{{ asset('images/welcome/kmlhq.jpg') }}" alt="About Kumwell" loading="lazy" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
            <!-- Floating trust card overlay -->
            <div class="absolute bottom-6 left-6 right-6 backdrop-blur-md bg-white/95 dark:bg-[#1E2129]/95 border border-white/20 dark:border-white/5 shadow-xl rounded-xl p-4 flex items-center gap-4 transition-transform duration-300 group-hover:-translate-y-1">
                <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-500/10 flex items-center justify-center text-red-600 dark:text-red-500">
                    <i class="fa-solid fa-shield-halved text-lg"></i>
                </div>
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">KUMWELL HQ</span>
                    <span class="block text-sm font-bold text-gray-900 dark:text-white">ผู้นำระบบป้องกันฟ้าผ่าและต่อลงดิน</span>
                </div>
            </div>
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
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold tracking-wider bg-red-50 text-red-600 dark:bg-red-950/30 dark:text-red-400 border border-red-200/30 dark:border-red-800/30 mb-3">
                    OUR PORTALS
                </span>
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
                <!-- Bento Grid for Admin / HR -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Card 1: HR Request -->
                    <div class="lg:col-span-2 h-full reveal opacity-0 translate-y-8 transition-all duration-700">
                        @auth
                            <div class="group bg-white dark:bg-[#151B26]/35 border border-slate-200/60 dark:border-slate-800/80 rounded-xl p-8 transition-all duration-300 hover:border-red-500/30 hover:shadow-md flex flex-col md:flex-row justify-between gap-6 relative overflow-hidden h-full w-full">
                        @else
                            <div class="group bg-white dark:bg-[#151B26]/35 border border-slate-200/60 dark:border-slate-800/80 rounded-xl p-8 transition-all duration-300 hover:border-red-500/30 hover:shadow-md flex flex-col md:flex-row justify-between gap-6 w-full login-open-btn cursor-pointer relative overflow-hidden h-full">
                        @endauth
                                <div class="flex-1 flex flex-col justify-between">
                                    <div>
                                        <div class="w-12 h-12 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
                                            <i class="fa-regular fa-file-lines"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors">ระบบจัดการคำร้อง</h3>
                                        <p class="text-xs text-slate-600 dark:text-gray-300 leading-relaxed mb-6">ดำเนินการยื่นขอเอกสาร การลางาน และการปรับแก้ไขเวลาทำงาน พร้อมติดตามความคืบหน้าอย่างรวดเร็ว</p>
                                    </div>
                                    @auth
                                        <a href="{{ route('request.hr') }}" class="inline-flex items-center text-xs font-semibold text-red-600 dark:text-red-500 group-hover:translate-x-1 transition-transform focus:outline-none">
                                            เปิดใช้งานระบบ <i class="fas fa-arrow-right ml-1.5"></i>
                                        </a>
                                    @else
                                        <span class="inline-flex items-center text-xs font-semibold text-red-600 dark:text-red-500 group-hover:translate-x-1 transition-transform">
                                            เปิดใช้งานระบบ <i class="fas fa-arrow-right ml-1.5"></i>
                                        </span>
                                    @endauth
                                </div>
                                
                                <!-- Quick Actions Section inside the card -->
                                <div class="w-full md:w-56 shrink-0 flex flex-col gap-2.5 border-t md:border-t-0 md:border-l border-slate-100 dark:border-slate-800/80 pt-6 md:pt-0 md:pl-6">
                                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">เมนูด่วน / Quick Links</span>
                                    @auth
                                        <a href="{{ route('request.hr') }}" class="flex items-center gap-2 px-3 py-2 bg-slate-50 hover:bg-red-50/50 dark:bg-slate-900/40 dark:hover:bg-red-950/20 border border-slate-100 dark:border-slate-800/50 rounded-lg text-xs text-slate-700 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 font-medium transition-colors">
                                            <i class="fa-solid fa-umbrella-beach text-[10px] opacity-75"></i> ยื่นใบลาออนไลน์
                                        </a>
                                        <a href="{{ route('request.hr') }}" class="flex items-center gap-2 px-3 py-2 bg-slate-50 hover:bg-red-50/50 dark:bg-slate-900/40 dark:hover:bg-red-950/20 border border-slate-100 dark:border-slate-800/50 rounded-lg text-xs text-slate-700 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 font-medium transition-colors">
                                            <i class="fa-solid fa-file-invoice text-[10px] opacity-75"></i> ขอหนังสือรับรอง
                                        </a>
                                        <a href="{{ route('request.hr') }}" class="flex items-center gap-2 px-3 py-2 bg-slate-50 hover:bg-red-50/50 dark:bg-slate-900/40 dark:hover:bg-red-950/20 border border-slate-100 dark:border-slate-800/50 rounded-lg text-xs text-slate-700 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 font-medium transition-colors">
                                            <i class="fa-solid fa-clock-rotate-left text-[10px] opacity-75"></i> ปรับปรุงเวลาสแกน
                                        </a>
                                    @else
                                        <button type="button" class="login-open-btn flex items-center gap-2 px-3 py-2 bg-slate-50 hover:bg-red-50/50 dark:bg-slate-900/40 dark:hover:bg-red-950/20 border border-slate-100 dark:border-slate-800/50 rounded-lg text-xs text-slate-700 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 font-medium transition-colors text-left w-full">
                                            <i class="fa-solid fa-umbrella-beach text-[10px] opacity-75"></i> ยื่นใบลาออนไลน์
                                        </button>
                                        <button type="button" class="login-open-btn flex items-center gap-2 px-3 py-2 bg-slate-50 hover:bg-red-50/50 dark:bg-slate-900/40 dark:hover:bg-red-950/20 border border-slate-100 dark:border-slate-800/50 rounded-lg text-xs text-slate-700 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 font-medium transition-colors text-left w-full">
                                            <i class="fa-solid fa-file-invoice text-[10px] opacity-75"></i> ขอหนังสือรับรอง
                                        </button>
                                        <button type="button" class="login-open-btn flex items-center gap-2 px-3 py-2 bg-slate-50 hover:bg-red-50/50 dark:bg-slate-900/40 dark:hover:bg-red-950/20 border border-slate-100 dark:border-slate-800/50 rounded-lg text-xs text-slate-700 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 font-medium transition-colors text-left w-full">
                                            <i class="fa-solid fa-clock-rotate-left text-[10px] opacity-75"></i> ปรับปรุงเวลาสแกน
                                        </button>
                                    @endauth
                                </div>
                            </div>
                    </div>

                    <!-- Card 2: Manpower -->
                    <div class="lg:col-span-1 h-full reveal opacity-0 translate-y-8 transition-all duration-700 delay-75">
                        <a href="{{ route('manpower.dashboard') }}" class="group bg-white dark:bg-[#151B26]/35 border border-slate-200/60 dark:border-slate-800/80 rounded-xl p-8 transition-all duration-300 hover:border-red-500/30 hover:shadow-md flex flex-col justify-between h-full min-h-[250px]">
                            <div>
                                <div class="w-12 h-12 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
                                    <i class="fa-solid fa-users-gear"></i>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors">ระบบอัตรากำลังพล</h3>
                                <p class="text-xs text-slate-600 dark:text-gray-300 leading-relaxed">วิเคราะห์แผนกำลังพล ความต้องการของแผนกต่างๆ และการอนุมัติสิทธิ์อัตรากำลัง</p>
                            </div>
                            <span class="inline-flex items-center text-xs font-semibold text-red-600 dark:text-red-500 group-hover:translate-x-1 transition-transform mt-4">
                                ดูข้อมูลวิเคราะห์ <i class="fas fa-arrow-right ml-1.5"></i>
                            </span>
                        </a>
                    </div>

                    <!-- Card 3: Training -->
                    <div class="lg:col-span-1 h-full reveal opacity-0 translate-y-8 transition-all duration-700 delay-100">
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

                    <!-- Card 4: Data Management -->
                    <div class="lg:col-span-2 h-full reveal opacity-0 translate-y-8 transition-all duration-700 delay-150">
                        <a href="{{ route('request.data') }}" class="group bg-white dark:bg-[#151B26]/35 border border-slate-200/60 dark:border-slate-800/80 rounded-xl p-8 transition-all duration-300 hover:border-red-500/30 hover:shadow-md flex flex-col md:flex-row justify-between gap-6 relative overflow-hidden h-full">
                            <div class="flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="w-12 h-12 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
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
                <!-- Normal User / Staff Layout (Asymmetric 3-Column Bento Grid) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    
                    <!-- Card 1: HR Request -->
                    <div class="md:col-span-2 h-full reveal opacity-0 translate-y-8 transition-all duration-700">
                        @auth
                            <div class="group bg-white dark:bg-[#151B26]/35 border border-slate-200/60 dark:border-slate-800/80 rounded-xl p-8 transition-all duration-300 hover:border-red-500/30 hover:shadow-md flex flex-col sm:flex-row justify-between gap-6 relative overflow-hidden h-full w-full">
                        @else
                            <div class="group bg-white dark:bg-[#151B26]/35 border border-slate-200/60 dark:border-slate-800/80 rounded-xl p-8 transition-all duration-300 hover:border-red-500/30 hover:shadow-md flex flex-col sm:flex-row justify-between gap-6 w-full login-open-btn cursor-pointer relative overflow-hidden h-full">
                        @endauth
                                <div class="flex-1 flex flex-col justify-between">
                                    <div>
                                        <div class="w-12 h-12 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 flex items-center justify-center text-xl mb-4 group-hover:scale-105 transition-transform">
                                            <i class="fa-regular fa-file-lines"></i>
                                        </div>
                                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2 group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors">ระบบจัดการคำร้อง</h3>
                                        <p class="text-xs text-slate-600 dark:text-gray-300 leading-relaxed mb-6">ดำเนินการยื่นขอเอกสาร การลางาน และการปรับแก้ไขเวลาทำงาน พร้อมติดตามความคืบหน้าการอนุมัติได้ง่ายๆ</p>
                                    </div>
                                    @auth
                                        <a href="{{ route('request.hr') }}" class="inline-flex items-center text-xs font-semibold text-red-600 dark:text-red-500 group-hover:translate-x-1 transition-transform focus:outline-none">
                                            เปิดใช้งานระบบ <i class="fas fa-arrow-right ml-1.5"></i>
                                        </a>
                                    @else
                                        <span class="inline-flex items-center text-xs font-semibold text-red-600 dark:text-red-500 group-hover:translate-x-1 transition-transform">
                                            เปิดใช้งานระบบ <i class="fas fa-arrow-right ml-1.5"></i>
                                        </span>
                                    @endauth
                                </div>
                                
                                <!-- Quick Actions Section inside the card -->
                                <div class="w-full sm:w-48 shrink-0 flex flex-col gap-2.5 border-t sm:border-t-0 sm:border-l border-slate-100 dark:border-slate-800/80 pt-6 sm:pt-0 sm:pl-6">
                                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">เมนูด่วน / Quick Links</span>
                                    @auth
                                        <a href="{{ route('request.hr') }}" class="flex items-center gap-2 px-3 py-2 bg-slate-50 hover:bg-red-50/50 dark:bg-slate-900/40 dark:hover:bg-red-950/20 border border-slate-100 dark:border-slate-800/50 rounded-lg text-xs text-slate-700 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 font-medium transition-colors">
                                            <i class="fa-solid fa-umbrella-beach text-[10px] opacity-75"></i> ยื่นใบลาออนไลน์
                                        </a>
                                        <a href="{{ route('request.hr') }}" class="flex items-center gap-2 px-3 py-2 bg-slate-50 hover:bg-red-50/50 dark:bg-slate-900/40 dark:hover:bg-red-950/20 border border-slate-100 dark:border-slate-800/50 rounded-lg text-xs text-slate-700 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 font-medium transition-colors">
                                            <i class="fa-solid fa-file-invoice text-[10px] opacity-75"></i> ขอหนังสือรับรอง
                                        </a>
                                        <a href="{{ route('request.hr') }}" class="flex items-center gap-2 px-3 py-2 bg-slate-50 hover:bg-red-50/50 dark:bg-slate-900/40 dark:hover:bg-red-950/20 border border-slate-100 dark:border-slate-800/50 rounded-lg text-xs text-slate-700 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 font-medium transition-colors">
                                            <i class="fa-solid fa-clock-rotate-left text-[10px] opacity-75"></i> ปรับปรุงเวลาสแกน
                                        </a>
                                    @else
                                        <button type="button" class="login-open-btn flex items-center gap-2 px-3 py-2 bg-slate-50 hover:bg-red-50/50 dark:bg-slate-900/40 dark:hover:bg-red-950/20 border border-slate-100 dark:border-slate-800/50 rounded-lg text-xs text-slate-700 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 font-medium transition-colors text-left w-full">
                                            <i class="fa-solid fa-umbrella-beach text-[10px] opacity-75"></i> ยื่นใบลาออนไลน์
                                        </button>
                                        <button type="button" class="login-open-btn flex items-center gap-2 px-3 py-2 bg-slate-50 hover:bg-red-50/50 dark:bg-slate-900/40 dark:hover:bg-red-950/20 border border-slate-100 dark:border-slate-800/50 rounded-lg text-xs text-slate-700 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 font-medium transition-colors text-left w-full">
                                            <i class="fa-solid fa-file-invoice text-[10px] opacity-75"></i> ขอหนังสือรับรอง
                                        </button>
                                        <button type="button" class="login-open-btn flex items-center gap-2 px-3 py-2 bg-slate-50 hover:bg-red-50/50 dark:bg-slate-900/40 dark:hover:bg-red-950/20 border border-slate-100 dark:border-slate-800/50 rounded-lg text-xs text-slate-700 dark:text-slate-300 hover:text-red-600 dark:hover:text-red-400 font-medium transition-colors text-left w-full">
                                            <i class="fa-solid fa-clock-rotate-left text-[10px] opacity-75"></i> ปรับปรุงเวลาสแกน
                                        </button>
                                    @endauth
                                </div>
                            </div>
                    </div>

                    <!-- Card 2: Training -->
                    <div class="md:col-span-1 h-full reveal opacity-0 translate-y-8 transition-all duration-700 delay-100">
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

                </div>
            @endif
        </div>
    </div>

    <!-- ==================== BLOG / NEWS ==================== -->
    <div id="news-grid" class="py-24 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800/80">
        <div class="max-w-5xl mx-auto px-6">
            <!-- Header matching the screenshot -->
            <div class="flex items-center mb-12 reveal opacity-0 translate-y-8 transition-all duration-700">
                <div class="bg-[#F5A623] text-white px-5 py-3 font-bold text-sm md:text-base flex items-center gap-2.5 shadow-sm shrink-0">
                    <i class="fa-solid fa-bullhorn text-sm"></i>
                    ข่าวประชาสัมพันธ์
                </div>
                <!-- Decorative repeating dot grid pattern -->
                <div class="flex-1 h-11 bg-[radial-gradient(#d1d5db_1px,transparent_1px)] dark:bg-[radial-gradient(#475569_1px,transparent_1px)] [background-size:5px_5px] opacity-90 ml-3 pointer-events-none"></div>
            </div>

            @if (isset($newsItems) && $newsItems->count() > 0)
                <div class="flex flex-col reveal opacity-0 translate-y-8 transition-all duration-700 delay-100">
                    @foreach ($newsItems->take(5) as $item)
                        <div class="pt-8 pb-3 first:pt-0 border-b border-dotted border-slate-350 dark:border-slate-700/80">
                            <div class="flex flex-col md:flex-row gap-6 items-start">
                                <!-- Left column: Image & Date/Views -->
                                <div class="w-full md:w-72 shrink-0">
                                    <a href="{{ route('news.detail', $item->news_id) }}" class="group/img block relative aspect-[16/10] w-full rounded-none overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
                                        <img src="{{ $item->image_path ? asset(is_array($item->image_path) ? $item->image_path[0] : $item->image_path) : 'https://placehold.co/600x400/e2e8f0/FFF?text=News' }}"
                                             alt="{{ $item->title }}"
                                             loading="lazy"
                                             class="w-full h-full object-cover group-hover/img:scale-105 transition-transform duration-500 ease-out">
                                        <div class="absolute inset-0 bg-black/5 opacity-0 group-hover/img:opacity-100 transition-opacity"></div>
                                    </a>
                                    <!-- Thick grey bar under the image -->
                                    <div class="h-[5px] bg-slate-500 dark:bg-slate-650 w-full"></div>
                                    
                                    @php
                                        $thai_months = [
                                            1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
                                            5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
                                            9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
                                        ];
                                        $raw_date = $item->published_date ?? $item->created_at;
                                        $date = null;
                                        if ($raw_date) {
                                            $date = \Carbon\Carbon::parse($raw_date);
                                        }
                                        // Stable simulated views based on news_id
                                        $views = ($item->news_id * 37) % 450 + 88;
                                    @endphp
                                    
                                    <div class="text-xs md:text-sm text-slate-500 dark:text-slate-400 font-normal mt-6">
                                        @if($date)
                                            {{ $date->day }} {{ $thai_months[$date->month] }} {{ $date->year + 543 }}
                                        @else
                                            N/A
                                        @endif
                                        <span class="ml-2">ผู้เข้าชม {{ $views }}</span>
                                    </div>
                                </div>
                                
                                <!-- Right column: Text Content -->
                                <div class="flex-1 flex flex-col min-w-0">
                                    <a href="{{ route('news.detail', $item->news_id) }}" class="group/title block">
                                        <h3 class="text-xs md:text-sm font-normal text-slate-900 dark:text-white mb-2 leading-snug group-hover/title:text-red-600 dark:group-hover/title:text-red-500 transition-colors line-clamp-2">
                                            {{ $item->title }}
                                        </h3>
                                    </a>
                                    <p class="text-xs md:text-sm text-slate-600 dark:text-gray-300 leading-relaxed line-clamp-3 md:line-clamp-4">
                                        {{ strip_tags($item->content) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex justify-end reveal opacity-0 translate-y-8 transition-all duration-700 delay-150">
                    <a href="{{ route('news.newsAll') }}" class="inline-flex items-center gap-2 text-xs md:text-sm font-normal text-slate-500 hover:text-red-600 dark:text-slate-400 dark:hover:text-red-500 transition-colors group">
                        ดูทั้งหมด 
                        <span class="w-6 h-6 rounded-full border border-slate-350 dark:border-slate-700 flex items-center justify-center text-[10px] group-hover:border-red-600 group-hover:bg-red-600 group-hover:text-white transition-all text-slate-500 dark:text-slate-400 group-hover:text-white">
                            <i class="fa-solid fa-arrow-right text-[8px]"></i>
                        </span>
                    </a>
                </div>
            @else
                <div class="border-2 border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 rounded-xl p-16 text-center text-slate-500 reveal opacity-0 translate-y-8 transition-all duration-700">
                    <i class="fa-regular fa-newspaper text-4xl mb-4 opacity-50"></i>
                    <p>ยังไม่มีข่าวประชาสัมพันธ์ในขณะนี้</p>
                </div>
            @endif
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
        });
    </script>

    @include('layouts.footer');
@endsection
