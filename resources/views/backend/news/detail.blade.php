@extends('layouts.app')
@section('title', 'รายละเอียดข่าวสาร')

@section('content')
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
    @endphp

    <div class="p-4 md:p-8 text-slate-800 dark:text-gray-200 theme-transition">
        <!-- Breadcrumb & Date -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 text-sm md:text-base font-normal gap-4 max-w-7xl mx-auto w-full">
            <div class="flex items-center text-gray-600 dark:text-gray-300">
                <a href="{{ route('welcome') }}" class="hover:text-red-500 transition">หน้าแรก</a>
                <span class="mx-2 text-gray-400 dark:text-gray-500">&gt;</span>
                <a href="{{ route('news.newsAll') }}" class="hover:text-red-500 transition">ข่าวสาร</a>
                <span class="mx-2 text-gray-400 dark:text-gray-500">&gt;</span>
                <span class="text-red-500 font-medium truncate max-w-[150px] sm:max-w-[300px] lg:max-w-none">{{ $news->title }}</span>
            </div>
            <div class="flex items-center bg-gray-100 dark:bg-[#1c1f26] px-5 py-2 rounded-full border border-gray-200 dark:border-gray-800 shadow-sm shrink-0">
                <span class="text-gray-600 dark:text-gray-400 mr-2 font-medium">วันที่เผยแพร่ :</span>
                <span class="text-red-600 dark:text-red-500 font-bold">{{ \Carbon\Carbon::parse($news->published_date)->format('d/m/Y') }}</span>
            </div>
        </div>

        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
                <!-- Left Column: Gallery / Slider -->
                <div class="w-full flex flex-col">
                    <div
                        class="rounded-3xl overflow-hidden shadow-xl border border-gray-200 dark:border-gray-800/50 relative bg-white dark:bg-transparent">
                        <div id="slider" class="relative group aspect-video lg:aspect-[4/3]">
                            @foreach ($galleryImages as $idx => $img)
                                <div
                                    class="slide {{ $idx === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0 absolute inset-0' }} transition-opacity duration-500 w-full h-full flex items-center justify-center overflow-hidden bg-slate-100 dark:bg-slate-900">
                                    {{-- Blurred Background Fallback --}}
                                    <div class="absolute inset-0 z-0 scale-110 blur-2xl opacity-40">
                                        <img src="{{ $img }}" class="w-full h-full object-cover" loading="lazy">
                                    </div>
                                    {{-- Sharp Main Image --}}
                                    <img src="{{ $img }}" alt="{{ $news->title }}"
                                        class="relative z-10 max-w-full max-h-full object-contain shadow-2xl" loading="lazy" />
                                </div>
                            @endforeach

                            @if (count($galleryImages) > 1)
                                <button type="button"
                                    class="prev absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 dark:bg-black/40 hover:bg-white dark:hover:bg-black/70 text-slate-800 dark:text-white px-4 py-2 rounded-full text-lg shadow-md backdrop-blur-sm transition z-20 opacity-0 group-hover:opacity-100"><i
                                        class="fas fa-chevron-left"></i></button>
                                <button type="button"
                                    class="next absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 dark:bg-black/40 hover:bg-white dark:hover:bg-black/70 text-slate-800 dark:text-white px-4 py-2 rounded-full text-lg shadow-md backdrop-blur-sm transition z-20 opacity-0 group-hover:opacity-100"><i
                                        class="fas fa-chevron-right"></i></button>
                            @endif
                        </div>

                        @if (count($galleryImages) > 1)
                            <div
                                class="flex space-x-2 justify-center py-4 bg-gray-50 dark:bg-[#13161c] border-t border-gray-100 dark:border-gray-800">
                                @foreach ($galleryImages as $i => $img)
                                    <button
                                        class="dot w-2.5 h-2.5 rounded-full transition-colors {{ $i === 0 ? 'bg-red-500 scale-110' : 'bg-gray-300 dark:bg-gray-600 hover:bg-gray-400' }}"
                                        data-index="{{ $i }}"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if (count($galleryImages) > 1)
                        <div class="mt-6 px-2">
                            <div class="flex flex-wrap gap-3">
                                @foreach ($galleryImages as $i => $img)
                                    <button
                                        class="thumb group relative border-2 border-transparent hover:border-red-500 rounded-xl overflow-hidden transition-all shadow-sm"
                                        data-index="{{ $i }}">
                                        <div
                                            class="absolute inset-0 bg-black/20 group-hover:bg-transparent transition-colors z-10">
                                        </div>
                                        <img src="{{ $img }}" class="w-24 h-16 sm:w-28 sm:h-20 object-cover"
                                            alt="thumb {{ $i + 1 }}" loading="lazy" />
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column: Content & Attachments -->
                <div class="flex flex-col h-full py-2">
                    <h1 class="text-3xl md:text-4xl font-bold mb-6 text-slate-900 dark:text-red-500 leading-tight">
                        {{ $news->title }}
                    </h1>

                    <div
                        class="prose prose-slate dark:prose-invert prose-lg max-w-none text-slate-600 dark:text-gray-300 font-light leading-relaxed mb-8">
                        {!! nl2br(e($news->content)) !!}
                    </div>

                    @if (!empty($news->link_news))
                        <div
                            class="bg-blue-50 dark:bg-[#12151a] border border-blue-200 dark:border-blue-700/50 rounded-2xl px-6 py-5 mb-8 text-sm shadow-sm transition-transform hover:-translate-y-1">
                            <div class="flex items-center text-blue-600 dark:text-blue-400 font-semibold mb-2">
                                <i class="fas fa-link mr-2"></i> ลิงก์ที่เกี่ยวข้อง
                            </div>
                            <a href="{{ $news->link_news }}" target="_blank"
                                class="block truncate text-blue-500 dark:text-blue-300 hover:text-blue-600 dark:hover:text-blue-200 underline underline-offset-2">{{ $news->link_news }}</a>
                        </div>
                    @endif

                    @if (count($attachmentFiles))
                        <div class="mt-10 overflow-hidden bg-gray-100 dark:bg-[#181c22] rounded-2xl p-6  shadow-md mb-8">
                            <div class="flex items-center space-x-3 mb-6">
                                <div class="w-1.5 h-6 bg-red-600 rounded-full"></div>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">
                                    เอกสารแนบที่เกี่ยวข้อง</h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($attachmentFiles as $idx => $file)
                                    @php
                                        $fileLabel = basename($file);
                                        $fileUrl = Str::startsWith($file, ['http://', 'https://'])
                                            ? $file
                                            : asset($file);
                                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                                        // Map extension to icon and color
                                        $fileConfig = match ($ext) {
                                            'pdf' => [
                                                'icon' => 'fa-file-pdf',
                                                'color' => 'text-red-500',
                                                'bg' => 'bg-red-50 dark:bg-red-500/10',
                                            ],
                                            'doc', 'docx' => [
                                                'icon' => 'fa-file-word',
                                                'color' => 'text-blue-500',
                                                'bg' => 'bg-blue-50 dark:bg-blue-500/10',
                                            ],
                                            'xls', 'xlsx' => [
                                                'icon' => 'fa-file-excel',
                                                'color' => 'text-green-500',
                                                'bg' => 'bg-green-50 dark:bg-green-500/10',
                                            ],
                                            'ppt', 'pptx' => [
                                                'icon' => 'fa-file-powerpoint',
                                                'color' => 'text-orange-500',
                                                'bg' => 'bg-orange-50 dark:bg-orange-500/10',
                                            ],
                                            'jpg', 'jpeg', 'png', 'gif', 'svg', 'webp' => [
                                                'icon' => 'fa-file-image',
                                                'color' => 'text-purple-500',
                                                'bg' => 'bg-purple-50 dark:bg-purple-500/10',
                                            ],
                                            default => [
                                                'icon' => 'fa-file-lines',
                                                'color' => 'text-gray-500',
                                                'bg' => 'bg-gray-50 dark:bg-gray-500/10',
                                            ],
                                        };
                                    @endphp
                                    <div
                                        class="group relative bg-white dark:bg-[#1c1f26] border border-gray-100 dark:border-gray-800 rounded-2xl p-4 shadow-sm hover:shadow-xl hover:border-red-500/30 transition-all duration-300 transform hover:-translate-y-1">
                                        <div class="flex items-start">
                                            {{-- File Icon Badge --}}
                                            <div
                                                class="flex-shrink-0 w-12 h-12 {{ $fileConfig['bg'] }} {{ $fileConfig['color'] }} rounded-xl flex items-center justify-center text-xl shadow-inner transition-transform group-hover:scale-110">
                                                <i class="fas {{ $fileConfig['icon'] }}"></i>
                                            </div>

                                            {{-- File Info --}}
                                            <div class="ml-4 flex-grow min-w-0">
                                                <div class="flex flex-col">
                                                    <h4 class="text-sm font-semibold text-slate-800 dark:text-gray-200 truncate group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors mb-1"
                                                        title="{{ $fileLabel }}">
                                                        {{ $fileLabel }}
                                                    </h4>
                                                    <div
                                                        class="flex items-center space-x-3 text-[11px] text-gray-500 dark:text-gray-400 font-medium">
                                                        <span class="uppercase">{{ $ext }} FILE</span>
                                                        <span
                                                            class="w-1 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></span>
                                                        <span>{{ date('d/m/Y', file_exists(public_path($file)) ? filemtime(public_path($file)) : time()) }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Action Icon --}}
                                            <a href="{{ $fileUrl }}" target="_blank"
                                                class="ml-2 flex-shrink-0 w-10 h-10 bg-slate-100 dark:bg-gray-800 hover:bg-red-600 dark:hover:bg-red-600 text-slate-600 dark:text-gray-400 hover:text-white rounded-full flex items-center justify-center transition-all duration-300 shadow-sm"
                                                title="Download">
                                                <i class="fas fa-download text-sm"></i>
                                            </a>
                                        </div>

                                        {{-- Subtle Progress/Accent line --}}
                                        <div
                                            class="absolute bottom-0 left-4 right-4 h-0.5 bg-gradient-to-r from-transparent via-red-500/0 to-transparent group-hover:via-red-500/50 transition-all duration-500">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Share Section -->
            <div class="mt-12 border-t border-gray-200 dark:border-gray-800/50 pt-10">
                <div class="flex flex-col space-y-4 max-w-3xl">
                    <div class="flex items-center text-blue-600 dark:text-blue-400 space-x-2">
                        <i class="fas fa-share-nodes text-lg"></i>
                        <span class="font-medium text-lg">แชร์ข่าวนี้</span>
                    </div>

                    <div
                        class="flex flex-col sm:flex-row items-center bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden shadow-lg p-1.5 focus-within:ring-2 focus-within:ring-red-500/50 transition-all">
                        <div class="flex items-center pl-5 pr-2 w-full">
                            <i class="fas fa-link text-gray-500"></i>
                            <input type="text"
                                value="{{ request()->getSchemeAndHttpHost() . request()->getRequestUri() }}" id="shareLink"
                                readonly
                                class="bg-transparent border-none text-gray-300 w-full focus:ring-0 px-3 py-3 font-mono text-sm sm:text-base selection:bg-red-500/30">
                        </div>
                        <button onclick="copyToClipboard()"
                            class="w-full sm:w-auto mt-2 sm:mt-0 bg-[#EF4444] hover:bg-red-600 text-white px-8 py-3 rounded-xl font-medium transition duration-300 flex-shrink-0 flex items-center justify-center gap-2 shadow-sm">
                            <span>คัดลอก</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simple slider logic
        (function() {
            const slides = Array.from(document.querySelectorAll('#slider .slide'));
            const dots = Array.from(document.querySelectorAll('.dot'));
            const thumbs = Array.from(document.querySelectorAll('.thumb'));
            let current = 0;

            function show(index) {
                slides.forEach((s, i) => {
                    if (i === index) {
                        s.classList.remove('opacity-0', 'absolute');
                        s.classList.add('opacity-100');
                    } else {
                        s.classList.add('opacity-0', 'absolute');
                        s.classList.remove('opacity-100');
                    }
                });
                dots.forEach((d, i) => d.classList.toggle('bg-red-500', i === index));
                current = index;
            }
            document.querySelector('.prev')?.addEventListener('click', () => {
                show((current - 1 + slides.length) % slides.length);
            });
            document.querySelector('.next')?.addEventListener('click', () => {
                show((current + 1) % slides.length);
            });
            dots.forEach(d => d.addEventListener('click', () => show(parseInt(d.dataset.index))));
            thumbs.forEach(t => t.addEventListener('click', () => show(parseInt(t.dataset.index))));
        })();

        function copyToClipboard() {
            var copyText = document.getElementById('shareLink');
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value).then(() => {
                const btn = document.querySelector('button[onclick="copyToClipboard()"]');
                const originalText = btn.innerText;
                btn.innerText = 'คัดลอกแล้ว!';
                btn.classList.replace('bg-[#ef4444]', 'bg-green-600');
                setTimeout(() => {
                    btn.innerText = originalText;
                    btn.classList.replace('bg-green-600', 'bg-[#ef4444]');
                }, 2000);
            });
        }
    </script>
@endsection
