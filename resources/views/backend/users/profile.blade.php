@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 pb-20">
    <!-- Premium Hero Section -->
    <div id="hero-section" class="relative h-64 md:h-80 overflow-hidden bg-[#4A0E10]">
        <!-- Background with high-end gradient and pattern -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#B21F24] via-[#8B1A1E] to-[#4A0E10]"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] text-white"></div>
        
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <!-- Flowing Lines (Animated SVG) -->
            <svg class="absolute inset-0 w-full h-full opacity-20" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="lineGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="transparent" />
                        <stop offset="50%" stop-color="white" />
                        <stop offset="100%" stop-color="transparent" />
                    </linearGradient>
                </defs>
                <path class="animate-flow-line" d="M -100 100 Q 200 50 500 100 T 1100 100" stroke="url(#lineGradient)" stroke-width="2" fill="none" />
                <path class="animate-flow-line-delayed" d="M -100 150 Q 300 100 600 150 T 1200 150" stroke="url(#lineGradient)" stroke-width="1" fill="none" />
            </svg>

            <!-- Sparkles/Glimmer -->
            <div class="absolute inset-0">
                @for ($i = 0; $i < 12; $i++)
                    <div class="sparkle absolute w-1 h-1 bg-white rounded-full opacity-0 animate-twinkle" 
                         style="top: {{ rand(5, 95) }}%; left: {{ rand(5, 95) }}%; animation-delay: {{ rand(0, 8000) }}ms;"></div>
                @endfor
            </div>

            <!-- Floating Orbs -->
            <div class="parallax-element absolute top-[10%] left-[10%] w-64 h-64 bg-white/10 rounded-full blur-[80px] animate-float-slow" data-speed="0.02"></div>
            <div class="parallax-element absolute bottom-[20%] right-[15%] w-80 h-80 bg-black/20 rounded-full blur-[100px] animate-pulse-slow" data-speed="-0.03"></div>
            <div class="parallax-element absolute top-[40%] right-[10%] w-48 h-48 bg-kumwell-red/20 rounded-full blur-[60px] animate-float-delayed" data-speed="0.04"></div>
            
            <!-- Stardust/Particle effect -->
            <div class="absolute inset-0 opacity-30" id="particles-container">
                @for ($i = 0; $i < 20; $i++)
                    <div class="particle absolute bg-white rounded-full opacity-0 animate-stardust" 
                         style="width: {{ rand(1, 2) }}px; height: {{ rand(1, 2) }}px; top: {{ rand(0, 100) }}%; left: {{ rand(0, 100) }}%; animation-delay: {{ rand(0, 5000) }}ms;"></div>
                @endfor
            </div>
        </div>

        <style>
            @keyframes float {
                0%, 100% { transform: translateY(0) translateX(0); }
                33% { transform: translateY(-20px) translateX(10px); }
                66% { transform: translateY(10px) translateX(-15px); }
            }
            @keyframes pulse-slow {
                0%, 100% { opacity: 0.3; transform: scale(1); }
                50% { opacity: 0.5; transform: scale(1.1); }
            }
            @keyframes stardust {
                0% { opacity: 0; transform: translateY(0); }
                50% { opacity: 1; }
                100% { opacity: 0; transform: translateY(-40px); }
            }
            @keyframes twinkle {
                0%, 100% { opacity: 0; transform: scale(0) rotate(0deg); }
                50% { opacity: 0.8; transform: scale(1.5) rotate(180deg); }
            }
            @keyframes flow-line {
                0% { stroke-dasharray: 0 1000; stroke-dashoffset: 0; opacity: 0; }
                20% { opacity: 0.5; }
                80% { opacity: 0.5; }
                100% { stroke-dasharray: 1000 0; stroke-dashoffset: -1000; opacity: 0; }
            }
            .animate-float-slow { animation: float 15s ease-in-out infinite; }
            .animate-float-delayed { animation: float 18s ease-in-out infinite 2s; }
            .animate-pulse-slow { animation: pulse-slow 10s ease-in-out infinite; }
            .animate-stardust { animation: stardust 5s linear infinite; }
            .animate-twinkle { animation: twinkle 4s ease-in-out infinite; }
            .animate-flow-line { 
                animation: flow-line 8s linear infinite;
                stroke-dasharray: 300 1000;
            }
            .animate-flow-line-delayed { 
                animation: flow-line 12s linear infinite 3s;
                stroke-dasharray: 200 1000;
            }
            
            /* Glimmer/Ving Effect */
            .sparkle::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(circle, white 0%, transparent 70%);
                transform: translate(-50%, -50%);
                filter: blur(2px);
            }
        </style>
    </div>

    <!-- Main Profile Container -->
    <div class="max-w-6xl mx-auto px-6 -mt-32 relative z-10">
        <!-- Header Card -->
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-[2.5rem] p-8 md:p-12 shadow-2xl border border-white/20 dark:border-slate-700/50 mb-8">
            <div class="flex flex-col md:flex-row items-center md:items-end gap-8">
                <!-- Avatar Section -->
                <div class="relative group">
                    <div id="avatar-container" class="w-40 h-40 md:w-48 md:h-48 rounded-[2rem] overflow-hidden ring-8 ring-white dark:ring-slate-800 shadow-2xl transition-transform duration-500 group-hover:scale-105 bg-white dark:bg-slate-700">
                        @php $avatar = $user->photo_user; @endphp
                        @if($avatar)
                            <img id="profile-image" src="{{ asset($avatar) }}" alt="User Avatar" class="w-full h-full object-cover" loading="lazy">
                        @else
                            <div id="profile-placeholder" class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-slate-700 dark:to-slate-600 flex items-center justify-center text-gray-400 dark:text-slate-500">
                                <i class="fa-solid fa-user text-6xl"></i>
                            </div>
                        @endif
                        
                        <!-- Upload Overlay -->
                        <div id="upload-overlay" class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                            <i class="fa-solid fa-cloud-arrow-up text-white text-3xl"></i>
                        </div>
                    </div>
                    
                    @php $inputId = 'avatarUpload-'.($user->id ?? 'me'); @endphp
                    <label for="{{ $inputId }}" class="absolute bottom-4 right-4 bg-white dark:bg-slate-700 w-12 h-12 rounded-2xl shadow-xl flex items-center justify-center cursor-pointer hover:bg-kumwell-red hover:text-white transition-all transform hover:rotate-12 z-20">
                        <i class="fa-solid fa-camera text-lg"></i>
                        <input id="{{ $inputId }}" type="file" accept="image/*" class="hidden" onchange="uploadAvatar(this)">
                    </label>
                </div>

                <!-- User Identity -->
                <div class="flex-1 flex flex-col items-center md:items-start space-y-4 text-center md:text-left">
                    <div class="space-y-1">
                        <span class="inline-block px-4 py-1.5 rounded-full bg-kumwell-red/10 text-kumwell-red text-xs font-black uppercase tracking-widest border border-kumwell-red/20">
                            {{ $user->usertype->description ?? 'Employee' }}
                        </span>
                        <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tight">
                            {{ $user->first_name }} {{ $user->last_name }}
                        </h1>
                    </div>
                    
                    <div class="flex flex-wrap justify-center md:justify-start gap-4 text-sm font-bold text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-slate-700/50 rounded-xl">
                            <i class="fa-solid fa-id-badge text-kumwell-red"></i>
                            <span>{{ $user->employee_code }}</span>
                        </div>
                        <div class="flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-slate-700/50 rounded-xl">
                            <i class="fa-solid fa-briefcase text-kumwell-red"></i>
                            <span>{{ $user->position ?: 'N/A' }}</span>
                        </div>
                        <div class="flex items-center gap-2 px-4 py-2 bg-green-500/10 text-green-600 border border-green-500/20 rounded-xl">
                            <i class="fa-solid fa-circle text-[8px] animate-pulse"></i>
                            <span>Active</span>
                        </div>
                    </div>

                    <!-- Mobile Action Button -->
                    <div class="lg:hidden w-full pt-4">
                        <a href="#" class="w-full text-center bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-8 py-4 rounded-3xl font-black text-sm uppercase tracking-widest active:scale-95 transition-all shadow-xl shadow-gray-400/20 dark:shadow-none inline-block">
                            Edit Profile
                        </a>
                    </div>
                </div>

                <!-- Primary Action (Optional) -->
                <div class="hidden lg:block pb-4">
                    <a href="#" class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-8 py-4 rounded-3xl font-black text-sm uppercase tracking-widest hover:scale-105 transition-all shadow-xl shadow-gray-400/20 dark:shadow-none inline-block">
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Sidebar Info (Stats) -->
            <div class="lg:col-span-4 space-y-8">
                <!-- Work Experience Card -->
                <div class="bg-white dark:bg-slate-800 rounded-[2rem] p-8 shadow-sm border border-gray-100 dark:border-slate-700">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white mb-8 border-b border-gray-50 dark:border-slate-700 pb-4 flex items-center gap-3">
                        <i class="fa-solid fa-clock-rotate-left text-kumwell-red"></i>
                        Work Statistics
                    </h3>
                    
                    @php
                        $start = \Carbon\Carbon::parse($user->startwork_date ?? now());
                        $now = \Carbon\Carbon::now();
                        $diff = $start->diff($now);
                        $totalMonths = ($diff->y * 12) + $diff->m;
                    @endphp

                    <div class="space-y-10">
                        <div class="text-center">
                            <div class="text-5xl font-black text-kumwell-red tracking-tighter">{{ $diff->y }}</div>
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Years in Service</div>
                        </div>

                        <div class="space-y-6 pt-6 border-t border-gray-50 dark:border-slate-700">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-500">
                                    <i class="fa-solid fa-calendar-check"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.05em]">Started Date</p>
                                    <p class="text-sm font-black text-gray-800 dark:text-gray-200">{{ $start->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center text-orange-500">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.05em]">Current Status</p>
                                    <p class="text-sm font-black text-gray-800 dark:text-gray-200">{{ $totalMonths }} Months employed</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Overview Card -->
                <div class="bg-gradient-to-br from-kumwell-red to-[#8B1A1E] rounded-[2rem] p-8 text-white shadow-xl shadow-orange-500/20">
                    <p class="text-xs font-bold uppercase tracking-widest opacity-70 mb-2">Primary Department</p>
                    <h4 class="text-2xl font-black mb-6">{{ optional($user->department)->department_name ?? 'Kumwell Corp' }}</h4>
                    <div class="flex items-center gap-4 text-sm opacity-90">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>{{ $user->workplace ?: 'Main Office, BKK' }}</span>
                    </div>
                </div>
            </div>

            <!-- Detailed Grid -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Navigation Tabs -->
                <div class="flex gap-10 border-b border-gray-200 dark:border-slate-700 px-4">
                    <button class="pb-4 text-sm font-black uppercase tracking-widest text-kumwell-red border-b-4 border-kumwell-red">Information</button>
                    <button class="pb-4 text-sm font-black uppercase tracking-widest text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Career Path</button>
                </div>

                <!-- Info Sections -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Personal Info -->
                    <div class="bg-white dark:bg-slate-800 rounded-[2rem] p-8 shadow-sm border border-gray-100 dark:border-slate-700 transition-all hover:border-kumwell-red/30">
                        <h4 class="text-base font-black text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                            <span class="w-1 h-6 bg-kumwell-red rounded-full"></span>
                            Personal Information
                        </h4>
                        <div class="space-y-6">
                            <div class="group">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Employee Code</label>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $user->employee_code }}</p>
                            </div>
                            <div class="group">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Gender</label>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $user->sex ?? 'Not Specified' }}</p>
                            </div>
                            <div class="group">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Employee Type</label>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $user->employee_type ?? 'Permanent' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Org Info -->
                    <div class="bg-white dark:bg-slate-800 rounded-[2rem] p-8 shadow-sm border border-gray-100 dark:border-slate-700 transition-all hover:border-kumwell-red/30">
                        <h4 class="text-base font-black text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                            <span class="w-1 h-6 bg-kumwell-red rounded-full"></span>
                            Organizational Details
                        </h4>
                        <div class="space-y-6">
                            <div class="group">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Section Code</label>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ optional($user->section)->section_code ?? '-' }}</p>
                            </div>
                            <div class="group">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Division</label>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ optional($user->division)->division_name ?? '-' }}</p>
                            </div>
                            <div class="group">
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Department</label>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ optional($user->department)->department_name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- HR Level / Compliance Card -->
                <div class="bg-gray-50 dark:bg-slate-800/40 rounded-[2.5rem] p-10 border-2 border-dashed border-gray-200 dark:border-slate-700">
                    <div class="flex flex-col md:flex-row items-center gap-8">
                        <div class="w-20 h-20 rounded-full bg-white dark:bg-slate-700 shadow-xl flex items-center justify-center">
                            <i class="fa-solid fa-shield-halved text-kumwell-red text-3xl"></i>
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h5 class="text-lg font-black text-gray-900 dark:text-white mb-2">Corporate Classification</h5>
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed font-medium">
                                Current HR Level: <span class="text-kumwell-red font-black">{{ $user->hr_level ?? 'Standard' }}</span>. 
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

    // Parallax Effect for Background Elements
    document.addEventListener('mousemove', (e) => {
        const hero = document.getElementById('hero-section');
        if (!hero) return;

        const x = e.clientX / window.innerWidth;
        const y = e.clientY / window.innerHeight;

        const elements = document.querySelectorAll('.parallax-element');
        elements.forEach(el => {
            const speed = parseFloat(el.getAttribute('data-speed')) || 0.05;
            const xOffset = (window.innerWidth / 2 - e.clientX) * speed;
            const yOffset = (window.innerHeight / 2 - e.clientY) * speed;
            
            el.style.transform = `translate(${xOffset}px, ${yOffset}px)`;
        });
    });

    // Particle/Stardust Enhancement
    function createExtraParticles() {
        const container = document.getElementById('particles-container');
        if (!container) return;

        for (let i = 0; i < 10; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle absolute bg-white rounded-full opacity-0 animate-stardust';
            
            const size = Math.random() * 2 + 1;
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            particle.style.top = `${Math.random() * 100}%`;
            particle.style.left = `${Math.random() * 100}%`;
            particle.style.animationDelay = `${Math.random() * 5000}ms`;
            
            container.appendChild(particle);
        }
    }
    
    // Periodically add new particles to keep it fresh
    setInterval(createExtraParticles, 10000);
</script>
@endpush
@endsection