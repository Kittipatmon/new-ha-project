@extends('layouts.app')

@section('title', 'ข่าวสารและกิจกรรม')

@section('content')
    {{-- Background: ใช้สีพื้นหลังที่นุ่มนวลขึ้นใน Light mode และเข้มลึกใน Dark mode --}}
    <div class="min-h-screen transition-colors duration-300 pb-16 font-light" style="font-family: 'Kanit', sans-serif;">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 mb-6">
            {{-- Breadcrumb --}}
            <div class="text-sm md:text-base mb-6 font-normal">
                <a href="/" class="text-gray-800 dark:text-gray-100 hover:text-red-500 transition-colors">หน้าแรก</a>
                <span class="mx-2 text-gray-400">&gt;</span>
                <span class="text-red-500">ข่าวสาร</span>
            </div>

            {{-- Section Title --}}
            <div class="flex items-center">
                <h2 class="text-3xl font-light text-gray-800 dark:text-gray-100 pr-4">News</h2>
                <div class="flex-grow border-t border-gray-200 dark:border-gray-700"></div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Grid Content --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($newsItems as $index => $news)
                    @if ($index === 0)
                        {{-- Highlight Item (Spans all 3 columns) --}}
                        <article
                            class="flex flex-col md:flex-row bg-transparent group md:col-span-2 lg:col-span-3 mb-6 md:mb-4">
                            {{-- Image Section --}}
                            <a href="{{ route('news.detail', $news->news_id) }}"
                                class="relative w-full md:w-[65%] overflow-hidden block bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 aspect-[16/9]">
                                <img src="{{ $news->image_path ? asset(is_array($news->image_path) ? $news->image_path[0] : $news->image_path) : 'https://placehold.co/800x450/e2e8f0/FFF?text=Highlight' }}"
                                    alt="{{ $news->title }}" class="w-full h-full object-cover" loading="lazy">
                            </a>

                            {{-- Content Section --}}
                            <div class="flex flex-col pt-6 md:pt-0 md:pl-10 w-full md:w-[35%] justify-center items-start">
                                <div class="mb-5 bg-red-600 text-white text-xs px-4 py-1.5 inline-block">
                                    ข่าวสาร / News
                                </div>
                                <h2
                                    class="text-xl md:text-[1.35rem] font-normal text-gray-800 dark:text-gray-100 line-clamp-3 leading-relaxed">
                                    <a href="{{ route('news.detail', $news->news_id) }}"
                                        class="transition-all duration-300 group-hover:text-red-600 group-hover:[text-shadow:0_0_8px_rgba(220,38,38,0.4)]">
                                        {{ $news->title }}
                                    </a>
                                </h2>
                            </div>
                        </article>
                    @else
                        {{-- Standard Card Item --}}
                        <article class="flex flex-col bg-transparent group">

                            {{-- Image Section --}}
                            <a href="{{ route('news.detail', $news->news_id) }}"
                                class="relative w-full overflow-hidden block bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 aspect-[16/9]">
                                <img src="{{ $news->image_path ? asset(is_array($news->image_path) ? $news->image_path[0] : $news->image_path) : 'https://placehold.co/600x400/e2e8f0/FFF?text=News' }}"
                                    alt="{{ $news->title }}" class="w-full h-full object-cover" loading="lazy">

                                {{-- Category Tag on Image (Bottom Left) --}}
                                <div class="absolute bottom-0 left-0 bg-red-600 text-white text-xs px-4 py-1.5 z-10">
                                    ข่าวสาร / News
                                </div>
                            </a>

                            {{-- Content Section --}}
                            <div class="flex flex-col flex-grow pt-4">
                                {{-- Title --}}
                                <h2
                                    class="text-[1.05rem] font-normal text-gray-800 dark:text-gray-100 line-clamp-2 leading-relaxed">
                                    <a href="{{ route('news.detail', $news->news_id) }}"
                                        class="transition-all duration-300 group-hover:text-red-600 group-hover:[text-shadow:0_0_8px_rgba(220,38,38,0.4)]">
                                        {{ $news->title }}
                                    </a>
                                </h2>
                            </div>
                        </article>
                    @endif
                @empty
                    {{-- Empty State: Modern Styling --}}
                    <div class="col-span-full py-20 text-center">
                        <div class="relative inline-block">
                            <div class="absolute inset-0 bg-red-100 dark:bg-red-900/30 rounded-full blur-xl"></div>
                            <div
                                class="relative bg-white dark:bg-gray-800 p-6 rounded-full shadow-lg mb-6 mx-auto inline-flex">
                                <svg class="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">ยังไม่มีข่าวสารในขณะนี้</h3>
                        <p class="text-gray-500 dark:text-gray-400">ข้อมูลจะถูกอัปเดตเร็วๆ นี้ โปรดกลับมาติดตามใหม่อีกครั้ง
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if (method_exists($newsItems, 'links'))
                <div class="mt-16 flex justify-center">
                    {{-- 
                   หมายเหตุ: ถ้าต้องการเปลี่ยนสี Pagination ของ Laravel ต้องไปแก้ที่ 
                   vendor/laravel/framework/src/Illuminate/Pagination/resources/views/tailwind.blade.php 
                   หรือ publish views ออกมาแก้ 
                   
                   แต่เบื้องต้นสามารถ wrap ด้วย class นี้เพื่อให้ theme สีแดงทำงานได้ดีขึ้นหาก config tailwind ถูกต้อง
                --}}
                    <div class="[&_.active]:bg-red-600 [&_.active]:border-red-600">
                        {{ $newsItems->links() }}
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
