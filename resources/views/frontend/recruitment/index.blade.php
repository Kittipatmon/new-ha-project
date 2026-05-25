@extends('layouts.recruitment.app')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900 pb-20">
        <!-- Hero Section -->
        <div class="relative py-20 px-6 overflow-hidden bg-cover bg-center"
            style="background-image: url('https://image.makewebeasy.net/makeweb/crop/0etpaXZ92/contacts/S__78053393-2.jpg?v=202405291424&x=0&y=38&w=680&h=395&rw=640');">
            <!-- Gradient Overlay (Kumwell Red Gradient) -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#B21F24]/95 via-[#B21F24]/80 to-[#B21F24]/40"></div>

            <!-- Glassmorphism decorative element -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-40">
                <div
                    class="absolute -top-[10%] -right-[10%] w-[40%] h-[60%] bg-gradient-to-br from-white/20 to-transparent rounded-full blur-3xl transform rotate-12">
                </div>
                <div
                    class="absolute -bottom-[20%] -left-[10%] w-[50%] h-[70%] bg-gradient-to-tr from-black/30 to-transparent rounded-full blur-3xl">
                </div>
            </div>

            <div class="max-w-6xl mx-auto relative z-10 text-center">
                <span
                    class="inline-block px-4 py-1.5 mb-6 text-xs font-bold tracking-[0.2em] text-white/90 uppercase bg-white/10 backdrop-blur-md rounded-full border border-white/20">
                    Careers @ Kumwell
                </span>
                <h1 class="text-5xl md:text-7xl font-black text-white mb-6 tracking-tight leading-tight drop-shadow-2xl">
                    ร่วมเป็นส่วนหนึ่งกับ<br /><span class="text-white/90">ครอบครัว Kumwell</span>
                </h1>
                <p class="text-white/90 text-lg md:text-xl max-w-2xl mx-auto font-medium leading-relaxed drop-shadow-lg">
                    ค้นพบโอกาสในการเติบโตและสร้างสรรค์นวัตกรรม<br />ไปพร้อมกับผู้นำด้านระบบป้องกันฟ้าผ่าและกราวด์ดิ้ง
                </p>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="max-w-5xl mx-auto -mt-12 px-6 relative z-20">
            <div
                class="bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-[0_25px_50px_-12px_rgba(0,0,0,0.15)] p-3 md:p-4 border border-gray-100 dark:border-slate-700">
                <form action="{{ route('recruitment.index') }}" method="GET" class="flex flex-col md:flex-row gap-2">
                    <div class="flex-grow relative group">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-kumwell-red transition-colors"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาชื่อตำแหน่ง..."
                            class="w-full pl-14 pr-6 py-4 bg-gray-50 dark:bg-slate-900 border-2 border-transparent focus:border-kumwell-red/10 focus:bg-white dark:focus:bg-slate-800 rounded-[1.8rem] focus:ring-4 focus:ring-kumwell-red/5 transition-all text-base text-gray-900 dark:text-gray-100 placeholder:text-gray-400">
                    </div>
                    <div class="md:w-64 relative"
                        x-data="{ 
                                                                                                                                            open: false, 
                                                                                                                                            selectedId: '{{ request('department_id') }}',
                                                                                                                                            selectedName: '{{ $departments->where('department_id', request('department_id'))->first()?->department_fullname ?: 'ทุกแผนก' }}'
                                                                                                                                        }"
                        @click.away="open = false">
                        <!-- Hidden Input for Form -->
                        <input type="hidden" name="department_id" :value="selectedId">

                        <!-- Trigger Button -->
                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between pl-6 pr-5 py-4 bg-gray-50 dark:bg-slate-900 border-2 border-transparent hover:border-gray-200 dark:hover:border-slate-700/50 rounded-[1.8rem] transition-all text-base text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-4 focus:ring-kumwell-red/5">
                            <span x-text="selectedName" class="truncate font-medium"></span>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300"
                                :class="open ? 'rotate-180' : ''"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                            class="absolute top-full left-0 w-full mt-3 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-slate-700 overflow-hidden z-50 py-2"
                            style="display: none;">

                            <!-- Option: All Departments -->
                            <button type="button" @click="selectedId = ''; selectedName = 'ทุกแผนก'; open = false"
                                class="w-full text-left px-5 py-3 hover:bg-gray-50 dark:hover:bg-slate-900/50 transition-colors flex items-center justify-between group">
                                <span class="text-sm"
                                    :class="selectedId === '' ? 'text-[#B21F24] font-bold' : 'text-gray-600 dark:text-gray-400'">ทุกแผนก</span>
                                <i class="fa-solid fa-check text-xs text-[#B21F24]" x-show="selectedId === ''"></i>
                            </button>

                            @foreach ($departments as $dept)
                                <button type="button"
                                    @click="selectedId = '{{ $dept->department_id }}'; selectedName = '{{ $dept->department_fullname }}'; open = false"
                                    class="w-full text-left px-5 py-3 hover:bg-gray-50 dark:hover:bg-slate-900/50 transition-colors flex items-center justify-between group">
                                    <span class="text-sm"
                                        :class="selectedId == '{{ $dept->department_id }}' ? 'text-[#B21F24] font-bold' : 'text-gray-600 dark:text-gray-400'">{{ $dept->department_fullname }}</span>
                                    <i class="fa-solid fa-check text-xs text-[#B21F24]"
                                        x-show="selectedId == '{{ $dept->department_id }}'"></i>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <button type="submit"
                        class="bg-[#B21F24] hover:bg-[#8e181c] text-white font-black px-10 py-4 rounded-[1.8rem] shadow-xl shadow-red-500/20 transition-all active:scale-[0.96] hover:shadow-red-500/40">
                        ค้นหา
                    </button>
                </form>
            </div>
        </div>

        <!-- Job List -->
        <div class="max-w-6xl mx-auto mt-12 px-6 ">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">ตำแหน่งงานที่เปิดรับ ({{ $posts->total() }})
                </h2>
                <div class="flex gap-2 bg-red-500">
                    <!-- Simple filter chips could go here -->
                </div>
            </div>

            <div class="space-y-4 ">
                @forelse($posts as $post)
                    <a href="{{ route('recruitment.show', $post->slug) }}"
                        class="block bg-white dark:bg-slate-800 rounded-xl overflow-hidden border border-gray-100 dark:border-slate-700/50 hover:shadow-lg transition-all duration-300 group">
                        <div class="flex flex-col md:flex-row p-6 md:p-8 gap-6 bg-slate-200/45">
                            <!-- Content Area -->
                            <div class="flex-grow space-y-4">
                                <div class="flex justify-between items-start">
                                    <div class="space-y-1">
                                        <h3
                                            class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white group-hover:text-kumwell-red transition-colors leading-tight">
                                            {{ $post->position_name }}
                                        </h3>
                                        <p class="text-gray-500 dark:text-gray-400 font-medium text-sm">
                                            Kumwell Corporation Public Company Limited
                                        </p>
                                    </div>
                                    <div class="text-right hidden md:block">
                                        <span class="text-gray-600 text-sm font-medium">
                                            {{ $post->published_at ? $post->published_at->translatedFormat('j M y') : '-' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Meta Info Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-3 gap-x-8">
                                    <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                                        <div class="w-5 flex justify-center text-kumwell-red">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </div>
                                        <span
                                            class="text-sm font-medium">{{ $post->location ?: 'สำนักงานใหญ่, กรุงเทพฯ' }}</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                                        <div class="w-5 flex justify-center text-kumwell-red">
                                            <i class="fa-solid fa-bitcoin-sign"></i>
                                        </div>
                                        <span class="text-sm font-medium">
                                            @if($post->salary_min && $post->salary_max)
                                                {{ number_format($post->salary_min) }} - {{ number_format($post->salary_max) }} บาท
                                            @else
                                                ตามตกลง
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                                        <div class="w-5 flex justify-center text-kumwell-red">
                                            <i class="fa-solid fa-desktop"></i>
                                        </div>
                                        <span class="text-sm font-medium">สัมภาษณ์งานออนไลน์</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Logo/Action Area -->
                            <div class="md:w-32 flex flex-row md:flex-col items-center justify-between md:justify-center gap-4">
                                <div
                                    class="w-24 h-24 md:w-28 md:h-28 bg-white border border-gray-100 dark:border-slate-700 rounded-2xl flex items-center justify-center p-4 shadow-sm">
                                    <img src="{{ asset('images/logos/th-kumwell-logo.png') }}" alt="Kumwell Logo"
                                        class="w-full object-contain opacity-80 group-hover:opacity-100 transition-opacity">
                                </div>
                                <div class="md:hidden text-gray-400 text-xs">
                                    {{ $post->published_at ? $post->published_at->translatedFormat('j M y') : '-' }}
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div
                        class="col-span-full py-20 text-center bg-white dark:bg-slate-800 rounded-3xl border border-gray-100 dark:border-slate-700">
                        <div
                            class="w-20 h-20 bg-gray-100 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                            <i class="fa-solid fa-briefcase text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">ไม่พบตำแหน่งงานที่ต้องการ</h3>
                        <p class="text-gray-500">กรุณาลองค้นหาด้วยคำสำคัญอื่น หรือเลือกทุกแผนก</p>
                    </div>
                @endforelse
            </div>

            @if($posts->hasPages())
                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection