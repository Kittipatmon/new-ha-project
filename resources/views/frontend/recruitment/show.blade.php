@extends('layouts.recruitment.app')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900 pb-28">
        
        <!-- Breadcrumb & Top Bar -->
        <div class="bg-white dark:bg-slate-800 border-b border-gray-200/80 dark:border-slate-700 py-3.5 px-4 sm:px-8">
            <div class="max-w-7xl mx-auto flex items-center justify-between text-xs md:text-sm text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-2 truncate">
                    <a href="{{ route('recruitment.index') }}" class="hover:text-[#B21F24] transition-colors font-medium">ตำแหน่งงานทั้งหมด</a>
                    <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
                    <span class="text-gray-900 dark:text-white font-semibold truncate">{{ $post->position_name }}</span>
                </div>
                <div class="hidden sm:flex items-center gap-4 text-xs">
                    <span>รหัสประกาศ: #{{ str_pad($post->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-6 space-y-6">
            @php
                $isClosedOrExpired = ($post->publish_status === 'closed') || ($post->end_date && $post->end_date->endOfDay()->isPast());
            @endphp

            @if($isClosedOrExpired)
                <div class="bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/50 rounded-2xl p-4 flex items-center justify-between gap-4 text-red-800 dark:text-red-300 text-xs sm:text-sm shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/50 flex items-center justify-center shrink-0 text-red-600 dark:text-red-400 font-bold">
                            <i class="fa-solid fa-ban"></i>
                        </span>
                        <div>
                            <span class="font-bold text-red-900 dark:text-red-200">ตำแหน่งงานนี้ปิดรับสมัครแล้ว:</span>
                            <span>ประกาศนี้ได้ถูกยกเลิกหรือสิ้นสุดระยะเวลารับสมัครแล้ว จึงไม่สามารถส่งใบสมัครได้</span>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-200/80 text-red-900 dark:bg-red-900/60 dark:text-red-200 shrink-0">
                        Closed
                    </span>
                </div>
            @endif

            <!-- Company Header Card + QR Code Box (Matching Reference Screenshot 3 & 4) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-stretch">
                <!-- Left: Company Profile Card -->
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-gray-200/80 dark:border-slate-700 p-6 flex flex-col sm:flex-row items-center sm:items-start gap-5 shadow-sm">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 bg-white border border-gray-100 dark:border-slate-700 rounded-2xl flex items-center justify-center p-3 shadow-inner shrink-0">
                        <img src="{{ asset('images/logos/th-kumwell-logo.png') }}" alt="Kumwell Logo" class="max-h-full max-w-full object-contain">
                    </div>
                    <div class="space-y-2 text-center sm:text-left flex-grow">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                            Kumwell Corporation Public Company Limited
                        </h2>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                            ประเภทธุรกิจ : นวัตกรรมและผู้นำด้านระบบป้องกันฟ้าผ่า และระบบความปลอดภัยไฟฟ้า (Total Solution)
                        </p>
                        <div class="pt-2">
                            <a href="https://www.kumwell.com" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full border border-red-500 text-[#B21F24] hover:bg-[#B21F24] hover:text-white transition-all text-xs font-semibold">
                                ดูโปรไฟล์บริษัท
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right: QR Code & Views / Status Card (Matching Reference Screenshot 3 & 4) -->
                <div class="bg-sky-50/70 dark:bg-slate-800/90 rounded-2xl border border-sky-100 dark:border-slate-700 p-5 flex items-center gap-5 shadow-sm">
                    <!-- QR Code Placeholder (Standard QR image for mobile sharing) -->
                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-white rounded-xl p-1.5 border border-sky-200 dark:border-slate-600 flex items-center justify-center shrink-0 shadow-sm">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('recruitment.show', $post->slug)) }}" 
                             alt="QR Code" class="w-full h-full object-contain">
                    </div>
                    <div class="space-y-1.5 text-xs sm:text-sm text-gray-700 dark:text-gray-300">
                        <div class="flex items-center gap-1.5">
                            <span class="text-gray-500">จำนวนผู้เข้าชม :</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ number_format($post->views ?? 0) }} คน</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-gray-500">สมัครงานตำแหน่งนี้ :</span>
                            <span class="font-bold text-[#B21F24]">{{ number_format($applicationCount ?? ($post->applications_count ?? $post->applications()->count())) }} คน</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-gray-500">วันที่อัปเดต :</span>
                            <span class="font-medium text-gray-700 dark:text-gray-300">
                                {{ $post->updated_at ? $post->updated_at->locale('th')->translatedFormat('d M ') . ($post->updated_at->year + 543) : ($post->published_at ? $post->published_at->locale('th')->translatedFormat('d M ') . ($post->published_at->year + 543) : 'วันนี้') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Job Title Action Bar (Matching Reference Screenshot 3 & 4) -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200/80 dark:border-slate-700 p-5 md:p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
                <div class="space-y-2.5">
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white leading-tight">
                        {{ $post->position_name }}
                    </h1>
                    <div class="flex items-center gap-2.5 flex-wrap pt-0.5">
                        @if(($post->urgency ?? 'urgent') === 'very_urgent')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold border border-amber-500/40 text-amber-800 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/40 shadow-xs">
                                <span>⚡</span> รับสมัครด่วนมาก
                            </span>
                        @elseif(($post->urgency ?? 'urgent') === 'urgent')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold border border-red-500/40 text-[#B21F24] bg-red-50 dark:bg-red-950/40">
                                <span>🔥</span> รับสมัครด่วน
                            </span>
                        @endif
                        <span class="inline-flex items-center text-xs text-gray-500 dark:text-gray-400 bg-gray-100/80 dark:bg-slate-700/60 px-2.5 py-1 rounded-lg">
                            <i class="fa-solid fa-building text-gray-400 mr-1.5"></i>
                            {{ $post->department->department_fullname ?? 'ทั่วไป' }}
                        </span>
                    </div>
                </div>

                <!-- Right Apply + Utilities (Responsive: Full-width CTA + 4-button grid on mobile, inline row on desktop) -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                    @if($isClosedOrExpired)
                        <button type="button" onclick="showClosedAlert()"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-400 text-white font-bold px-7 py-3 sm:py-2.5 rounded-xl sm:rounded-full shadow-sm text-sm cursor-not-allowed" title="ตำแหน่งงานนี้ปิดรับสมัครแล้ว">
                            <i class="fa-solid fa-ban text-xs"></i>
                            <span>ปิดรับสมัครแล้ว</span>
                        </button>
                    @else
                        <a href="{{ route('recruitment.apply', $post->slug) }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#B21F24] to-red-700 hover:from-[#96181c] hover:to-red-800 text-white font-bold px-8 py-3 sm:py-2.5 rounded-xl sm:rounded-full shadow-md hover:shadow-lg transition-all active:scale-[0.98] text-sm text-center">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                            <span>สมัครงาน</span>
                        </a>
                    @endif

                    <!-- Action icon buttons (Bookmark, Print, Share, Contact) -->
                    <div class="grid grid-cols-4 sm:flex items-center gap-2 sm:gap-2">
                        <button type="button" onclick="alert('บันทึกตำแหน่งงานเรียบร้อยแล้ว')" 
                            class="h-11 sm:h-10 sm:w-10 rounded-xl sm:rounded-full border border-gray-200 dark:border-slate-700 bg-gray-50/80 dark:bg-slate-700/60 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:text-[#B21F24] hover:border-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-all shadow-2xs" title="บันทึกตำแหน่งนี้">
                            <i class="fa-regular fa-bookmark text-sm"></i>
                        </button>
                        <button type="button" onclick="window.print()" 
                            class="h-11 sm:h-10 sm:w-10 rounded-xl sm:rounded-full border border-gray-200 dark:border-slate-700 bg-gray-50/80 dark:bg-slate-700/60 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:text-[#B21F24] hover:border-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-all shadow-2xs" title="พิมพ์">
                            <i class="fa-solid fa-print text-sm"></i>
                        </button>
                        <button type="button" onclick="navigator.clipboard.writeText(window.location.href); alert('คัดลอกลิงก์เรียบร้อยแล้ว');" 
                            class="h-11 sm:h-10 sm:w-10 rounded-xl sm:rounded-full border border-gray-200 dark:border-slate-700 bg-gray-50/80 dark:bg-slate-700/60 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:text-[#B21F24] hover:border-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-all shadow-2xs" title="แชร์">
                            <i class="fa-solid fa-share-nodes text-sm"></i>
                        </button>
                        <a href="#how-to-apply" 
                            class="h-11 sm:h-10 sm:w-10 rounded-xl sm:rounded-full border border-gray-200 dark:border-slate-700 bg-gray-50/80 dark:bg-slate-700/60 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:text-[#B21F24] hover:border-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-all shadow-2xs" title="สอบถามข้อมูล">
                            <i class="fa-regular fa-comment-dots text-sm"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Two-Column Main Layout: Left Details + Right Sidebar (Matching Reference Screenshot 3, 4 & 5) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                <!-- Left Column: Structured Job Specs Table + Detailed Sections + Application Methods + Tags -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Job Metadata Specifications Table (Matching Reference Screenshot 3 & 4) -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200/80 dark:border-slate-700 p-6 shadow-sm">
                        @php
                            $branchDetails = [
                                'สำนักงานใหญ่' => [
                                    'title' => 'สำนักงานใหญ่ (Head Office)',
                                    'address' => '358 ถนนเลี่ยงเมืองนนทบุรี ตำบลบางกระสอ เมืองนนทบุรี จังหวัดนนทบุรี 11000',
                                    'tel' => '(662) 954-3455',
                                    'fax' => '(662) 591-7891',
                                    'email' => 'info@kumwell.com',
                                    'web' => 'www.kumwell.com',
                                    'map_query' => 'Kumwell Corporation 358 ถนนเลี่ยงเมืองนนทบุรี ตำบลบางกระสอ เมืองนนทบุรี จังหวัดนนทบุรี 11000',
                                    'map_label' => 'Kumwell Head Office (สำนักงานใหญ่)',
                                    'map_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3873.6428914205912!2d100.49296487854991!3d13.860461735847247!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30e29c9301057693%3A0xf89f8553e77a61eb!2z4Lia4Lij4Li04Lip4Lix4LiXIOC4hOC4seC4oeC5gOC4p-C4pSDguITguK3guKPguYzguJvguK3guYDguKPguIrguLHguYjguJkg4LiI4Liz4LiB4Lix4LiUICjguKHguKvguLLguIrguJkp!5e0!3m2!1sth!2sth!4v1789021087327!5m2!1sth!2sth',
                                ],
                                'Factory สาขาบางเลน' => [
                                    'title' => 'Factory สาขาบางเลน',
                                    'address' => '26/2 หมู่ที่ 10 ตำบลบางเลน อำเภอบางใหญ่ จังหวัดนนทบุรี 11140',
                                    'tel' => '(662) 920-0133',
                                    'fax' => '(662) 920-0045',
                                    'email' => null,
                                    'web' => null,
                                    'map_query' => '26/2 หมู่ที่ 10 ตำบลบางเลน อำเภอบางใหญ่ จังหวัดนนทบุรี 11140',
                                    'map_label' => 'Kumwell Factory (สาขาบางเลน)',
                                    'map_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3873.963770251112!2d100.43460309999999!3d13.841213300000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30e29079e4f87b5b%3A0x77bfae4b8cc39990!2zS3Vtd2VsbCBDb3Jwb3JhdGlvbiBQTEMuIChGYWN0b3J5IOC4quC4suC4guC4suC4muC4suC4h-C5gOC4peC4mSk!5e0!3m2!1sth!2sth!4v1789021247126!5m2!1sth!2sth',
                                ],
                                'Factory สาขาไทรน้อย' => [
                                    'title' => 'Factory สาขาไทรน้อย',
                                    'address' => '27 หมู่ที่ 1 ตำบลไทรใหญ่ อำเภอไทรน้อย จังหวัดนนทบุรี 11150',
                                    'tel' => '(662) 117-2483-5',
                                    'fax' => '(662) 117-2486',
                                    'email' => null,
                                    'web' => null,
                                    'map_query' => '27 หมู่ที่ 1 ตำบลไทรใหญ่ อำเภอไทรน้อย จังหวัดนนทบุรี 11150',
                                    'map_label' => 'Kumwell Factory (สาขาไทรน้อย)',
                                    'map_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3870.265763731808!2d100.3008254!3d14.0614776!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30e28bc332328c05%3A0x135f170a932df523!2z4Lia4Lij4Li04Lip4Lix4LiXIOC4hOC4seC4oeC5gOC4p-C4pSDguITguK3guKPguYzguJvguK3guYDguKPguIrguLHguYjguJkg4LiI4Liz4LiB4Lix4LiU!5e0!3m2!1sen!2sth!4v1789021324076!5m2!1sen!2sth',
                                ],
                            ];

                            $matchedBranch = null;
                            $locStr = $post->location ?? '';
                            foreach ($branchDetails as $bKey => $bData) {
                                if ($locStr === $bKey || str_contains($locStr, $bKey) || ($bKey === 'สำนักงานใหญ่' && (empty($locStr) || str_contains($locStr, 'สนง.ใหญ่')))) {
                                    $matchedBranch = $bData;
                                    break;
                                }
                            }
                            // Default fallback to สำนักงานใหญ่ if not matched
                            if (!$matchedBranch) {
                                $matchedBranch = $branchDetails['สำนักงานใหญ่'];
                            }
                        @endphp
                        <div class="space-y-3.5 text-xs sm:text-sm">
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4 pb-2 border-b border-gray-100 dark:border-slate-700/50">
                                <span class="sm:col-span-4 font-bold text-gray-700 dark:text-gray-300">สถานที่ปฏิบัติงาน :</span>
                                <div class="sm:col-span-8 text-gray-800 dark:text-gray-200">
                                    @if($matchedBranch)
                                        <div class="space-y-1">
                                            <div class="font-bold text-[#B21F24] dark:text-red-400 flex items-center gap-1.5">
                                                <i class="fa-solid fa-building"></i>
                                                <span>{{ $matchedBranch['title'] }}</span>
                                            </div>
                                            <div class="text-xs text-gray-600 dark:text-gray-300">
                                                <i class="fa-solid fa-location-dot text-gray-400 mr-1"></i>{{ $matchedBranch['address'] }}
                                            </div>
                                            <div class="flex flex-wrap gap-x-3 gap-y-1 text-[11px] text-gray-500 dark:text-gray-400 pt-0.5">
                                                @if(!empty($matchedBranch['tel']))
                                                    <span><i class="fa-solid fa-phone mr-1 text-[#B21F24]/70"></i>{{ $matchedBranch['tel'] }}</span>
                                                @endif
                                                @if(!empty($matchedBranch['fax']))
                                                    <span><i class="fa-solid fa-fax mr-1 text-[#B21F24]/70"></i>{{ $matchedBranch['fax'] }}</span>
                                                @endif
                                                @if(!empty($matchedBranch['email']))
                                                    <span><i class="fa-solid fa-envelope mr-1 text-[#B21F24]/70"></i>{{ $matchedBranch['email'] }}</span>
                                                @endif
                                                @if(!empty($matchedBranch['web']))
                                                    <span><i class="fa-solid fa-globe mr-1 text-[#B21F24]/70"></i>{{ $matchedBranch['web'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span>{{ $post->location ?: 'สำนักงานใหญ่ (นนทบุรี / กรุงเทพฯ)' }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4 pb-2 border-b border-gray-100 dark:border-slate-700/50">
                                <span class="sm:col-span-4 font-bold text-gray-700 dark:text-gray-300">เงินเดือน (บาท) :</span>
                                <span class="sm:col-span-8 font-semibold text-gray-800 dark:text-gray-200">
                                    @if($post->salary_min && $post->salary_max)
                                        {{ number_format($post->salary_min) }} - {{ number_format($post->salary_max) }}
                                    @elseif($post->salary_min)
                                        เริ่มต้น {{ number_format($post->salary_min) }}
                                    @else
                                        ตามตกลง / โครงสร้างบริษัท
                                    @endif
                                    @if($post->salary_note)
                                        <span class="text-xs text-gray-400 font-normal">({{ $post->salary_note }})</span>
                                    @endif
                                </span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4 pb-2 border-b border-gray-100 dark:border-slate-700/50">
                                <span class="sm:col-span-4 font-bold text-gray-700 dark:text-gray-300">สาขาอาชีพหลัก :</span>
                                <span class="sm:col-span-8 text-gray-800 dark:text-gray-200">{{ $post->department->department_fullname ?? 'ทรัพยากรมนุษย์/วิศวกรรม' }}</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4 pb-2 border-b border-gray-100 dark:border-slate-700/50">
                                <span class="sm:col-span-4 font-bold text-gray-700 dark:text-gray-300">สาขาอาชีพรอง :</span>
                                <span class="sm:col-span-8 text-gray-800 dark:text-gray-200">{{ $post->department->department_name ?? '-' }}</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4 pb-2 border-b border-gray-100 dark:border-slate-700/50">
                                <span class="sm:col-span-4 font-bold text-gray-700 dark:text-gray-300">รูปแบบงาน :</span>
                                <span class="sm:col-span-8 text-gray-800 dark:text-gray-200">{{ $post->employment_type ?: 'งานประจำ (Full-time)' }}</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4 pb-2 border-b border-gray-100 dark:border-slate-700/50">
                                <span class="sm:col-span-4 font-bold text-gray-700 dark:text-gray-300">จำนวนที่รับ :</span>
                                <span class="sm:col-span-8 text-gray-800 dark:text-gray-200">{{ $post->vacancy ? $post->vacancy . ' ตำแหน่ง' : '1 ตำแหน่ง' }}</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4 pb-2 border-b border-gray-100 dark:border-slate-700/50">
                                <span class="sm:col-span-4 font-bold text-gray-700 dark:text-gray-300">วันทำงาน :</span>
                                <span class="sm:col-span-8 text-gray-800 dark:text-gray-200">{{ $post->work_schedule ?: 'ตามที่บริษัทกำหนด (จันทร์ - ศุกร์)' }}</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4 pb-2 border-b border-gray-100 dark:border-slate-700/50">
                                <span class="sm:col-span-4 font-bold text-gray-700 dark:text-gray-300">เวลาทำงาน :</span>
                                <span class="sm:col-span-8 text-gray-800 dark:text-gray-200">08:00 - 17:00</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4">
                                <span class="sm:col-span-4 font-bold text-gray-700 dark:text-gray-300">ระดับตำแหน่งงาน :</span>
                                <span class="sm:col-span-8 text-gray-800 dark:text-gray-200">เจ้าหน้าที่ / ปฏิบัติการ</span>
                            </div>
                        </div>
                    </div>

                    <!-- Job Description & Tasks -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200/80 dark:border-slate-700 p-6 shadow-sm space-y-4">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">
                            หน้าที่และความรับผิดชอบ
                        </h3>
                        <div class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed space-y-2 whitespace-pre-line pl-1">
                            {!! nl2br(e($post->job_description)) !!}
                        </div>
                    </div>

                    <!-- Qualifications -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200/80 dark:border-slate-700 p-6 shadow-sm space-y-4">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">
                            คุณสมบัติ
                        </h3>
                        <div class="space-y-2.5 text-xs sm:text-sm">
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4 pb-2 border-b border-gray-100 dark:border-slate-700/50">
                                <span class="sm:col-span-3 font-bold text-gray-700 dark:text-gray-300">เพศ :</span>
                                <span class="sm:col-span-9 text-gray-800 dark:text-gray-200">ชาย / หญิง / ไม่จำกัดเพศ</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4 pb-2 border-b border-gray-100 dark:border-slate-700/50">
                                <span class="sm:col-span-3 font-bold text-gray-700 dark:text-gray-300">อายุ(ปี) :</span>
                                <span class="sm:col-span-9 text-gray-800 dark:text-gray-200">ไม่จำกัด</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4 pb-2 border-b border-gray-100 dark:border-slate-700/50">
                                <span class="sm:col-span-3 font-bold text-gray-700 dark:text-gray-300">ระดับการศึกษา :</span>
                                <span class="sm:col-span-9 text-gray-800 dark:text-gray-200">ปวส. - ปริญญาตรีขึ้นไป หรือสาขาที่เกี่ยวข้อง</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4">
                                <span class="sm:col-span-3 font-bold text-gray-700 dark:text-gray-300">ประสบการณ์(ปี) :</span>
                                <span class="sm:col-span-9 text-gray-800 dark:text-gray-200">0 ปีขึ้นไป (ยินดีรับนักศึกษาจบใหม่)</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 dark:border-slate-700/50 space-y-2">
                            <h4 class="text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-200">คุณสมบัติด้านความรู้และความสามารถ</h4>
                            <div class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line pl-1">
                                {!! nl2br(e($post->qualification)) !!}
                            </div>
                        </div>

                        @if($post->benefits)
                            <div class="pt-4 border-t border-gray-100 dark:border-slate-700/50 space-y-2">
                                <h4 class="text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-200">สวัสดิการ</h4>
                                <div class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line pl-1">
                                    {!! nl2br(e($post->benefits)) !!}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Map Section with Tabs (Matching Reference Screenshot 5) -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200/80 dark:border-slate-700 p-6 shadow-sm space-y-4"
                        x-data="{ mapTab: 'online' }">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">แผนที่</h3>
                            <div class="flex items-center gap-1 bg-gray-100 dark:bg-slate-700 p-1 rounded-lg text-xs">
                                <button type="button" @click="mapTab = 'online'"
                                    :class="mapTab === 'online' ? 'bg-white dark:bg-slate-800 text-gray-900 dark:text-white font-bold shadow-xs' : 'text-gray-600 dark:text-gray-300'"
                                    class="px-3 py-1 rounded-md transition-all">
                                    แผนที่ออนไลน์
                                </button>
                                <button type="button" @click="mapTab = 'image'"
                                    :class="mapTab === 'image' ? 'bg-white dark:bg-slate-800 text-gray-900 dark:text-white font-bold shadow-xs' : 'text-gray-600 dark:text-gray-300'"
                                    class="px-3 py-1 rounded-md transition-all">
                                    แผนที่แบบภาพ
                                </button>
                            </div>
                        </div>

                        <p class="text-xs text-gray-500">การเดินทางเพิ่มเติม : ห่างจากสถานีรถไฟฟ้า/จุดศูนย์กลางประมาณ 1-2 กิโลเมตร สะดวกต่อการเดินทาง</p>

                        <!-- Online Google Map Tab (Embedded Real Map) -->
                        <div x-show="mapTab === 'online'" class="space-y-3 transition-all">
                            <div class="w-full h-[360px] sm:h-[420px] rounded-2xl overflow-hidden border border-gray-200 dark:border-slate-700 shadow-inner bg-gray-100 dark:bg-slate-900">
                                <iframe 
                                    src="{{ $matchedBranch['map_embed'] ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3873.6428914205912!2d100.49296487854991!3d13.860461735847247!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30e29c9301057693%3A0xf89f8553e77a61eb!2z4Lia4Lij4Li04Lip4Lix4LiXIOC4hOC4seC4oeC5gOC4p-C4pSDguITguK3guKPguYzguJvguK3guYDguKPguIrguLHguYjguJkg4LiI4Liz4LiB4Lix4LiUICjguKHguKvguLLguIrguJkp!5e0!3m2!1sth!2sth!4v1789021087327!5m2!1sth!2sth' }}" 
                                    class="w-full h-full border-0" 
                                    allowfullscreen="" 
                                    loading="lazy" 
                                    referrerpolicy="strict-origin-when-cross-origin">
                                </iframe>
                            </div>
                            <div class="flex items-center justify-between flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400 px-1">
                                <div class="flex items-center gap-1.5 font-medium text-gray-700 dark:text-gray-200">
                                    <i class="fa-solid fa-location-dot text-[#B21F24]"></i>
                                    <span>{{ $matchedBranch['title'] ?? 'บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)' }}</span>
                                </div>
                                <a href="https://maps.google.com/?q={{ urlencode($matchedBranch['map_query'] ?? 'Kumwell Corporation 358 ถนนเลี่ยงเมืองนนทบุรี ตำบลบางกระสอ เมืองนนทบุรี จังหวัดนนทบุรี 11000') }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1 text-[#B21F24] hover:underline font-semibold">
                                    <span>เปิดใน Google Maps แอปพลิเคชัน</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Image Map Tab -->
                        <div x-show="mapTab === 'image'" class="space-y-3 transition-all" style="display: none;">
                            <div class="relative rounded-2xl overflow-hidden bg-gradient-to-r from-red-500/90 to-rose-600/90 p-8 sm:p-12 text-center text-white shadow-inner flex flex-col items-center justify-center min-h-[220px]">
                                <div class="absolute inset-0 bg-black/10"></div>
                                <div class="relative z-10 space-y-3">
                                    <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md px-4 py-1.5 rounded-full text-xs font-semibold">
                                        <i class="fa-solid fa-building text-white"></i>
                                        <span>{{ $matchedBranch ? $matchedBranch['title'] : 'Kumwell Head Office' }}</span>
                                    </div>
                                    <p class="text-sm sm:text-base font-medium max-w-xl mx-auto drop-shadow-sm">
                                        {{ $matchedBranch['address'] ?? '358 ถนนเลี่ยงเมืองนนทบุรี ตำบลบางกระสอ อำเภอเมืองนนทบุรี นนทบุรี 11000' }}
                                    </p>
                                    <a href="https://maps.google.com/?q={{ urlencode($matchedBranch['map_query'] ?? ($post->location ?: 'Kumwell Corporation')) }}" target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center gap-2 bg-white text-gray-800 hover:bg-gray-100 font-bold px-5 py-2.5 rounded-full shadow transition-all active:scale-[0.98] text-xs">
                                        <i class="fa-solid fa-location-crosshairs text-[#B21F24]"></i>
                                        <span>คลิกเพื่อดูเส้นทางและพิกัด</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Application Methods (4 Red Buttons Matching Reference Screenshot 5) -->
                    <div id="how-to-apply" class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200/80 dark:border-slate-700 p-6 shadow-sm space-y-5">
                        <div class="space-y-1">
                            <h3 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">
                                เลือกวิธีการสมัครงาน
                            </h3>
                            <p class="text-xs text-gray-500">
                                รับสมัครงานผ่านระบบสรรหาออนไลน์ Kumwell Recruitment System
                            </p>
                            <p class="text-xs text-[#B21F24] font-semibold">
                                • hr_recruitment@kumwell.com
                            </p>
                        </div>

                        <div class="space-y-3.5 pt-2">
                            <!-- Button 1: สมัครงานด่วน -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                @if($isClosedOrExpired)
                                    <button type="button" onclick="showClosedAlert()"
                                        class="w-full sm:w-52 text-center bg-[#B21F24] hover:bg-[#8e181c] text-white font-bold py-2.5 px-4 rounded-full shadow-md text-xs sm:text-sm shrink-0 transition-all active:scale-[0.98] cursor-pointer">
                                        สมัครงานด่วน
                                    </button>
                                @else
                                    <a href="{{ route('recruitment.apply', $post->slug) }}"
                                        class="w-full sm:w-52 text-center bg-[#B21F24] hover:bg-[#8e181c] text-white font-bold py-2.5 px-4 rounded-full shadow-md text-xs sm:text-sm shrink-0 transition-all active:scale-[0.98]">
                                        สมัครงานด่วน
                                    </a>
                                @endif
                                <span class="text-xs text-gray-600 dark:text-gray-300">
                                    สมัครด้วยเรซูเม่และโปรไฟล์จากระบบ
                                </span>
                            </div>

                            <!-- Button 2: สร้างเรซูเม่ย่อ -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                @if($isClosedOrExpired)
                                    <button type="button" onclick="showClosedAlert()"
                                        class="w-full sm:w-52 text-center bg-[#B21F24] hover:bg-[#8e181c] text-white font-bold py-2.5 px-4 rounded-full shadow-md text-xs sm:text-sm shrink-0 transition-all active:scale-[0.98] cursor-pointer">
                                        สร้างเรซูเม่ย่อ
                                    </button>
                                @else
                                    <a href="{{ route('recruitment.apply', $post->slug) }}"
                                        class="w-full sm:w-52 text-center bg-[#B21F24] hover:bg-[#8e181c] text-white font-bold py-2.5 px-4 rounded-full shadow-md text-xs sm:text-sm shrink-0 transition-all active:scale-[0.98]">
                                        สร้างเรซูเม่ย่อ
                                    </a>
                                @endif
                                <span class="text-xs text-gray-600 dark:text-gray-300">
                                    สมัครง่ายและรวดเร็ว ด้วยการกรอกข้อมูลประวัติย่อ
                                </span>
                            </div>

                            <!-- Button 3: แนบไฟล์สมัครงาน -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                @if($isClosedOrExpired)
                                    <button type="button" onclick="showClosedAlert()"
                                        class="w-full sm:w-52 text-center bg-[#B21F24] hover:bg-[#8e181c] text-white font-bold py-2.5 px-4 rounded-full shadow-md text-xs sm:text-sm shrink-0 transition-all active:scale-[0.98] cursor-pointer">
                                        แนบไฟล์สมัครงาน
                                    </button>
                                @else
                                    <a href="{{ route('recruitment.apply', $post->slug) }}"
                                        class="w-full sm:w-52 text-center bg-[#B21F24] hover:bg-[#8e181c] text-white font-bold py-2.5 px-4 rounded-full shadow-md text-xs sm:text-sm shrink-0 transition-all active:scale-[0.98]">
                                        แนบไฟล์สมัครงาน
                                    </a>
                                @endif
                                <span class="text-xs text-gray-600 dark:text-gray-300">
                                    สมัครด้วยการแนบไฟล์เรซูเม่ (PDF, Word) หรือผลงานของคุณ
                                </span>
                            </div>

                            <!-- Button 4: สมัครงานผ่านอีเมล -->
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                <a href="mailto:hr_recruitment@kumwell.com?subject=สมัครงานตำแหน่ง {{ rawurlencode($post->position_name) }}"
                                    class="w-full sm:w-52 text-center bg-[#B21F24] hover:bg-[#8e181c] text-white font-bold py-2.5 px-4 rounded-full shadow-md text-xs sm:text-sm shrink-0 transition-all active:scale-[0.98]">
                                    สมัครงานผ่านอีเมล
                                </a>
                                <div class="text-xs text-gray-600 dark:text-gray-300">
                                    <span>สมัครผ่านอีเมลฝ่ายทรัพยากรบุคคล (แนบไฟล์ได้ไม่เกิน 10 MB)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tags Footer (Matching Reference Screenshot 5) -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200/80 dark:border-slate-700 p-5 shadow-sm space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-gray-700 dark:text-gray-300">
                            <span>Tags :</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $tags = [
                                    'หางาน',
                                    $post->department->department_fullname ?? 'วิศวกรรม/ไอที',
                                    $post->position_name,
                                    'หางานใกล้ฉัน',
                                    'งานประจำ',
                                    'สำนักงานใหญ่',
                                    'กรุงเทพฯและปริมณฑล',
                                    'สมัครงานด่วน',
                                    'Kumwell'
                                ];
                            @endphp
                            @foreach($tags as $tag)
                                <a href="{{ route('recruitment.index', ['search' => $tag]) }}"
                                    class="px-3.5 py-1 rounded-full border border-gray-200 dark:border-slate-700 text-gray-600 dark:text-gray-300 hover:border-red-400 hover:text-[#B21F24] text-xs transition-colors bg-gray-50/50 dark:bg-slate-900/50">
                                    {{ $tag }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                </div>

                <!-- Right Column: Other Jobs from Company (Matching Reference Screenshot 3 & 4) -->
                <div class="space-y-4">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200/80 dark:border-slate-700 p-5 shadow-sm space-y-4">
                        <h3 class="text-sm md:text-base font-bold text-gray-900 dark:text-white pb-2 border-b border-gray-100 dark:border-slate-700/60">
                            ตำแหน่งงานอื่นๆ ของบริษัทนี้
                        </h3>

                        <div class="space-y-3">
                            @forelse($otherPosts as $other)
                                <a href="{{ route('recruitment.show', ['slug' => $other->slug, 'from_card' => 1]) }}"
                                    class="block p-3.5 rounded-xl border border-gray-200/70 dark:border-slate-700/70 hover:border-red-400 hover:shadow-md transition-all group">
                                    <div class="flex items-center justify-between gap-2 mb-2">
                                        <div class="w-10 h-10 bg-white border border-gray-100 rounded-lg p-1.5 flex items-center justify-center shrink-0">
                                            <img src="{{ asset('images/logos/th-kumwell-logo.png') }}" alt="Kumwell" class="max-h-full max-w-full object-contain">
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[11px] text-gray-400">
                                                <i class="fa-regular fa-clock mr-0.5"></i>
                                                {{ $other->published_at ? $other->published_at->diffForHumans() : 'เมื่อสักครู่' }}
                                            </span>
                                            <div>
                                                @if(($other->urgency ?? 'urgent') === 'very_urgent')
                                                    <span class="inline-block text-[10px] text-amber-700 dark:text-amber-400 font-bold border border-amber-400/50 bg-amber-50 dark:bg-amber-950/30 rounded px-1.5 py-0.2">
                                                        ⚡ ด่วนมาก
                                                    </span>
                                                @elseif(($other->urgency ?? 'urgent') === 'urgent')
                                                    <span class="inline-block text-[10px] text-[#B21F24] font-semibold border border-red-400/40 bg-red-50 dark:bg-red-950/30 rounded px-1.5 py-0.2">
                                                        🔥 รับสมัครด่วน
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <h4 class="text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-100 group-hover:text-[#B21F24] transition-colors line-clamp-2">
                                        {{ $other->position_name }}
                                    </h4>
                                    
                                    <div class="mt-2 space-y-0.5 text-[11px] text-gray-500 dark:text-gray-400">
                                        <p class="truncate">สถานที่ปฏิบัติงาน : {{ $other->location ?: 'สำนักงานใหญ่' }}</p>
                                        <p class="font-medium text-gray-700 dark:text-gray-300">
                                            เงินเดือน(บาท) : 
                                            @if($other->salary_min && $other->salary_max)
                                                {{ number_format($other->salary_min) }} - {{ number_format($other->salary_max) }}
                                            @else
                                                ตามตกลง
                                            @endif
                                        </p>
                                    </div>
                                </a>
                            @empty
                                <p class="text-xs text-gray-400 text-center py-4">ไม่มีตำแหน่งงานอื่นๆ ในขณะนี้</p>
                            @endforelse
                        </div>

                        <!-- Red Outline Pill Button: ดูตำแหน่งงานทั้งหมดของบริษัทนี้ -->
                        <div class="pt-2">
                            <a href="{{ route('recruitment.index') }}"
                                class="block w-full text-center py-2.5 px-4 rounded-full border border-red-500 text-[#B21F24] hover:bg-[#B21F24] hover:text-white font-bold text-xs transition-all">
                                ดูตำแหน่งงานทั้งหมดของบริษัทนี้
                            </a>
                        </div>
                    </div>
                </div>

            </div>

    </div>

    @push('scripts')
    <script>
        function showClosedAlert() {
            if (window.Swal) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ตำแหน่งงานนี้ปิดรับสมัครแล้ว',
                    text: 'ขออภัย ตำแหน่งงานนี้ได้ถูกยกเลิกหรือปิดรับสมัครเรียบร้อยแล้ว จึงไม่สามารถส่งใบสมัครได้',
                    confirmButtonText: 'รับทราบ',
                    confirmButtonColor: '#B21F24',
                    customClass: {
                        popup: 'rounded-2xl font-sans'
                    }
                });
            } else {
                alert('ขออภัย ตำแหน่งงานนี้ได้ถูกยกเลิกหรือปิดรับสมัครเรียบร้อยแล้ว จึงไม่สามารถส่งใบสมัครได้');
            }
        }

        @if(session('closed_alert') || session('error'))
            document.addEventListener('DOMContentLoaded', function() {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'ตำแหน่งงานนี้ปิดรับสมัครแล้ว',
                        text: "{{ session('closed_alert') ?? session('error') }}",
                        confirmButtonText: 'รับทราบ',
                        confirmButtonColor: '#B21F24',
                        customClass: {
                            popup: 'rounded-2xl font-sans'
                        }
                    });
                } else {
                    alert("{{ session('closed_alert') ?? session('error') }}");
                }
            });
        @endif

        // Clean URL to remove temporary tracking parameters like 'from_card'
        // This guarantees that any subsequent page refresh (F5 / Reload) will NOT contain 'from_card' and will NOT be counted.
        if (window.history.replaceState && window.location.search) {
            const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
        }
    </script>
    @endpush
@endsection
