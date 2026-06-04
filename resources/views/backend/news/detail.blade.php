@extends('layouts.app')
@section('title', 'รายละเอียดข่าวสาร')

@section('content')
    @include('layouts.navigation')
    @php
        use Illuminate\Support\Str;

        // ==========================================
        // ส่วนที่ 1: จัดการรูปภาพ (Gallery Logic)
        // ==========================================
        $galleryImages = [];

        // $news->image_path is an array from the model's $casts.
// Iterate over it to build the gallery, ensuring only this news item's images are used.
        if (is_array($news->image_path)) {
            foreach ($news->image_path as $path) {
                if (!empty($path)) {
                    // Ensure path uses forward slashes for the asset helper
                    $galleryImages[] = asset(str_replace('\\', '/', $path));
                }
            }
        }

        // ==========================================
        // ส่วนที่ 2: จัดการไฟล์แนบ (Fix Error trim())
        // ==========================================
        $attachmentFiles = [];
        $rawFiles = $news->file_news; // ดึงค่าออกมาก่อน

        if (!empty($rawFiles)) {
            // กรณีที่ 1: เป็น Array อยู่แล้ว (เพราะ Model cast มาให้) -> ใช้ได้เลย
            if (is_array($rawFiles)) {
                $attachmentFiles = array_filter($rawFiles);
            }
            // กรณีที่ 2: เป็น String -> ต้องมา trim และ decode เอง
            elseif (is_string($rawFiles)) {
                $raw = trim($rawFiles);
                if (Str::startsWith($raw, '[')) {
                    // เป็น JSON String
                    $decoded = json_decode($raw, true);
                    if (is_array($decoded)) {
                        $attachmentFiles = array_filter($decoded);
                    }
                } else {
                    // เป็นข้อความคั่นด้วยคอมม่า
                    $attachmentFiles = array_filter(array_map('trim', explode(',', $raw)));
                }
            }
        }

        // ==========================================
        // ส่วนที่ 3: จัดการวันที่ (Thai date B.E. translation)
        // ==========================================
        $thai_months = [
            1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน',
            5 => 'พฤษภาคม', 6 => 'มิถุนายน', 7 => 'กรกฎาคม', 8 => 'สิงหาคม',
            9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
        ];
        $raw_date = $news->published_date ?? $news->created_at;
        $date = null;
        if ($raw_date) {
            $date = \Carbon\Carbon::parse($raw_date);
        }
        $views = ($news->news_id * 37) % 450 + 88;

        // ==========================================
        // ส่วนที่ 4: จัดการข้อมูลใน Sidebar
        // ==========================================
        $categories = \App\Models\datacenter\News::where('is_active', true)
            ->whereNotNull('newto')
            ->where('newto', '!=', '')
            ->distinct()
            ->pluck('newto');

        $latestNews = \App\Models\datacenter\News::where('is_active', true)
            ->where('news_id', '!=', $news->news_id)
            ->orderBy('published_date', 'desc')
            ->take(5)
            ->get();
    @endphp

    <div class="pt-20 md:pt-24 pb-16 text-slate-800 dark:text-gray-200 theme-transition bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800/80" style="font-family: 'Kanit', sans-serif;">
        <div class="max-w-6xl mx-auto px-6 lg:px-8">
            <!-- Breadcrumb & Date -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 text-sm font-normal gap-4 w-full">
                <div class="flex items-center text-gray-650 dark:text-gray-300">
                    <a href="{{ route('welcome') }}" class="hover:text-red-500 transition">หน้าแรก</a>
                    <span class="mx-2 text-gray-400 dark:text-gray-500">&gt;</span>
                    <a href="{{ route('news.newsAll') }}" class="hover:text-red-500 transition">ข่าวสาร</a>
                    <span class="mx-2 text-gray-400 dark:text-gray-500">&gt;</span>
                    <span class="text-red-500 font-medium truncate max-w-[150px] sm:max-w-[300px] lg:max-w-none">{{ $news->title }}</span>
                </div>
            </div>

            <!-- Header matching the screenshot -->
            <div class="flex items-center mb-12">
                <div class="bg-[#F5A623] text-white px-5 py-3 font-bold text-sm md:text-base flex items-center gap-2.5 shadow-sm shrink-0">
                    <i class="fa-solid fa-bullhorn text-sm"></i>
                    {{ $news->newto ?? 'ข่าวประชาสัมพันธ์' }}
                </div>
                <!-- Decorative repeating dot grid pattern -->
                <div class="flex-1 h-11 bg-[radial-gradient(#d1d5db_1px,transparent_1px)] dark:bg-[radial-gradient(#475569_1px,transparent_1px)] [background-size:5px_5px] opacity-90 ml-3 pointer-events-none"></div>
            </div>

            <!-- Two Column Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                
                <!-- Left Column (Main Content) -->
                <!-- Featured Image -->
            <div class="lg:col-span-9 flex flex-col">
                    @php
                        $mainImage = count($galleryImages) > 0 ? $galleryImages[0] : 'https://placehold.co/600x400/e2e8f0/FFF?text=News';
                    @endphp
                    <div class="relative max-w-lg mx-auto w-full overflow-hidden aspect-[16/10]">
                        <img src="{{ $mainImage }}" alt="{{ $news->title }}" class="w-full h-full object-cover">
                    </div>
                    <div class="max-w-lg mx-auto w-full h-[5px] bg-[#c4c4c4] dark:bg-[#c4c4c4] mb-1"></div>
                <div class="lg:col-span-9 flex flex-col bg-[#fafafa] dark:bg-slate-800/30 p-6 md:p-8">
                    <!-- News Title -->
                    <h1 class="text-lg md:text-xl lg:text-2xl font-normal text-[#4d4d4d] dark:text-slate-100 mb-3 leading-snug">
                        {{ $news->title }}
                    </h1>

                    <!-- Meta Information -->
                    <div class="text-[11px] md:text-xs text-slate-400 dark:text-slate-500 font-normal mb-6">
                        โพสต์เมื่อ 
                        @if($date)
                            {{ $date->day }} {{ $thai_months[$date->month] }} {{ $date->year + 543 }}
                        @else
                            N/A
                        @endif
                        <span class="ml-3">จำนวนผู้เข้าชม {{ $views }}</span>
                    </div>

                    <!-- Share Link Button -->
                    <div class="mb-6">
                        {{-- <button onclick="copyNewsLink(this)" class="inline-flex items-center gap-2 bg-[#2563eb] hover:bg-[#1d4ed8] text-white px-5 py-2.5 rounded-full text-xs font-bold shadow-sm transition-colors cursor-pointer">
                            <i class="fa-solid fa-link text-sm"></i>
                            <span>คัดลอกลิงค์ข่าวนี้</span>
                        </button> --}}
                    </div>

                    <!-- Article Body Content -->
                    <div class="text-xs md:text-sm text-slate-700 dark:text-slate-350 leading-relaxed space-y-4 font-normal mb-8">
                        {!! nl2br(e($news->content)) !!}
                    </div>

                    <!-- Share Link Box -->
                    <div class="bg-blue-50/30 dark:bg-blue-950/10 border border-dotted border-blue-400 dark:border-blue-800/80 rounded-xl p-8 text-center my-8">
                        <p class="text-slate-700 dark:text-slate-350 font-semibold text-xs md:text-sm mb-4">ถูกใจข่าวนี้? อย่าลืมแชร์บอกต่อเพื่อนๆ</p>
                        <button onclick="copyNewsLink(this)" class="inline-flex items-center gap-2 bg-[#2563eb] hover:bg-[#1d4ed8] text-white px-8 py-3 rounded-full text-xs font-bold shadow-sm transition-colors cursor-pointer">
                            <i class="fa-solid fa-link text-sm"></i>
                            <span>คัดลอกลิงค์แชร์</span>
                        </button>
                    </div>

                    <!-- Image Gallery Grid -->
                    @if (count($galleryImages) > 0)
                        <div class="mt-4">
                            <div class="grid grid-cols-3 gap-2 md:gap-3">
                                @foreach ($galleryImages as $img)
                                    <a href="{{ $img }}" target="_blank" class="block overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm aspect-[16/10] relative group">
                                        <img src="{{ $img }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" loading="lazy">
                                        <!-- Hover Overlay matching the screenshot -->
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center pointer-events-none">
                                            <div class="w-10 h-10 rounded-full bg-[#F5A623] flex items-center justify-center text-white shadow-md transform scale-90 group-hover:scale-100 transition-transform duration-300">
                                                <i class="fa-solid fa-image text-sm"></i>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Attachments -->
                    @if (count($attachmentFiles))
                        <div class="mt-8 border-t border-slate-100 dark:border-slate-800/80 pt-6">
                            <h3 class="text-sm font-bold text-slate-800 dark:text-white mb-4">เอกสารแนบที่เกี่ยวข้อง</h3>
                            <div class="flex flex-col gap-2">
                                @foreach ($attachmentFiles as $file)
                                    @php
                                        $fileLabel = basename($file);
                                        $fileUrl = Str::startsWith($file, ['http://', 'https://']) ? $file : asset($file);
                                    @endphp
                                    <a href="{{ $fileUrl }}" target="_blank" class="flex items-center gap-2 text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                        <i class="fa-solid fa-file-arrow-down"></i>
                                        <span>{{ $fileLabel }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

                <!-- Right Column (Sidebar) -->
                <div class="lg:col-span-3 flex flex-col gap-10">
                    
                    <!-- Widget 1: Categories -->
                    <div class="flex flex-col">
                        <!-- Widget Header -->
                        <div class="flex items-center mb-6">
                            <div class="bg-[#F5A623] text-white px-4 py-2 font-bold text-xs md:text-sm flex items-center shadow-sm shrink-0">
                                ประเภทข่าว
                            </div>
                            <div class="flex-1 h-8 bg-[radial-gradient(#d1d5db_1px,transparent_1px)] dark:bg-[radial-gradient(#475569_1px,transparent_1px)] [background-size:4px_4px] opacity-90 ml-2 pointer-events-none"></div>
                        </div>
                        <!-- Categories List -->
                        <div class="flex flex-col">
                            @foreach($categories as $cat)
                                <a href="{{ route('news.newsAll') }}?category={{ urlencode($cat) }}" class="py-2.5 text-xs md:text-sm text-slate-600 hover:text-red-650 dark:text-slate-350 dark:hover:text-red-500 border-b border-dotted border-slate-200 dark:border-slate-800/80 last:border-0 transition-colors font-medium">
                                    {{ $cat }}
                                </a>
                            @endforeach
                            <a href="{{ route('news.newsAll') }}" class="py-2.5 text-xs md:text-sm text-slate-650 hover:text-red-650 dark:text-slate-350 dark:hover:text-red-500 border-b border-dotted border-slate-200 dark:border-slate-800/80 last:border-0 transition-colors font-medium">
                                ข่าวทั้งหมด
                            </a>
                        </div>
                    </div>

                    <!-- Widget 2: Latest News -->
                    <div class="flex flex-col">
                        <!-- Widget Header -->
                        <div class="flex items-center mb-6">
                            <div class="bg-[#F5A623] text-white px-4 py-2 font-bold text-xs md:text-sm flex items-center shadow-sm shrink-0">
                                ข่าวล่าสุด
                            </div>
                            <div class="flex-1 h-8 bg-[radial-gradient(#d1d5db_1px,transparent_1px)] dark:bg-[radial-gradient(#475569_1px,transparent_1px)] [background-size:4px_4px] opacity-90 ml-2 pointer-events-none"></div>
                        </div>
                        <!-- Latest News List -->
                        <div class="flex flex-col gap-4">
                            @foreach($latestNews as $item)
                                <div class="flex gap-3 items-start pb-4 border-b border-dotted border-slate-200 dark:border-slate-800/80 last:border-0">
                                    <a href="{{ route('news.detail', $item->news_id) }}" class="w-16 h-16 shrink-0 border border-slate-200 dark:border-slate-800/80 overflow-hidden block rounded-none">
                                        <img src="{{ $item->image_path ? asset(is_array($item->image_path) ? $item->image_path[0] : $item->image_path) : 'https://placehold.co/150x150/e2e8f0/FFF?text=News' }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                    </a>
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('news.detail', $item->news_id) }}" class="text-xs font-normal text-slate-650 hover:text-red-650 dark:text-slate-350 dark:hover:text-red-500 transition-colors line-clamp-2 leading-snug">
                                            {{ $item->title }}
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
    @include('layouts.footer')

    <script>
    function copyNewsLink(btn) {
        navigator.clipboard.writeText(window.location.href).then(function() {
            var icon = btn.querySelector('i');
            var text = btn.querySelector('span');
            var origIcon = icon.className;
            var origText = text.textContent;
            icon.className = 'fa-solid fa-check text-sm';
            text.textContent = 'คัดลอกแล้ว!';
            btn.classList.remove('bg-[#2563eb]', 'hover:bg-[#1d4ed8]');
            btn.classList.add('bg-green-600', 'hover:bg-green-700');
            setTimeout(function() {
                icon.className = origIcon;
                text.textContent = origText;
                btn.classList.remove('bg-green-600', 'hover:bg-green-700');
                btn.classList.add('bg-[#2563eb]', 'hover:bg-[#1d4ed8]');
            }, 2000);
        });
    }
    </script>
@endsection
