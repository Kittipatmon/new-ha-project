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
        $views = $news->views ?? 0;

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
                        @if (count($galleryImages) > 0)
                            <a href="{{ $mainImage }}" onclick="event.preventDefault(); if(window.openLightbox) window.openLightbox(0);" class="block w-full h-full cursor-zoom-in">
                                <img src="{{ $mainImage }}" alt="{{ $news->title }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500 ease-out">
                            </a>
                        @else
                            <img src="{{ $mainImage }}" alt="{{ $news->title }}" class="w-full h-full object-cover">
                        @endif
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
                    
                    <!-- Attachments -->
                    @if (count($attachmentFiles))
                        <div class="mb-6 pb-6 border-b border-slate-100 dark:border-slate-800/80">
                            <h3 class="text-xs md:text-sm font-bold text-slate-800 dark:text-white mb-3">เอกสารแนบที่เกี่ยวข้อง</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach ($attachmentFiles as $file)
                                    @php
                                        $fileLabel = basename($file);
                                        // Clean up upload prefix / hash
                                        $displayName = preg_replace('/^\d+_file_[a-f0-9]+_/', '', $fileLabel);
                                        if ($displayName === $fileLabel) {
                                            $displayName = preg_replace('/^\d+_/', '', $fileLabel);
                                        }
                                        $fileUrl = Str::startsWith($file, ['http://', 'https://']) ? $file : asset($file);
                                        
                                        $ext = strtolower(pathinfo($fileLabel, PATHINFO_EXTENSION));
                                        $icon = 'fa-regular fa-file';
                                        if ($ext === 'pdf') {
                                            $icon = 'fa-regular fa-file-pdf text-red-500';
                                        } elseif (in_array($ext, ['doc', 'docx'])) {
                                            $icon = 'fa-regular fa-file-word text-blue-500';
                                        } elseif (in_array($ext, ['xls', 'xlsx'])) {
                                            $icon = 'fa-regular fa-file-excel text-green-600';
                                        } elseif (in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'svg'])) {
                                            $icon = 'fa-regular fa-file-image text-purple-500';
                                        } elseif (in_array($ext, ['zip', 'rar', '7z'])) {
                                            $icon = 'fa-regular fa-file-zipper text-amber-600';
                                        }
                                    @endphp
                                    <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer" class="group flex items-center justify-between p-3 bg-white dark:bg-slate-900/40 hover:bg-slate-50 dark:hover:bg-slate-800/50 border border-slate-200 dark:border-slate-800 rounded-xl transition-all duration-300 hover:shadow-sm">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-8 h-8 rounded-lg bg-slate-50 dark:bg-slate-800/60 flex items-center justify-center text-sm shrink-0">
                                                <i class="{{ $icon }}"></i>
                                            </div>
                                            <span class="text-xs font-medium text-slate-700 dark:text-slate-350 truncate pr-2" title="{{ $displayName }}">
                                                {{ $displayName }}
                                            </span>
                                        </div>
                                        <div class="w-7 h-7 rounded-full bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 flex items-center justify-center text-xs text-slate-400 dark:text-slate-500 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors shrink-0">
                                            <i class="fa-solid fa-arrow-down"></i>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif


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
                                    <a href="{{ $img }}" class="gallery-item block overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm aspect-[16/10] relative group">
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

    <!-- Lightbox Modal Popup (Borderless) -->
    <div id="imageLightbox" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md hidden items-center justify-center z-[9999] transition-opacity duration-300 opacity-0 select-none" style="top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; height: 100vh !important; width: 100vw !important;">
        <!-- Close Button (Top Right) -->
        <button type="button" id="closeLightbox" class="absolute top-6 right-6 text-white/70 hover:text-white hover:scale-110 text-3xl focus:outline-none z-50 p-2 cursor-pointer transition-all duration-300" aria-label="Close Gallery">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <!-- Prev Button -->
        <button type="button" id="prevLightbox" class="absolute left-6 top-1/2 -translate-y-1/2 text-white/70 hover:text-white text-3xl focus:outline-none z-50 p-4 bg-white/10 hover:bg-white/20 rounded-full cursor-pointer transition-all duration-300 select-none" aria-label="Previous Image">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <!-- Next Button -->
        <button type="button" id="nextLightbox" class="absolute right-6 top-1/2 -translate-y-1/2 text-white/70 hover:text-white text-3xl focus:outline-none z-50 p-4 bg-white/10 hover:bg-white/20 rounded-full cursor-pointer transition-all duration-300 select-none" aria-label="Next Image">
            <i class="fa-solid fa-chevron-right"></i>
        </button>

        <!-- Main Image Container -->
        <div class="relative max-w-[85vw] max-h-[80vh] flex items-center justify-center">
            <img id="lightboxImage" src="" alt="Gallery Image" class="max-w-full max-h-[80vh] object-contain rounded-xl shadow-2xl transition-all duration-300 transform scale-95 opacity-0">
        </div>

        <!-- Image Indicator (Bottom Center) -->
        <div id="lightboxIndicator" class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/90 text-xs font-semibold tracking-wider bg-white/10 backdrop-blur-md px-4 py-2 rounded-full border border-white/10">
            รูปภาพที่ 1 / 1
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

    document.addEventListener('DOMContentLoaded', function() {
        const galleryImages = @json($galleryImages);
        let currentIndex = 0;

        const lightbox = document.getElementById('imageLightbox');
        const lightboxImg = document.getElementById('lightboxImage');
        const indicator = document.getElementById('lightboxIndicator');
        const closeBtn = document.getElementById('closeLightbox');
        const prevBtn = document.getElementById('prevLightbox');
        const nextBtn = document.getElementById('nextLightbox');

        if (!lightbox || galleryImages.length === 0) return;

        // Open Lightbox
        document.querySelectorAll('.gallery-item').forEach((item, index) => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                openLightbox(index);
            });
        });

        function openLightbox(index) {
            currentIndex = index;
            updateLightboxImage();
            
            // Show lightbox overlay
            lightbox.classList.remove('hidden');
            lightbox.classList.add('flex');
            
            // Trigger animation
            setTimeout(() => {
                lightbox.classList.remove('opacity-0');
                lightbox.classList.add('opacity-100');
                if (lightboxImg) {
                    lightboxImg.classList.remove('scale-95', 'opacity-0');
                    lightboxImg.classList.add('scale-100', 'opacity-100');
                }
            }, 10);
        }

        // Expose to window so the main featured image link can call it
        window.openLightbox = openLightbox;

        function closeLightbox() {
            lightbox.classList.remove('opacity-100');
            lightbox.classList.add('opacity-0');
            if (lightboxImg) {
                lightboxImg.classList.remove('scale-100', 'opacity-100');
                lightboxImg.classList.add('scale-95', 'opacity-0');
            }
            
            setTimeout(() => {
                lightbox.classList.remove('flex');
                lightbox.classList.add('hidden');
            }, 300);
        }

        function updateLightboxImage() {
            // Fade out current image slightly
            lightboxImg.classList.add('opacity-0');
            
            setTimeout(() => {
                lightboxImg.src = galleryImages[currentIndex];
                indicator.textContent = `รูปภาพที่ ${currentIndex + 1} / ${galleryImages.length}`;
                lightboxImg.classList.remove('opacity-0');
            }, 150);
        }

        function showNext() {
            currentIndex = (currentIndex + 1) % galleryImages.length;
            updateLightboxImage();
        }

        // Event listeners
        closeBtn.addEventListener('click', closeLightbox);
        nextBtn.addEventListener('click', showNext);
        prevBtn.addEventListener('click', function() {
            currentIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
            updateLightboxImage();
        });

        // Click outside modal content (on the overlay backdrop) to close
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });

        // Key listeners
        document.addEventListener('keydown', function(e) {
            if (lightbox.classList.contains('hidden')) return;

            if (e.key === 'Escape') {
                closeLightbox();
            } else if (e.key === 'ArrowRight') {
                showNext();
            } else if (e.key === 'ArrowLeft') {
                currentIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
                updateLightboxImage();
            }
        });
    });
    </script>
@endsection
