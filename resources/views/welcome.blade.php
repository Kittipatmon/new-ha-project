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
    <div class="relative min-h-screen flex items-center overflow-hidden bg-slate-900">
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
        <div class="relative z-30 max-w-7xl mx-auto px-6 w-full text-center py-32">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 uppercase tracking-tight opacity-0 translate-y-8 transition-all duration-700 delay-100 reveal">
                Human Assetment
            </h1>
            <p class="text-base md:text-lg text-slate-100 max-w-2xl mx-auto mb-10 leading-relaxed opacity-0 translate-y-8 transition-all duration-700 delay-200 reveal">
                ยกระดับการบริหารทรัพยากรบุคคล มุ่งเน้นการประเมินที่มีประสิทธิภาพ
                และวัฒนธรรมการเรียนรู้ตลอดชีวิต (Life Long Learning)
                เพื่อขับเคลื่อนองค์กรสู่อนาคต
            </p>
            <div class="opacity-0 translate-y-8 transition-all duration-700 delay-300 reveal">
                <a href="#services-grid" class="inline-flex items-center gap-2 bg-white text-kumwell-red font-semibold text-sm px-8 py-3 rounded-full hover:bg-slate-50 transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-slate-900">
                    View More <i class="fas fa-arrow-down ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Slider Dots -->
        <div class="absolute bottom-10 left-0 right-0 z-30 flex justify-center gap-3">
            <button class="hero-dot w-3 h-3 rounded-full bg-white transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-slate-900" aria-label="Go to Slide 1"></button>
            <button class="hero-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white/80 transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-slate-900" aria-label="Go to Slide 2"></button>
            <button class="hero-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white/80 transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-slate-900" aria-label="Go to Slide 3"></button>
        </div>
    </div>

    <!-- ==================== SERVICES STRIP ==================== -->
    <div class="relative z-40 max-w-7xl mx-auto px-6 -mt-16 sm:-mt-20 mb-20">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1 -->
            <div class="bg-kumwell-red rounded-xl p-8 text-center shadow-sm border border-red-800 reveal opacity-0 translate-y-8 transition-all duration-700 hover:-translate-y-1 hover:shadow-lg">
                <div class="w-14 h-14 mx-auto bg-white/10 border border-white/20 rounded-xl flex items-center justify-center text-white text-2xl mb-5">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <h3 class="text-white text-lg font-bold mb-3">C (Corporation)</h3>
                <p class="text-white/90 text-sm leading-relaxed">ร่วมมือกับผู้มีส่วนได้เสีย ในการบูรณาการเพื่อส่งมอบผลิตภัณฑ์และบริการ พร้อมทั้งสร้างความพึงพอใจให้กับลูกค้า</p>
            </div>
            <!-- Card 2 -->
            <div class="bg-kumwell-red rounded-xl p-8 text-center shadow-sm border border-red-800 reveal opacity-0 translate-y-8 transition-all duration-700 delay-100 hover:-translate-y-1 hover:shadow-lg">
                <div class="w-14 h-14 mx-auto bg-white/10 border border-white/20 rounded-xl flex items-center justify-center text-white text-2xl mb-5">
                    <i class="fa-solid fa-network-wired"></i>
                </div>
                <h3 class="text-white text-lg font-bold mb-3">C (Creating)</h3>
                <p class="text-white/90 text-sm leading-relaxed">สร้างสรรค์กับพันธมิตรและเครือข่ายในการวิจัยและพัฒนาเพื่อส่งมอบ นวัตกรรมด้านความปลอดภัยและป้องกันฟ้าผ่า</p>
            </div>
            <!-- Card 3 -->
            <div class="bg-kumwell-red rounded-xl p-8 text-center shadow-sm border border-red-800 reveal opacity-0 translate-y-8 transition-all duration-700 delay-200 hover:-translate-y-1 hover:shadow-lg">
                <div class="w-14 h-14 mx-auto bg-white/10 border border-white/20 rounded-xl flex items-center justify-center text-white text-2xl mb-5">
                    <i class="fa-solid fa-chalkboard-teacher"></i>
                </div>
                <h3 class="text-white text-lg font-bold mb-3">S (Shared)</h3>
                <p class="text-white/90 text-sm leading-relaxed">ร่วมกันกับทุกภาคส่วนในการสร้างสรรค์และเพิ่มคุณค่า เพื่อส่งมอบความ ปลอดภัยสู่สังคม</p>
            </div>
            <!-- Card 4 -->
            <div class="bg-kumwell-red rounded-xl p-8 text-center shadow-sm border border-red-800 reveal opacity-0 translate-y-8 transition-all duration-700 delay-300 hover:-translate-y-1 hover:shadow-lg">
                <div class="w-14 h-14 mx-auto bg-white/10 border border-white/20 rounded-xl flex items-center justify-center text-white text-2xl mb-5">
                    <i class="fa-solid fa-people-carry-box"></i>
                </div>
                <h3 class="text-white text-lg font-bold mb-3">V (Value)</h3>
                <p class="text-white/90 text-sm leading-relaxed">เสริมสร้างคุณค่า ยกระดับขีดความสามารถทรัพยากรมนุษย์ให้เป็นองค์กร นวัตกรรมระดับสากลสู่ความยั่งยืน</p>
            </div>
        </div>
    </div>

    <!-- ==================== ABOUT US ==================== -->
    <div class="py-20 bg-white dark:bg-slate-900">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            <div class="reveal opacity-0 -translate-x-8 transition-all duration-700">
                <div class="flex items-center gap-2 text-xs font-bold tracking-widest uppercase text-kumwell-red mb-4">
                    <span class="w-5 h-0.5 bg-kumwell-red rounded-full"></span> About Us
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white leading-tight mb-6">
                    เกี่ยวกับ<br>Kumwell Group
                </h2>
                <p class="text-slate-600 dark:text-slate-400 text-base leading-relaxed mb-8">
                    บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน) เป็นผู้ผลิตและจัดจำหน่ายผลิตภัณฑ์ในระบบต่อลงดิน เช่น แท่งหลักดิน อุปกรณ์เชื่อมตัวนำด้วยความร้อน สารปรับปรุงค่าความต้านทานดิน บ่อทดสอบหลักดิน เป็นต้น ระบบป้องกันฟ้าผ่า เช่น แท่งล่อฟ้า อุปกรณ์จับยึดตัวนำ เป็นต้น ระบบป้องกันเสิร์จ ระบบตรวจจับฟ้าผ่า และระบบแจ้งเตือนภัยฟ้าผ่า ตามมาตรฐานสากลอย่างครบวงจร ภายใต้ตราสินค้าแบรนด์ Kumwell ที่มีการส่งออกและตัวแทนจำหน่ายครอบคลุม 40 ประเทศทั่วโลก
                </p>
                <a href="https://www.kumwell.com/" target="_blank" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 dark:bg-kumwell-red dark:hover:bg-red-800 text-white font-semibold text-sm px-6 py-3 rounded transition-colors focus:outline-none focus:ring-2 focus:ring-slate-900 dark:focus:ring-kumwell-red focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                    View More <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
            <div class="reveal opacity-0 translate-x-8 transition-all duration-700">
                <div class="relative rounded-xl overflow-hidden shadow-sm h-80 md:h-[400px] w-full bg-slate-100 dark:bg-slate-800">
                    <img src="{{ asset('images/welcome/kmlhq.jpg') }}" alt="About Kumwell" loading="lazy" class="w-full h-full object-cover">
                    <div class="absolute bottom-6 left-6 bg-slate-900/90 backdrop-blur-sm rounded-lg p-5 text-white border border-white/10">
                        <strong class="block text-3xl font-bold leading-none mb-1">25</strong>
                        <span class="text-xs text-white/70 uppercase tracking-wide">ปีแห่งประสบการณ์</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== HR SERVICES ==================== -->
    <div id="services-grid" class="py-24 bg-slate-50 dark:bg-slate-900 border-y border-slate-200 dark:border-slate-800 relative overflow-hidden scroll-mt-20">
        <!-- Optional Watermark Background (Muted) -->
        <div class="absolute inset-0 z-0 opacity-5 dark:opacity-10 bg-cover bg-bottom bg-fixed bg-no-repeat pointer-events-none"
             style="background-image: url('{{ asset('images/welcome/plant_night.png') }}');">
        </div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="text-center mb-16 reveal opacity-0 translate-y-8 transition-all duration-700">
                <div class="flex items-center justify-center gap-2 text-xs font-bold tracking-widest uppercase text-kumwell-red mb-3">
                    <span class="w-5 h-0.5 bg-kumwell-red rounded-full"></span> Our Services
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">
                    ระบบบริการ <span class="text-kumwell-red">HR</span>
                </h2>
                <p class="text-slate-600 dark:text-slate-400 max-w-lg mx-auto">
                    เลือกใช้งานระบบต่างๆ ที่ออกแบบมาเพื่อการบริหารทรัพยากรบุคคลอย่างมีประสิทธิภาพ
                </p>
            </div>

            @php
                $isHrOrAdmin = Auth::check() && Auth::user()->isHrOrAdmin();
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 {{ $isHrOrAdmin ? 'lg:grid-cols-4 gap-6' : 'max-w-3xl mx-auto gap-8' }}">
                
                <!-- HR Request -->
                @auth
                    <a href="{{ route('request.hr') }}" class="group bg-white dark:bg-slate-800 rounded-xl p-8 text-center shadow-sm border border-slate-200 dark:border-slate-700 hover:border-kumwell-red dark:hover:border-kumwell-red hover:shadow-md hover:-translate-y-1 transition-all reveal opacity-0 translate-y-8 duration-700 focus:outline-none focus:ring-2 focus:ring-kumwell-red focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                @else
                    <button type="button" class="group bg-white dark:bg-slate-800 rounded-xl p-8 text-center shadow-sm border border-slate-200 dark:border-slate-700 hover:border-kumwell-red dark:hover:border-kumwell-red hover:shadow-md hover:-translate-y-1 transition-all w-full block login-open-btn reveal opacity-0 translate-y-8 duration-700 focus:outline-none focus:ring-2 focus:ring-kumwell-red focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                @endauth
                        <div class="text-4xl text-slate-300 dark:text-slate-600 group-hover:text-kumwell-red group-hover:scale-110 transition-all mb-6">
                            <i class="fa-regular fa-file-lines"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white uppercase mb-2">ระบบจัดการคำร้อง</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">คำร้องทุกประเภท แก้ไขเวลา ใบรับรอง ฯลฯ</p>
                @auth
                    </a>
                @else
                    </button>
                @endauth

                <!-- Training -->
                @auth
                    <a href="{{ route('training.index') }}" class="group bg-white dark:bg-slate-800 rounded-xl p-8 text-center shadow-sm border border-slate-200 dark:border-slate-700 hover:border-kumwell-red dark:hover:border-kumwell-red hover:shadow-md hover:-translate-y-1 transition-all reveal opacity-0 translate-y-8 duration-700 delay-100 focus:outline-none focus:ring-2 focus:ring-kumwell-red focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                @else
                    <button type="button" class="group bg-white dark:bg-slate-800 rounded-xl p-8 text-center shadow-sm border border-slate-200 dark:border-slate-700 hover:border-kumwell-red dark:hover:border-kumwell-red hover:shadow-md hover:-translate-y-1 transition-all w-full block login-open-btn reveal opacity-0 translate-y-8 duration-700 delay-100 focus:outline-none focus:ring-2 focus:ring-kumwell-red focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                @endauth
                        <div class="text-4xl text-slate-300 dark:text-slate-600 group-hover:text-kumwell-red group-hover:scale-110 transition-all mb-6">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white uppercase mb-2">ระบบฝึกอบรม</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">ระบบฝึกอบรมและพัฒนาทักษะพนักงาน</p>
                @auth
                    </a>
                @else
                    </button>
                @endauth

                <!-- Management Dashboards (HR Admin Only) -->
                @if ($isHrOrAdmin)
                    <a href="{{ route('manpower.dashboard') }}" class="group bg-white dark:bg-slate-800 rounded-xl p-8 text-center shadow-sm border border-slate-200 dark:border-slate-700 hover:border-kumwell-red dark:hover:border-kumwell-red hover:shadow-md hover:-translate-y-1 transition-all reveal opacity-0 translate-y-8 duration-700 delay-200 focus:outline-none focus:ring-2 focus:ring-kumwell-red focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                        <div class="text-4xl text-slate-300 dark:text-slate-600 group-hover:text-kumwell-red group-hover:scale-110 transition-all mb-6">
                            <i class="fa-solid fa-users-gear"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white uppercase mb-2">ระบบอัตรากำลังพล</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">ระบบจัดการและวิเคราะห์อัตรากำลังพล</p>
                    </a>

                    <a href="{{ route('request.data') }}" class="group bg-white dark:bg-slate-800 rounded-xl p-8 text-center shadow-sm border border-slate-200 dark:border-slate-700 hover:border-kumwell-red dark:hover:border-kumwell-red hover:shadow-md hover:-translate-y-1 transition-all reveal opacity-0 translate-y-8 duration-700 delay-300 focus:outline-none focus:ring-2 focus:ring-kumwell-red focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                        <div class="text-4xl text-slate-300 dark:text-slate-600 group-hover:text-kumwell-red group-hover:scale-110 transition-all mb-6">
                            <i class="fa-solid fa-database"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white uppercase mb-2">ระบบจัดการข้อมูล</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">จัดการข้อมูลพนักงานและฐานข้อมูลระบบ</p>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- ==================== BLOG / NEWS ==================== -->
    <div id="news-grid" class="py-24 bg-white dark:bg-slate-900">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col sm:flex-row justify-between items-end mb-12 reveal opacity-0 translate-y-8 transition-all duration-700">
                <div>
                    <div class="flex items-center gap-2 text-xs font-bold tracking-widest uppercase text-kumwell-red mb-3">
                        <span class="w-5 h-0.5 bg-kumwell-red rounded-full"></span> Blog & News
                    </div>
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white">
                        ข่าวสาร <span class="text-kumwell-red">& ประชาสัมพันธ์</span>
                    </h2>
                </div>
                <div class="mt-6 sm:mt-0">
                    <a href="{{ route('news.newsAll') }}" class="inline-block px-6 py-2.5 border border-slate-300 dark:border-slate-700 rounded text-sm text-slate-600 dark:text-slate-400 hover:border-kumwell-red hover:text-kumwell-red hover:bg-slate-50 dark:hover:bg-slate-800 dark:hover:text-kumwell-red transition-colors focus:outline-none focus:ring-2 focus:ring-kumwell-red focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                        View All
                    </a>
                </div>
            </div>

            @if ($highlight || (isset($otherNews) && $otherNews->count() > 0))
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 reveal opacity-0 translate-y-8 transition-all duration-700 delay-100">
                    @foreach ($otherNews->take(3) as $item)
                        <a href="{{ route('news.detail', $item->news_id) }}" class="group flex flex-col h-full bg-white dark:bg-slate-800 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-kumwell-red focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                            <div class="w-full aspect-[16/9] relative bg-slate-100 dark:bg-slate-900 overflow-hidden">
                                <img src="{{ $item->image_path ? asset(is_array($item->image_path) ? $item->image_path[0] : $item->image_path) : 'https://placehold.co/600x400/e2e8f0/FFF?text=News' }}"
                                     alt="{{ $item->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                                <div class="absolute bottom-0 left-0 bg-kumwell-red text-white text-xs font-medium px-3 py-1.5 z-10">
                                    ข่าวสาร / News
                                </div>
                            </div>
                            <div class="p-6 flex-1 flex flex-col">
                                <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-4 line-clamp-2 leading-snug group-hover:text-kumwell-red transition-colors">
                                    {{ $item->title }}
                                </h3>
                                <div class="mt-auto text-xs text-slate-500 flex items-center gap-2">
                                    <i class="far fa-calendar-alt"></i> {{ $item->created_at ? $item->created_at->format('d M Y') : 'N/A' }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="border-2 border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 rounded-xl p-16 text-center text-slate-500 reveal opacity-0 translate-y-8 transition-all duration-700">
                    <i class="fa-regular fa-newspaper text-4xl mb-4 opacity-50"></i>
                    <p>ยังไม่มีข่าวประชาสัมพันธ์ในขณะนี้</p>
                </div>
            @endif
        </div>
    </div>

    <!-- ==================== TOPBAR (FOOTER ALIGNED) ==================== -->
    <div class="bg-slate-900 text-slate-400 text-xs py-4 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex flex-wrap justify-center md:justify-start gap-4 md:gap-6">
                <span><i class="fas fa-phone mr-1.5 opacity-70"></i> +66 2 123 4567</span>
                <span><i class="fas fa-envelope mr-1.5 opacity-70"></i> info@kumwell.com</span>
                <span><i class="fas fa-clock mr-1.5 opacity-70"></i> 08:00 – 17:30 น.</span>
            </div>
            <div class="flex items-center gap-6">
                <button aria-label="Toggle dark mode" onclick="toggleTheme()" class="hover:text-white transition-colors flex items-center gap-1.5 px-2 py-1 -ml-2 rounded focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <i class="fas fa-circle-half-stroke"></i> Theme
                </button>
                @auth
                    <span class="text-slate-300">สวัสดี, {{ Auth::user()->name }}</span>
                @else
                    <a href="{{ route('login') }}" class="hover:text-white transition-colors font-medium px-2 py-1 rounded focus:outline-none focus:ring-2 focus:ring-slate-500">เข้าสู่ระบบ</a>
                @endauth
            </div>
        </div>
    </div>
@endsection
