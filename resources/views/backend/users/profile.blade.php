@extends('layouts.app')

@section('content')
    @include('layouts.navigation')
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 pb-20 pt-16">
    <!-- Premium Hero Section -->
    <div id="hero-section" class="relative h-48 md:h-64 overflow-hidden bg-[#8B1A1E]">
        <!-- Background with high-end corporate gradient -->
        <div class="absolute inset-0 bg-gradient-to-r from-[#B21F24] to-[#8B1A1E]"></div>
        
        <!-- Clean geometric pattern with low opacity -->
        <div class="absolute inset-0 opacity-5 bg-[linear-gradient(to_right,#fff_1px,transparent_1px),linear-gradient(to_bottom,#fff_1px,transparent_1px)] bg-[size:24px_24px]"></div>
    </div>

    <!-- Main Profile Container -->
    <div class="max-w-6xl mx-auto px-6 -mt-20 md:-mt-24 relative z-10">
        <!-- Header Card -->
        <div class="bg-white dark:bg-slate-800 rounded-xl p-6 md:p-8 shadow-sm border border-slate-100 dark:border-slate-700/50 mb-8">
            <div class="flex flex-col md:flex-row items-center md:items-end gap-6 md:gap-8">
                <!-- Avatar Section -->
                <div class="relative group">
                    <div id="avatar-container" class="w-32 h-32 md:w-40 md:h-40 rounded-xl overflow-hidden ring-4 ring-white dark:ring-slate-800 shadow-md bg-white dark:bg-slate-700 transition-transform duration-350 hover:scale-[1.02]">
                        @php $avatar = $user->photo_user; @endphp
                        @if($avatar)
                            <img id="profile-image" src="{{ asset($avatar) }}" alt="User Avatar" class="w-full h-full object-cover" loading="lazy">
                        @else
                            <div id="profile-placeholder" class="w-full h-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-400 dark:text-slate-500">
                                <i class="fa-solid fa-user text-5xl"></i>
                            </div>
                        @endif
                        
                        <!-- Upload Overlay -->
                        <div id="upload-overlay" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                            <i class="fa-solid fa-cloud-arrow-up text-white text-2xl"></i>
                        </div>
                    </div>
                    
                    @php $inputId = 'avatarUpload-'.($user->id ?? 'me'); @endphp
                    <label for="{{ $inputId }}" class="absolute bottom-2 right-2 bg-white dark:bg-slate-700 w-10 h-10 rounded-lg shadow-md flex items-center justify-center cursor-pointer hover:bg-[#B21F24] hover:text-white dark:hover:bg-[#B21F24] transition-colors z-20 border border-slate-100 dark:border-slate-600">
                        <i class="fa-solid fa-camera text-sm"></i>
                        <input id="{{ $inputId }}" type="file" accept="image/*" class="hidden" onchange="uploadAvatar(this)">
                    </label>
                </div>

                <!-- User Identity -->
                <div class="flex-1 flex flex-col items-center md:items-start space-y-3 text-center md:text-left">
                    <div class="space-y-1">
                        <span class="inline-block px-3 py-1 rounded bg-[#B21F24]/10 text-[#B21F24] dark:text-red-400 text-[10px] font-bold uppercase tracking-wider border border-[#B21F24]/20">
                            {{ $user->usertype->description ?? 'Employee' }}
                        </span>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            {{ $user->first_name }} {{ $user->last_name }}
                        </h1>
                    </div>
                    
                    <div class="flex flex-wrap justify-center md:justify-start gap-3 text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 dark:bg-slate-700/40 rounded-lg border border-slate-100 dark:border-slate-700/50">
                            <i class="fa-solid fa-id-badge text-[#B21F24]"></i>
                            <span>{{ $user->employee_code }}</span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 dark:bg-slate-700/40 rounded-lg border border-slate-100 dark:border-slate-700/50">
                            <i class="fa-solid fa-briefcase text-[#B21F24]"></i>
                            <span>{{ $user->position ?: 'N/A' }}</span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-green-500/10 text-green-600 dark:text-green-400 border border-green-500/20 rounded-lg">
                            <i class="fa-solid fa-circle text-[6px] animate-pulse"></i>
                            <span>Active</span>
                        </div>
                    </div>

                    <!-- Mobile Action Button -->
                    <div class="lg:hidden w-full pt-2">
                        <a href="#" class="w-full text-center bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 px-6 py-3 rounded-lg font-semibold text-xs uppercase tracking-wider transition-colors shadow-sm inline-block">
                            Edit Profile
                        </a>
                    </div>
                </div>

                <!-- Primary Action (Optional) -->
                <div class="hidden lg:block pb-2">
                    <a href="#" class="bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 px-6 py-3 rounded-lg font-semibold text-xs uppercase tracking-wider transition-colors shadow-sm inline-block">
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">
            <!-- Sidebar Info (Stats) -->
            <div class="lg:col-span-4 space-y-6 md:space-y-8">
                <!-- Work Experience Card -->
                <div class="bg-white dark:bg-slate-800 rounded-xl p-6 md:p-8 shadow-sm border border-slate-100 dark:border-slate-700/50">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-50 dark:border-slate-700/50 pb-3 flex items-center gap-3">
                        <i class="fa-solid fa-clock-rotate-left text-[#B21F24]"></i>
                        Work Statistics
                    </h3>
                    
                    @php
                        $start = \Carbon\Carbon::parse($user->startwork_date ?? now());
                        $now = \Carbon\Carbon::now();
                        $diff = $start->diff($now);
                        $totalMonths = ($diff->y * 12) + $diff->m;
                    @endphp

                    <div class="space-y-6">
                        <div class="text-center">
                            <div class="text-4xl font-extrabold text-[#B21F24] tracking-tight">{{ $diff->y }}</div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1">Years in Service</div>
                        </div>

                        <div class="space-y-4 pt-6 border-t border-slate-150 dark:border-slate-700/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-slate-50 dark:bg-slate-700/30 flex items-center justify-center text-slate-500 dark:text-slate-400 border border-slate-100 dark:border-slate-700/50">
                                    <i class="fa-solid fa-calendar-check text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Started Date</p>
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate">{{ $start->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-slate-50 dark:bg-slate-700/30 flex items-center justify-center text-slate-500 dark:text-slate-400 border border-slate-100 dark:border-slate-700/50">
                                    <i class="fa-solid fa-hourglass-half text-xs"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Current Status</p>
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate">{{ $totalMonths }} Months employed</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Overview Card -->
                <div class="bg-[#8B1A1E] text-white rounded-xl p-6 md:p-8 shadow-sm border border-red-700">
                    <p class="text-[9px] font-bold uppercase tracking-wider opacity-70 mb-1">Primary Department</p>
                    <h4 class="text-lg font-bold mb-4 truncate">{{ optional($user->department)->department_name ?? 'Kumwell Corp' }}</h4>
                    <div class="flex items-center gap-3 text-xs opacity-90">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>{{ $user->workplace ?: 'Main Office, BKK' }}</span>
                    </div>
                </div>
            </div>

            <!-- Detailed Grid -->
            <div class="lg:col-span-8 space-y-6 md:space-y-8">
                <!-- Navigation Tabs -->
                <div class="flex gap-8 border-b border-slate-200 dark:border-slate-700 px-2">
                    <button class="pb-3 text-xs font-bold uppercase tracking-wider text-[#B21F24] border-b-2 border-[#B21F24]">Information</button>
                    <button class="pb-3 text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">Career Path</button>
                </div>

                <!-- Info Sections -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Personal Info -->
                    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 md:p-8 shadow-sm border border-slate-100 dark:border-slate-700/50 transition-colors hover:border-[#B21F24]/30">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                            <span class="w-1 h-4 bg-[#B21F24] rounded-full"></span>
                            Personal Information
                        </h4>
                        <div class="space-y-4">
                            <div>
                                <label class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Employee Code</label>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $user->employee_code }}</p>
                            </div>
                            <div>
                                <label class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Gender</label>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $user->sex ?? 'Not Specified' }}</p>
                            </div>
                            <div>
                                <label class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Employee Type</label>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $user->employee_type ?? 'Permanent' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Org Info -->
                    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 md:p-8 shadow-sm border border-slate-100 dark:border-slate-700/50 transition-colors hover:border-[#B21F24]/30">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                            <span class="w-1 h-4 bg-[#B21F24] rounded-full"></span>
                            Organizational Details
                        </h4>
                        <div class="space-y-4">
                            <div>
                                <label class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Section Code</label>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ optional($user->section)->section_code ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Division</label>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ optional($user->division)->division_name ?? '-' }}</p>
                            </div>
                            <div>
                                <label class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Department</label>
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ optional($user->department)->department_name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HR Level / Compliance Card -->
                <div class="bg-slate-50 dark:bg-slate-800/20 rounded-xl p-6 md:p-8 border border-dashed border-slate-200 dark:border-slate-700">
                    <div class="flex flex-col md:flex-row items-center gap-6">
                        <div class="w-14 h-14 rounded-xl bg-white dark:bg-slate-700 shadow-sm flex items-center justify-center border border-slate-100 dark:border-slate-600 shrink-0">
                            <i class="fa-solid fa-shield-halved text-[#B21F24] text-xl"></i>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h5 class="text-sm font-bold text-slate-900 dark:text-white mb-1.5">Corporate Classification</h5>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-semibold">
                                Current HR Level: <span class="text-[#B21F24] dark:text-red-400 font-extrabold">{{ $user->hr_level ?? 'Standard' }}</span>. 
                                This information is managed by the Human Resources department specialized for Kumwell Corporation.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function uploadAvatar(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Basic validation
            if (!file.type.match('image.*')) {
                Swal.fire({
                    icon: 'error',
                    title: 'ไฟล์ไม่ถูกต้อง',
                    text: 'กรุณาเลือกไฟล์รูปภาพเท่านั้น'
                });
                return;
            }

            if (file.size > 2 * 1024 * 1024) { // 2MB
                Swal.fire({
                    icon: 'error',
                    title: 'ไฟล์ใหญ่เกินไป',
                    text: 'กรุณาเลือกรูปภาพขนาดไม่เกิน 2MB'
                });
                return;
            }

            const formData = new FormData();
            formData.append('avatar', file);

            // Show loading
            Swal.fire({
                title: 'กำลังอัปโหลด...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route("users.update_avatar") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update image on page
                    const img = document.getElementById('profile-image');
                    const placeholder = document.getElementById('profile-placeholder');
                    
                    if (img) {
                        img.src = data.avatar_url;
                    } else if (placeholder) {
                        // Create img if it was a placeholder
                        const newImg = document.createElement('img');
                        newImg.id = 'profile-image';
                        newImg.src = data.avatar_url;
                        newImg.alt = 'User Avatar';
                        newImg.className = 'w-full h-full object-cover';
                        placeholder.parentNode.replaceChild(newImg, placeholder);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: 'อัปโหลดรูปภาพโปรไฟล์เรียบร้อยแล้ว',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'ผิดพลาด',
                        text: data.message || 'ไม่สามารถอัปโหลดรูปภาพได้'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด',
                    text: 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์'
                });
            });
        }
    }
</script>
@endpush
@endsection