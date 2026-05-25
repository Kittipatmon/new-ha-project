@extends('layouts.recruitment.app')

@section('content')
    <style>
        .sarabun-font {
            font-family: 'THSarabunPSK', sans-serif !important;
        }

        /* Increase font size significantly because Sarabun is usually very small on web */
        .sarabun-font .text-base {
            font-size: 1.35rem !important;
            line-height: 1.6;
        }

        .sarabun-font .text-sm {
            font-size: 1.15rem !important;
        }

        .sarabun-font .text-xs {
            font-size: 1rem !important;
        }

        .sarabun-font h1 {
            font-size: 2.5rem !important;
        }

        .sarabun-font h2 {
            font-size: 2rem !important;
        }

        .sarabun-font h3 {
            font-size: 1.75rem !important;
        }
    </style>
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
        <!-- Navigation Breadcrumb -->
        <div class="max-w-5xl mx-auto px-6 py-6 pt-10">
            <nav class="flex text-sm text-gray-500 gap-2 items-center font-medium">
                <a href="{{ route('recruitment.index') }}" class="hover:text-kumwell-red transition-colors">ตำแหน่งงานทั้งหมด</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-gray-900 dark:text-gray-300 truncate">{{ $post->title }}</span>
            </nav>
        </div>

        <div class="max-w-5xl mx-auto px-6">
            <!-- Company Mini Header -->
            <div class="flex items-center gap-6 mb-10">
                <div class="w-24 h-24 bg-white border border-gray-100 dark:border-slate-800 rounded-2xl flex items-center justify-center p-4 shadow-sm">
                    <img src="{{ asset('images/logos/th-kumwell-logo.png') }}" alt="Kumwell Logo" class="w-full object-contain">
                </div>
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Kumwell Corporation Public Company Limited</h2>
                    <a href="https://www.kumwell.com/about-us" class="text-kumwell-red hover:text-kumwell-red-dark text-sm font-bold border-b border-kumwell-red/30 hover:border-kumwell-red-dark pb-0.5 transition-all">ดูรายละเอียดบริษัท</a>
                </div>
            </div>

            <!-- Main Job Header Card -->
            <div class="bg-slate-200/30 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-700/50 rounded-3xl p-8 md:p-12 mb-12 relative overflow-hidden">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative z-10">
                    <div class="space-y-6 flex-grow">
                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white leading-tight">
                                    {{ $post->position_name }}
                                </h1>
                                <span class="text-gray-600 text-sm font-medium whitespace-nowrap">
                                    {{ $post->published_at ? $post->published_at->translatedFormat('j M y') : '-' }}
                                </span>
                            </div>
                            
                            <!-- Meta Info -->
                            <div class="space-y-4 pt-2">
                                <div class="flex items-center gap-4 text-gray-600 dark:text-gray-300">
                                    <div class="w-6 flex justify-center text-kumwell-red">
                                        <i class="fa-solid fa-location-dot text-lg"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider leading-none mb-1">สถานที่ปฏิบัติงาน</span>
                                        <span class="text-sm md:text-base font-bold">{{ $post->location ?: 'สำนักงานใหญ่, กรุงเทพฯ' }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 text-gray-600 dark:text-gray-300">
                                    <div class="w-6 flex justify-center text-kumwell-red">
                                        <i class="fa-solid fa-bitcoin-sign text-lg"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider leading-none mb-1">เงินเดือน</span>
                                        <span class="text-sm md:text-base font-bold">
                                            @if($post->salary_min && $post->salary_max)
                                                {{ number_format($post->salary_min) }} - {{ number_format($post->salary_max) }} บาท
                                            @else
                                                ตามตกลง
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 text-gray-600 dark:text-gray-300">
                                    <div class="w-6 flex justify-center text-kumwell-red">
                                        <i class="fa-solid fa-user-group text-lg"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs text-gray-400 font-bold uppercase tracking-wider leading-none mb-1">อัตรา</span>
                                        <span class="text-sm md:text-base font-bold">{{ $post->vacancy }} อัตรา</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Column -->
                    <div class="flex flex-col items-center md:items-end gap-6 w-full md:w-auto">
                        <a href="{{ route('recruitment.apply', $post->slug) }}" 
                            class="w-full md:w-56 text-center bg-kumwell-red hover:bg-[#e65d3a] text-white font-bold py-4 px-8 rounded-xl shadow-lg shadow-orange-500/20 transition-all active:scale-[0.98]">
                            สมัครงาน
                        </a>
                        
                        <!-- Share/Utility -->
                        <div class="flex items-center gap-6 text-gray-500 dark:text-gray-400">
                            <button class="flex items-center gap-2 hover:text-kumwell-red transition-colors text-sm font-bold">
                                <i class="fa-regular fa-heart"></i> สนใจ
                            </button>
                            <button onclick="window.print()" class="flex items-center gap-2 hover:text-kumwell-red transition-colors text-sm font-bold">
                                <i class="fa-solid fa-print"></i> พิมพ์
                            </button>
                            <button class="flex items-center gap-2 hover:text-kumwell-red transition-colors text-sm font-bold">
                                <i class="fa-solid fa-share-nodes"></i> แชร์
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="space-y-12 max-w-4xl sarabun-font font-size-24 mb-1">
                <!-- Job Description -->
                <div class="space-y-6 mb">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white pb-2 border-b-2 border-slate-100 dark:border-slate-800 inline-block">
                        รายละเอียดงาน
                    </h3>
                    <div class="space-y-2 text-gray-800 dark:text-gray-400 text-base font-medium">
                        {{ $post->job_description }}
                    </div>
                </div>

                <!-- Quals -->
                <div class="space-y-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white pb-2 border-b-2 border-slate-100 dark:border-slate-800 inline-block">
                        คุณสมบัติผู้สมัคร
                    </h3>
                    <div class="space-y-2 text-gray-800 dark:text-gray-400 text-base font-medium">
                        {{ $post->qualification }}
                    </div>
                </div>

                <!-- Benefits -->
                <div class="space-y-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white pb-2 border-b-2 border-slate-100 dark:border-slate-800 inline-block">
                        สวัสดิการ
                    </h3>
                    <div class="space-y-2 text-gray-800 dark:text-gray-400 text-base font-medium">
                        {{ $post->benefits ?: 'ติดต่อสอบถามเพิ่มเติมขณะเรียกสัมภาษณ์' }}
                    </div>
                </div>

                <!-- How to Apply -->
                <div class="space-y-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white pb-2 border-b-2 border-slate-100 dark:border-slate-800 inline-block">
                        วิธีการสมัคร
                    </h3>
                    <div class="space-y-2 text-gray-800 dark:text-gray-400 text-base font-medium">
                        <p>- กดปุ่มสมัครผ่านหน้าเว็บไซต์</p>
                        <p>- ส่งประวัติย่อ (Resume/CV) ทางอีเมล</p>
                        <p>- ติดต่อสอบถามข้อมูลเพิ่มเติมแผนกทรัพยากรมนุษย์</p>
                    </div>
                </div>

                <!-- Contact -->
                <div class="space-y-6 pt-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white pb-2 border-b-2 border-slate-100 dark:border-slate-800 inline-block">
                        ติดต่อ
                    </h3>
                    <div class="text-gray-800 dark:text-gray-400 text-base font-medium space-y-1">
                        <p>แผนกทรัพยากรมนุษย์ (HR)</p>
                        <p>Kumwell Corporation Public Company Limited</p>
                        <p>โทร: 02-xxx-xxxx ต่อ xxx</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
