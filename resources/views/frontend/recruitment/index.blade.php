@extends('layouts.recruitment.app')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900 pb-36">
        <!-- Hero Section -->
        <div class="relative py-8 sm:py-10 md:py-14 lg:py-16 px-6 overflow-hidden bg-cover bg-center"
            style="background-image: url('https://image.makewebeasy.net/makeweb/crop/0etpaXZ92/contacts/S__78053393-2.jpg?v=202405291424&x=0&y=38&w=680&h=395&rw=640');">
            <!-- Gradient Overlay (Kumwell Red Gradient) -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#B21F24]/95 via-[#B21F24]/85 to-[#B21F24]/40"></div>

            <div class="max-w-6xl mx-auto relative z-10 text-center">
                <span
                    class="inline-block px-3 py-0.5 mb-2 text-[11px] sm:text-xs font-bold tracking-[0.2em] text-white/90 uppercase bg-white/10 backdrop-blur-md rounded-full border border-white/20">
                    Careers @ Kumwell
                </span>
                <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black text-white mb-2 md:mb-3 tracking-tight leading-tight drop-shadow-xl">
                    ร่วมเป็นส่วนหนึ่งกับ <span class="text-white/95">ครอบครัว Kumwell</span>
                </h1>
                <p class="text-white/90 text-xs sm:text-sm md:text-base max-w-2xl mx-auto font-medium leading-relaxed drop-shadow">
                    ค้นพบโอกาสในการเติบโตและสร้างสรรค์นวัตกรรม ไปพร้อมกับผู้นำด้านระบบป้องกันฟ้าผ่าและกราวด์ดิ้ง
                </p>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="max-w-5xl mx-auto -mt-6 sm:-mt-7 md:-mt-8 px-4 sm:px-6 relative z-20">
            <div class="bg-white dark:bg-slate-800 rounded-2xl md:rounded-[2rem] shadow-xl p-2.5 sm:p-3 md:p-4 border border-gray-100 dark:border-slate-700">
                <form id="recruitment-search-form" action="{{ route('recruitment.index') }}" method="GET" class="flex flex-col md:flex-row gap-2.5">
                    <div class="flex-grow min-w-0 relative group">
                        <i class="fa-solid fa-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-[#B21F24] transition-colors"></i>
                        <input type="text" id="recruitment-search-input" name="search" value="{{ request('search') }}" placeholder="ค้นหาชื่อตำแหน่งหรือสายงาน..."
                            class="w-full pl-12 pr-4 py-2.5 sm:py-3 md:py-3.5 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 focus:border-[#B21F24] rounded-xl md:rounded-2xl focus:ring-2 focus:ring-[#B21F24]/20 transition-all text-xs sm:text-sm md:text-base text-gray-900 dark:text-gray-100 placeholder:text-gray-400">
                    </div>
                    <div class="md:w-64 relative"
                        x-data="{ 
                            open: false, 
                            selectedId: '{{ request('department_id') }}',
                            selectedName: '{{ $departments->where('department_id', request('department_id'))->first()?->department_fullname ?: 'ทุกแผนก/สายงาน' }}'
                        }"
                        @click.away="open = false">
                        <input type="hidden" name="department_id" :value="selectedId">
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between px-4 py-3.5 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 hover:border-gray-300 rounded-xl md:rounded-2xl transition-all text-sm md:text-base text-gray-900 dark:text-gray-200">
                            <span x-text="selectedName" class="truncate font-medium"></span>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-cloak
                            class="absolute top-full left-0 w-full mt-2 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-gray-100 dark:border-slate-700 overflow-hidden z-50 py-1.5 max-h-60 overflow-y-auto">
                            <button type="button" @click="selectedId = ''; selectedName = 'ทุกแผนก/สายงาน'; open = false"
                                class="w-full text-left px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-slate-700 text-sm flex items-center justify-between"
                                :class="selectedId === '' ? 'text-[#B21F24] font-bold' : 'text-gray-700 dark:text-gray-300'">
                                <span>ทุกแผนก/สายงาน</span>
                                <i class="fa-solid fa-check text-xs" x-show="selectedId === ''"></i>
                            </button>
                            @foreach ($departments as $dept)
                                <button type="button"
                                    @click="selectedId = '{{ $dept->department_id }}'; selectedName = '{{ $dept->department_fullname }}'; open = false"
                                    class="w-full text-left px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-slate-700 text-sm flex items-center justify-between"
                                    :class="selectedId == '{{ $dept->department_id }}' ? 'text-[#B21F24] font-bold' : 'text-gray-700 dark:text-gray-300'">
                                    <span class="truncate pr-2">{{ $dept->department_fullname }}</span>
                                    <i class="fa-solid fa-check text-xs shrink-0" x-show="selectedId == '{{ $dept->department_id }}'"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <!-- Retain sort param -->
                    <input type="hidden" name="sort" value="{{ request('sort', 'relevant') }}">
                    <button type="submit"
                        class="bg-[#B21F24] hover:bg-[#8e181c] text-white font-bold px-8 py-3.5 rounded-xl md:rounded-2xl shadow-md transition-all active:scale-[0.98]">
                        ค้นหา
                    </button>
                </form>
            </div>
        </div>



        <!-- Main Content Area: Poster Left + Job List + JOBBKK Filter Controls & Bottom Cards -->
        <div class="w-full max-w-[1760px] mx-auto mt-8 sm:mt-10 lg:mt-12 px-3 sm:px-6 relative" 
             x-data="{ 
                showPosterModal: false, 
                modalPosterSrc: '', 
                modalPosterTitle: '',
                zoomLevel: 0.6,
                openModal(src, title) {
                    this.modalPosterSrc = src;
                    this.modalPosterTitle = title;
                    this.zoomLevel = 0.6;
                    this.showPosterModal = true;
                },
                toggleZoom() {
                    if (this.zoomLevel <= 0.6) {
                        this.zoomLevel = 0.8;
                    } else if (this.zoomLevel <= 0.8) {
                        this.zoomLevel = 1.0;
                    } else if (this.zoomLevel <= 1.0) {
                        this.zoomLevel = 1.2;
                    } else {
                        this.zoomLevel = 0.6;
                    }
                },
                zoomIn() {
                    this.zoomLevel = Math.min(+(this.zoomLevel + 0.1).toFixed(2), 2.5);
                },
                zoomOut() {
                    this.zoomLevel = Math.max(+(this.zoomLevel - 0.1).toFixed(2), 0.3);
                },
                resetZoom() {
                    this.zoomLevel = 0.6;
                }
             }">
            <div class="flex flex-col lg:flex-row gap-5 xl:gap-6 items-start justify-center">

                <!-- Left Column: POPULAR SEARCH + Poster โปรโมท (ถ้ามี) -->
                @php
                    $leftPopularKeywords = $popularKeywords ?? [
                        'วิศวกรไฟฟ้า', 'วิศวกรเครื่องกล', 'ช่างเทคนิค', 'ช่างซ่อมคอม',
                        'โปรแกรมเมอร์', 'Developer', 'เจ้าหน้าที่การตลาด', 'เจ้าหน้าที่บุคคล (HR)',
                        'เจ้าหน้าที่บัญชีและการเงิน', 'เจ้าหน้าที่จัดซื้อ', 'เจ้าหน้าที่ความปลอดภัย (จป.)',
                        'เจ้าหน้าที่ธุรการ/ประสานงาน'
                    ];
                @endphp
                <div class="w-full lg:w-[280px] xl:w-[320px] 2xl:w-[360px] shrink-0 lg:sticky lg:top-20 xl:top-24 mx-auto lg:mx-0 space-y-3.5 sm:space-y-4">
                    
                    {{-- POPULAR SEARCH Box (แถบซ้าย บนโปสเตอร์) --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm p-4 sm:p-5">
                        <div class="flex items-center gap-2 mb-3 pb-2.5 border-b border-gray-100 dark:border-slate-700/80">
                            <span class="w-2 h-2 rounded-full bg-[#B21F24] animate-ping"></span>
                            <h3 class="text-xs sm:text-sm font-black text-gray-800 dark:text-white uppercase tracking-wider">
                                POPULAR SEARCH
                            </h3>
                        </div>
                        <div class="flex flex-wrap gap-1.5 sm:gap-2">
                            @foreach($leftPopularKeywords as $kw)
                                @php
                                    $isSelected = request('search') === $kw;
                                @endphp
                                <a href="{{ route('recruitment.index', array_merge(request()->except('page'), ['search' => $isSelected ? null : $kw])) }}"
                                    class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs transition-all duration-200 cursor-pointer select-none font-medium border {{ $isSelected ? 'bg-[#B21F24] text-white border-[#B21F24] shadow-xs' : 'bg-gray-50 hover:bg-gray-100 dark:bg-slate-900/80 dark:hover:bg-slate-700 text-gray-600 dark:text-gray-300 border-gray-200/80 dark:border-slate-700 hover:text-[#B21F24] hover:border-red-200' }}"
                                    title="{{ $isSelected ? 'คลิกเพื่อล้างคำค้นหา' : 'ค้นหา ' . $kw }}">
                                    <span>{{ $kw }}</span>
                                    @if($isSelected)
                                        <i class="fa-solid fa-xmark text-[10px] ml-1"></i>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>

                    @if(isset($promoPosters) && $promoPosters->count() > 0)
                        {{-- โปสเตอร์ฝั่งซ้าย (แสดงเฉพาะจอคอมพิวเตอร์ / จอใหญ่ lg ขึ้นไป) --}}
                        <div class="hidden lg:block relative group recruitment-poster-container">
                            <div id="recruitment-poster-carousel" class="recruitment-poster-carousel relative overflow-hidden">
                                @foreach($promoPosters as $idx => $poster)
                                    @php
                                        $destUrl = $poster->action_url ? route('posters.click', $poster->id) : null;
                                    @endphp
                                    <div class="recruitment-poster-slide {{ $idx === 0 ? 'block' : 'hidden' }} transition-all duration-500" data-poster-id="{{ $poster->id }}">
                                        <div class="flex justify-center">
                                            <div class="relative inline-block group/item">
                                                @if($destUrl)
                                                    <a href="{{ $destUrl }}" target="_blank" rel="noopener noreferrer" class="block">
                                                @else
                                                    <div class="block cursor-pointer" 
                                                         @click="openModal('{{ asset($poster->image_path) }}', '{{ addslashes($poster->title) }}')">
                                                @endif
                                                    <img src="{{ asset($poster->image_path) }}" 
                                                         alt="{{ $poster->title }}"
                                                         class="block w-auto max-w-full h-auto max-h-[850px] object-contain hover:opacity-95 transition-all duration-300">
                                                @if($destUrl)
                                                    </a>
                                                @else
                                                    </div>
                                                @endif

                                                <!-- Enlarge button hint directly on the top-right corner of the image -->
                                                <button type="button" 
                                                    @click.stop="openModal('{{ asset($poster->image_path) }}', '{{ addslashes($poster->title) }}')"
                                                    class="absolute top-2.5 right-2.5 w-8 h-8 rounded-full bg-black/50 hover:bg-[#B21F24] text-white flex items-center justify-center opacity-0 group-hover/item:opacity-100 transition-all shadow-md backdrop-blur-xs cursor-pointer z-10"
                                                    title="คลิกเพื่อขยายและซูมดูภาพ">
                                                    <i class="fa-solid fa-magnifying-glass-plus text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($promoPosters->count() > 1)
                                <div class="flex items-center justify-between mt-3 px-1">
                                    <button type="button" class="poster-prev-btn w-7 h-7 rounded-full bg-white dark:bg-slate-800 hover:bg-[#B21F24] hover:text-white text-gray-700 dark:text-gray-200 flex items-center justify-center transition-colors shadow-sm cursor-pointer text-xs">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </button>
                                    <div class="flex gap-1.5 items-center justify-center">
                                        @foreach($promoPosters as $idx => $poster)
                                            <button type="button" class="recruitment-poster-dot w-2 h-2 rounded-full transition-all duration-300 cursor-pointer {{ $idx === 0 ? 'w-5 bg-[#B21F24]' : 'bg-gray-300 dark:bg-slate-600' }}" data-slide="{{ $idx }}"></button>
                                        @endforeach
                                    </div>
                                    <button type="button" class="poster-next-btn w-7 h-7 rounded-full bg-white dark:bg-slate-800 hover:bg-[#B21F24] hover:text-white text-gray-700 dark:text-gray-200 flex items-center justify-center transition-colors shadow-sm cursor-pointer text-xs">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Center Column: Search Result Header Bar + Job Postings List + Bottom Cards (Box 2) -->
                <div id="job-listings-section" class="w-full max-w-5xl space-y-5" x-data="{ viewMode: 'list' }">
                    
                    <!-- Search Criteria Breadcrumb Bar (Matching Reference Screenshot 1) -->
                    <div class="flex flex-wrap items-center gap-2 text-xs md:text-sm text-gray-500 dark:text-gray-400">
                        <span>ผลการค้นหา งานใน :</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-gray-100 dark:bg-slate-800 font-medium text-gray-700 dark:text-gray-300">
                            {{ request('search') ?: 'ทุกทำเล / ทุกตำแหน่ง' }}
                        </span>
                        <span class="ml-1">สาขาอาชีพ :</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-gray-100 dark:bg-slate-800 font-medium text-gray-700 dark:text-gray-300">
                            {{ $departments->where('department_id', request('department_id'))->first()?->department_fullname ?: 'ทุกสาขาอาชีพ' }}
                        </span>
                    </div>

                    <!-- Search Result Count + Display Toggle + Sort Dropdown (Matching Reference Screenshot 1) -->
                    <div class="flex flex-wrap items-center justify-between gap-4 pt-1 pb-3 border-b border-gray-200/70 dark:border-slate-800">
                        <!-- Result Count -->
                        <div class="text-base md:text-lg font-bold text-gray-800 dark:text-white">
                            ผลการค้นพบ <span class="text-[#B21F24] font-black text-xl">{{ number_format($posts->total()) }}</span> ตำแหน่ง
                        </div>

                        <!-- Right Controls: View Toggle & Sort Dropdown -->
                        <div class="flex items-center gap-3 md:gap-4 flex-wrap">
                            <!-- View Toggle (Grid / List) -->
                            <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                <span>การแสดงผล :</span>
                                <button type="button" @click="viewMode = 'grid'" 
                                    :class="viewMode === 'grid' ? 'text-[#B21F24]' : 'text-gray-400 hover:text-gray-600'" 
                                    class="p-1 text-sm transition-colors" title="มุมมองตาราง">
                                    <i class="fa-solid fa-table-cells-large"></i>
                                </button>
                                <button type="button" @click="viewMode = 'list'" 
                                    :class="viewMode === 'list' ? 'text-[#B21F24]' : 'text-gray-400 hover:text-gray-600'" 
                                    class="p-1 text-sm transition-colors" title="มุมมองรายการ">
                                    <i class="fa-solid fa-list"></i>
                                </button>
                            </div>

                            <!-- Sort Dropdown Menu -->
                            @php
                                $sortLabels = [
                                    'relevant' => 'ความเหมาะสม',
                                    'latest' => 'ตำแหน่งล่าสุด',
                                    'company' => 'ชื่อบริษัท',
                                    'salary_max' => 'เงินเดือนสูงสุด',
                                    'salary_min' => 'เงินเดือนต่ำสุด'
                                ];
                                $currentSort = request('sort', 'relevant');
                                $currentLabel = $sortLabels[$currentSort] ?? 'ความเหมาะสม';
                            @endphp
                            <div class="relative" x-data="{ sortOpen: false }" @click.away="sortOpen = false">
                                <button type="button" @click="sortOpen = !sortOpen"
                                    class="flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs md:text-sm font-medium text-gray-700 dark:text-gray-200 hover:border-gray-400 transition-colors shadow-sm">
                                    <i class="fa-solid fa-arrow-down-short-wide text-[#B21F24] text-xs"></i>
                                    <span>เรียงลำดับตาม : {{ $currentLabel }}</span>
                                    <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 transition-transform" :class="sortOpen ? 'rotate-180' : ''"></i>
                                </button>

                                <div x-show="sortOpen" x-cloak
                                    class="absolute right-0 top-full mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-gray-100 dark:border-slate-700 py-1.5 z-40 text-xs md:text-sm">
                                    @foreach($sortLabels as $key => $label)
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => $key]) }}"
                                            class="block px-4 py-2 hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors {{ $currentSort === $key ? 'text-[#B21F24] font-bold bg-red-50/50 dark:bg-slate-700/50' : 'text-gray-700 dark:text-gray-200' }}">
                                            {{ $label }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job Listings Cards (List or Grid View) -->
                    <div :class="viewMode === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 gap-4' : 'space-y-3.5'">
                        @forelse($posts as $post)
                            <a href="{{ route('recruitment.show', ['slug' => $post->slug, 'from_card' => 1]) }}"
                                class="block bg-white dark:bg-slate-800 rounded-xl border border-gray-200/80 dark:border-slate-700/80 p-4 md:p-5 hover:border-red-400 hover:shadow-lg transition-all duration-200 group">
                                <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                                    <!-- Left Content Area -->
                                    <div class="flex-grow min-w-0 space-y-2.5">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white group-hover:text-[#B21F24] transition-colors leading-snug">
                                                    {{ $post->position_name }}
                                                </h3>
                                                @if(($post->urgency ?? 'urgent') === 'very_urgent')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-[11px] font-bold border border-amber-500/40 text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 shadow-xs">
                                                        <span>⚡</span> รับสมัครด่วนมาก
                                                    </span>
                                                @elseif(($post->urgency ?? 'urgent') === 'urgent')
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-[11px] font-semibold border border-red-500/30 text-[#B21F24] bg-red-50 dark:bg-red-950/30">
                                                        <span>🔥</span> รับสมัครด่วน
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Kumwell Corporation Public Company Limited
                                            </p>
                                        </div>

                                        <!-- Meta Info Row -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-1.5 gap-x-4 text-xs md:text-sm text-gray-600 dark:text-gray-300 pt-1">
                                            <div class="flex items-center gap-2">
                                                <i class="fa-solid fa-location-dot text-[#B21F24] text-xs w-4 text-center"></i>
                                                <span class="truncate">{{ $post->location ?: 'สำนักงานใหญ่, กรุงเทพฯ' }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i class="fa-solid fa-money-bill-wave text-[#B21F24] text-xs w-4 text-center"></i>
                                                <span class="font-medium">
                                                    @if($post->salary_min && $post->salary_max)
                                                        {{ number_format($post->salary_min) }} - {{ number_format($post->salary_max) }} บาท
                                                    @else
                                                        ตามตกลง
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i class="fa-solid fa-briefcase text-[#B21F24] text-xs w-4 text-center"></i>
                                                <span class="truncate">{{ $post->department->department_fullname ?? 'ทั่วไป' }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <i class="fa-regular fa-clock text-[#B21F24] text-xs w-4 text-center"></i>
                                                <span>{{ $post->employment_type ?: 'งานประจำ (Full-time)' }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Area: Date + Logo -->
                                    <div class="sm:w-28 flex sm:flex-col items-center sm:items-end justify-between sm:justify-start gap-3 shrink-0 self-stretch sm:self-auto">
                                        <span class="text-[11px] text-gray-400 font-medium">
                                            <i class="fa-regular fa-calendar mr-1"></i>
                                            {{ $post->published_at ? $post->published_at->locale('th')->translatedFormat('j M ') . ($post->published_at->year + 543) : '-' }}
                                        </span>
                                        <div class="w-14 h-14 bg-white border border-gray-100 dark:border-slate-700 rounded-xl flex items-center justify-center p-2 shadow-sm group-hover:scale-105 transition-transform">
                                            <img src="{{ asset('images/logos/th-kumwell-logo.png') }}" alt="Kumwell Logo"
                                                class="w-full object-contain">
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="py-16 text-center bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                    <i class="fa-solid fa-briefcase text-2xl"></i>
                                </div>
                                <h3 class="text-base font-bold text-gray-800 dark:text-white">ไม่พบตำแหน่งงานที่ต้องการ</h3>
                                <p class="text-gray-500 text-xs mt-1">กรุณาลองค้นหาด้วยคำสำคัญอื่น หรือเลือกทุกแผนก</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Clean Red-Numbered Pagination (Matching Reference Screenshot 2: [1] 2 3 4 5 6 7 >) -->
                    @if($posts->hasPages())
                        <div class="flex items-center justify-center gap-1.5 pt-6">
                            @if ($posts->onFirstPage())
                                <span class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 text-gray-300 text-xs cursor-not-allowed">&lt;</span>
                            @else
                                <a href="{{ $posts->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 text-gray-700 hover:border-red-500 hover:text-[#B21F24] text-xs transition-colors">&lt;</a>
                            @endif

                            @foreach(range(1, min(7, $posts->lastPage())) as $page)
                                @if($page == $posts->currentPage())
                                    <span class="w-8 h-8 flex items-center justify-center rounded bg-[#B21F24] text-white font-bold text-xs shadow-sm">{{ $page }}</span>
                                @else
                                    <a href="{{ $posts->url($page) }}" class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 text-gray-700 hover:border-red-500 hover:text-[#B21F24] text-xs transition-colors">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($posts->hasMorePages())
                                <a href="{{ $posts->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 text-gray-700 hover:border-red-500 hover:text-[#B21F24] text-xs transition-colors">&gt;</a>
                            @else
                                <span class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 text-gray-300 text-xs cursor-not-allowed">&gt;</span>
                            @endif
                        </div>
                    @endif

                    <!-- Bottom 2 Cards: 5 สาขาอาชีพยอดนิยม & คุณกำลังต้องการงานด่วน ใช่ไหม! (ล่างสุด) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-8">
                        <!-- Card 1: 5 สาขาอาชีพยอดนิยม -->
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200/80 dark:border-slate-700/80 p-6 shadow-sm">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white pb-3 border-b border-gray-100 dark:border-slate-700/60">
                                5 สาขาอาชีพยอดนิยม
                            </h3>
                            <ul class="divide-y divide-gray-100 dark:divide-slate-700/40 text-sm">
                                @forelse($popularCategories as $cat)
                                    <li class="py-3 flex items-center justify-between">
                                        <a href="{{ route('recruitment.index', ['department_id' => $cat->department_id]) }}" 
                                            class="text-gray-700 dark:text-gray-300 hover:text-[#B21F24] transition-colors truncate pr-2">
                                            {{ $cat->department_fullname }}
                                        </a>
                                        <span class="text-[#B21F24] font-bold text-xs shrink-0">
                                            ({{ number_format($cat->job_posts_count ?? 0) }})
                                        </span>
                                    </li>
                                @empty
                                    <li class="py-3 text-gray-400 text-xs text-center">ไม่มีข้อมูลสาขายอดนิยม</li>
                                @endforelse
                            </ul>
                        </div>

                        <!-- Card 2: คุณกำลังต้องการงานด่วน ใช่ไหม! -->
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200/80 dark:border-slate-700/80 p-6 shadow-sm flex flex-col items-center text-center justify-center space-y-4">
                            <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white">
                                คุณกำลังต้องการงานด่วน ใช่ไหม!
                            </h3>
                            <a href="#recruitment-search" onclick="window.scrollTo({top: 150, behavior: 'smooth'}); return false;"
                                class="inline-block bg-[#B21F24] hover:bg-[#8e181c] text-white font-bold px-7 py-2.5 rounded-full shadow-md transition-all active:scale-[0.98] text-sm">
                                คลิก ปุ่มต้องการงานด่วน
                            </a>
                            <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed max-w-sm">
                                เพิ่มโอกาสในการได้งาน ด้วยตำแหน่งงานที่พร้อมสัมภาษณ์และเริ่มงานได้ทันที คัดเลือกเรซูเม่ของคุณโดยตรงจากทีมสรรหา
                            </p>
                        </div>
                    </div>

                    {{-- โปสเตอร์ล่างสุด (สำหรับ iPad / แท็บเล็ต / มือถือ ที่หน้าจอเล็กกว่า lg) --}}
                    @if(isset($promoPosters) && $promoPosters->count() > 0)
                        <div class="block lg:hidden pt-6 pb-2 max-w-sm sm:max-w-md mx-auto w-full">
                            <div class="relative group recruitment-poster-container">
                                <div class="recruitment-poster-carousel relative overflow-hidden">
                                    @foreach($promoPosters as $idx => $poster)
                                        @php
                                            $destUrl = $poster->action_url ? route('posters.click', $poster->id) : null;
                                        @endphp
                                        <div class="recruitment-poster-slide {{ $idx === 0 ? 'block' : 'hidden' }} transition-all duration-500" data-poster-id="{{ $poster->id }}">
                                            <div class="flex justify-center">
                                                <div class="relative inline-block group/item">
                                                    @if($destUrl)
                                                        <a href="{{ $destUrl }}" target="_blank" rel="noopener noreferrer" class="block">
                                                    @else
                                                        <div class="block cursor-pointer" 
                                                             @click="openModal('{{ asset($poster->image_path) }}', '{{ addslashes($poster->title) }}')">
                                                    @endif
                                                        <img src="{{ asset($poster->image_path) }}" 
                                                             alt="{{ $poster->title }}"
                                                             class="block w-auto max-w-full h-auto max-h-[850px] object-contain hover:opacity-95 transition-all duration-300">
                                                    @if($destUrl)
                                                        </a>
                                                    @else
                                                        </div>
                                                    @endif

                                                    <!-- Enlarge button hint directly on the top-right corner of the image -->
                                                    <button type="button" 
                                                        @click.stop="openModal('{{ asset($poster->image_path) }}', '{{ addslashes($poster->title) }}')"
                                                        class="absolute top-2.5 right-2.5 w-8 h-8 rounded-full bg-black/50 hover:bg-[#B21F24] text-white flex items-center justify-center opacity-0 group-hover/item:opacity-100 transition-all shadow-md backdrop-blur-xs cursor-pointer z-10"
                                                        title="คลิกเพื่อขยายและซูมดูภาพ">
                                                        <i class="fa-solid fa-magnifying-glass-plus text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if($promoPosters->count() > 1)
                                    <div class="flex items-center justify-between mt-3 px-1">
                                        <button type="button" class="poster-prev-btn w-7 h-7 rounded-full bg-white dark:bg-slate-800 hover:bg-[#B21F24] hover:text-white text-gray-700 dark:text-gray-200 flex items-center justify-center transition-colors shadow-sm cursor-pointer text-xs">
                                            <i class="fa-solid fa-chevron-left"></i>
                                        </button>
                                        <div class="flex gap-1.5 items-center justify-center">
                                            @foreach($promoPosters as $idx => $poster)
                                                <button type="button" class="recruitment-poster-dot w-2 h-2 rounded-full transition-all duration-300 cursor-pointer {{ $idx === 0 ? 'w-5 bg-[#B21F24]' : 'bg-gray-300 dark:bg-slate-600' }}" data-slide="{{ $idx }}"></button>
                                            @endforeach
                                        </div>
                                        <button type="button" class="poster-next-btn w-7 h-7 rounded-full bg-white dark:bg-slate-800 hover:bg-[#B21F24] hover:text-white text-gray-700 dark:text-gray-200 flex items-center justify-center transition-colors shadow-sm cursor-pointer text-xs">
                                            <i class="fa-solid fa-chevron-right"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Right Column Balancer: Keeps Box 2 centered on ultra-wide screens (2xl+) -->
                <div class="hidden 2xl:block 2xl:w-[360px] shrink-0 pointer-events-none" aria-hidden="true"></div>

            </div>

            <!-- Poster Lightbox Modal with Zoom & Pan Controls -->
            <div x-show="showPosterModal" 
                style="display: none;"
                @keydown.escape.window="showPosterModal = false"
                @click="showPosterModal = false"
                class="fixed inset-0 z-[9999] flex flex-col items-center justify-start p-3 sm:p-6 bg-black/90 backdrop-blur-md overflow-y-auto cursor-pointer">
                
                <!-- Floating Controls Header -->
                <div @click.stop
                    class="sticky top-2 z-30 flex flex-wrap items-center justify-center gap-1.5 sm:gap-2 bg-slate-900/95 border border-white/20 text-white rounded-full px-3 py-1.5 sm:px-4 sm:py-2 shadow-2xl backdrop-blur-md mb-4 text-xs sm:text-sm cursor-default">
                    <!-- Zoom Out -->
                    <button type="button" @click.stop="zoomOut()" class="w-7 h-7 sm:w-8 sm:h-8 rounded-full hover:bg-white/20 active:scale-95 flex items-center justify-center transition-all cursor-pointer" title="ซูมออก (-)">
                        <i class="fa-solid fa-minus text-xs"></i>
                    </button>
                    <!-- Zoom Percentage -->
                    <span class="px-1.5 sm:px-2 font-mono font-bold text-xs min-w-[42px] text-center text-amber-300" x-text="Math.round(zoomLevel * 100) + '%'"></span>
                    <!-- Zoom In -->
                    <button type="button" @click.stop="zoomIn()" class="w-7 h-7 sm:w-8 sm:h-8 rounded-full hover:bg-white/20 active:scale-95 flex items-center justify-center transition-all cursor-pointer" title="ซูมเข้า (+)">
                        <i class="fa-solid fa-plus text-xs"></i>
                    </button>
                    
                    <span class="w-px h-4 bg-white/20 mx-0.5"></span>
                    
                    <!-- Reset / Fit -->
                    <button type="button" @click.stop="resetZoom()" class="px-2.5 py-1 rounded-full hover:bg-white/20 active:scale-95 text-xs transition-all cursor-pointer flex items-center gap-1" title="ขนาดเริ่มต้น (60%)">
                        <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                        <span class="hidden sm:inline">เริ่มต้น (60%)</span>
                    </button>
                    
                    <!-- Open full in new tab -->
                    <a :href="modalPosterSrc" target="_blank" rel="noopener noreferrer" class="px-2.5 py-1 rounded-full hover:bg-white/20 active:scale-95 text-xs transition-all cursor-pointer flex items-center gap-1 text-gray-200 hover:text-white" title="เปิดดูภาพต้นฉบับ">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        <span class="hidden sm:inline">ภาพเต็ม</span>
                    </a>

                    <span class="w-px h-4 bg-white/20 mx-0.5"></span>

                    <!-- Close Button -->
                    <button type="button" @click.stop="showPosterModal = false" class="bg-red-600/85 hover:bg-red-600 active:scale-95 text-white px-3 py-1 rounded-full text-xs font-bold transition-all cursor-pointer flex items-center gap-1 shadow">
                        <i class="fa-solid fa-xmark"></i>
                        <span>ปิด (ESC)</span>
                    </button>
                </div>

                <!-- Zoomable Image Container (clicking black area outside image closes modal) -->
                <div class="relative flex items-center justify-center my-auto transition-all duration-200 py-2 w-full"
                     @click.self="showPosterModal = false">
                    <img :src="modalPosterSrc" 
                         :alt="modalPosterTitle" 
                         :style="`width: min(${Math.round(850 * zoomLevel)}px, ${Math.round(95 * Math.max(1, zoomLevel / 0.6))}vw); max-width: none;`"
                         :class="zoomLevel > 0.6 ? 'cursor-zoom-out' : 'cursor-zoom-in'"
                         @click.stop="toggleZoom()"
                         title="คลิกที่รูปเพื่อซูมเข้า / ซูมออก"
                         class="h-auto object-contain shadow-2xl transition-all duration-200 select-none">
                </div>

                <!-- Hint text at bottom -->
                <div @click.stop class="text-[11px] text-gray-400 mt-3 pb-2 text-center select-none cursor-default">
                    💡 คลิกที่รูปภาพเพื่อซูม | คลิกแถบสีดำด้านนอก หรือกด ESC เพื่อปิด
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const containers = document.querySelectorAll('.recruitment-poster-container');
            if (!containers.length) return;

            const recordedIds = new Set();
            function trackPoster(element) {
                if (!element) return;
                const pid = element.getAttribute('data-poster-id');
                if (pid && !recordedIds.has(pid)) {
                    recordedIds.add(pid);
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    fetch('{{ route("posters.track-views") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token || ''
                        },
                        body: JSON.stringify({ poster_ids: [pid] })
                    }).catch(() => {});
                }
            }

            containers.forEach(container => {
                const slides = container.querySelectorAll('.recruitment-poster-slide');
                const dots = container.querySelectorAll('.recruitment-poster-dot');
                const prevBtn = container.querySelector('.poster-prev-btn');
                const nextBtn = container.querySelector('.poster-next-btn');
                let currentSlide = 0;
                let autoPlayTimer = null;

                if (slides.length > 0) {
                    trackPoster(slides[0]);

                    if (slides.length > 1) {
                        function showSlide(index) {
                            slides.forEach((slide, i) => {
                                if (i === index) {
                                    slide.classList.remove('hidden');
                                    slide.classList.add('block');
                                    trackPoster(slide);
                                } else {
                                    slide.classList.add('hidden');
                                    slide.classList.remove('block');
                                }
                            });

                            dots.forEach((dot, i) => {
                                if (i === index) {
                                    dot.className = 'recruitment-poster-dot w-5 h-2 rounded-full transition-all duration-300 bg-[#B21F24]';
                                } else {
                                    dot.className = 'recruitment-poster-dot w-2 h-2 rounded-full transition-all duration-300 bg-gray-300 dark:bg-slate-600';
                                }
                            });

                            currentSlide = index;
                        }

                        function nextSlide() {
                            const next = (currentSlide + 1) % slides.length;
                            showSlide(next);
                        }

                        function prevSlide() {
                            const prev = (currentSlide - 1 + slides.length) % slides.length;
                            showSlide(prev);
                        }

                        if (nextBtn) {
                            nextBtn.addEventListener('click', () => {
                                clearInterval(autoPlayTimer);
                                nextSlide();
                                startAutoPlay();
                            });
                        }

                        if (prevBtn) {
                            prevBtn.addEventListener('click', () => {
                                clearInterval(autoPlayTimer);
                                prevSlide();
                                startAutoPlay();
                            });
                        }

                        dots.forEach((dot, i) => {
                            dot.addEventListener('click', () => {
                                clearInterval(autoPlayTimer);
                                showSlide(i);
                                startAutoPlay();
                            });
                        });

                        function startAutoPlay() {
                            autoPlayTimer = setInterval(nextSlide, 5000);
                        }

                        startAutoPlay();
                    }
                }
            });
        });
    </script>
    @endpush
@endsection