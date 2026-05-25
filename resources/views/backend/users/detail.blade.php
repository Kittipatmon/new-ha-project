@extends('layouts.sidebar')
@section('title', 'รายละเอียดพนักงาน : ' . $user->employee_code)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-7xl">
    {{-- Header / Breadcrumb --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
            <i class="fa-solid fa-user-circle text-primary"></i>
            รายละเอียดพนักงาน
        </h1>
        <a href="{{ route('users.index') }}" class="btn btn-ghost btn-sm gap-2">
            <i class="fa-solid fa-arrow-left"></i> ย้อนกลับ
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        {{-- Left Column: Profile Summary & Status --}}
        <div class="xl:col-span-1 space-y-8">
            
            {{-- Profile Card --}}
            <div class="card bg-base-100 dark:bg-gray-800 shadow-xl border border-base-200 dark:border-gray-700 overflow-hidden group">
                {{-- Decorative Background --}}
                <div class="h-32 bg-gradient-to-r from-primary/80 to-secondary/80 relative">
                    <div class="absolute -bottom-12 left-1/2 transform -translate-x-1/2">
                        <div class="avatar placeholder">
                            <div class="bg-white dark:bg-gray-800 text-primary text-4xl font-bold rounded-full w-24 h-24 ring-4 ring-white dark:ring-gray-800 shadow-lg flex items-center justify-center">
                                <span>{{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body items-center text-center pt-14 pb-8 px-6">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                        {{ $user->prefix }} {{ $user->first_name }} {{ $user->last_name }}
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">{{ $user->position ?? 'ไม่ระบุตำแหน่ง' }}</p>
                    
                    <div class="w-full mt-6 grid grid-cols-2 gap-4">
                        <div class="flex flex-col p-3 rounded-2xl bg-base-200/50 dark:bg-gray-700/30">
                            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">รหัสพนักงาน</span>
                            <span class="font-mono font-bold text-primary text-lg">{{ $user->employee_code }}</span>
                        </div>
                        <div class="flex flex-col p-3 rounded-2xl bg-base-200/50 dark:bg-gray-700/30">
                            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">อายุงาน</span>
                            <span class="font-semibold text-gray-700 dark:text-gray-300">
                                @if($user->startwork_date)
                                    {{ \Carbon\Carbon::parse($user->startwork_date)->diffForHumans(null, true) }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- System Status Card --}}
            <div class="card bg-base-100 dark:bg-gray-800 shadow-xl border border-base-200 dark:border-gray-700">
                <div class="card-body p-6">
                    <h3 class="font-bold text-lg mb-4 flex items-center gap-2 text-gray-800 dark:text-white">
                        <div class="w-8 h-8 rounded-lg bg-accent/10 flex items-center justify-center text-accent">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        สถานะในระบบ
                    </h3>
                    
                    <div class="space-y-5">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">สถานะพนักงาน</p>
                            @php
                                $statusOptions = \App\Models\User::getStatusOptions();
                                $statusMeta = $statusOptions[$user->status] ?? ['label' => '-', 'color' => 'gray'];
                                $badgeClass = match($user->status) {
                                    \App\Models\User::STATUS_ACTIVE => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800',
                                    \App\Models\User::STATUS_INACTIVE => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700/30 dark:text-gray-300 border border-gray-200 dark:border-gray-700'
                                };
                            @endphp
                            <div class="flex items-center justify-center p-3 rounded-xl {{ $badgeClass }} font-semibold transition-all">
                                <i class="fa-solid fa-circle text-[10px] mr-2 opacity-70"></i>
                                {{ $statusMeta['label'] }}
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3 rounded-xl bg-base-200/50 dark:bg-gray-700/30 border border-base-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">ระดับ User</p>
                                <div class="font-semibold text-center text-gray-800 dark:text-gray-200">
                                    {{ $user->level_user }}
                                </div>
                            </div>
                            <div class="p-3 rounded-xl bg-base-200/50 dark:bg-gray-700/30 border border-base-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">สถานะ HR</p>
                                @php
                                    $hrStatusOptions = \App\Models\User::getHrStatusOptions();
                                    $hrLabel = $hrStatusOptions[$user->hr_status]['label'] ?? '-';
                                @endphp
                                <div class="font-semibold text-center text-gray-800 dark:text-gray-200">
                                    {{ $hrLabel }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Resignation Alert --}}
            @if($user->status == \App\Models\User::STATUS_INACTIVE)
            <div class="card bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800/50 shadow-sm overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-red-500/10 rounded-full blur-xl"></div>
                <div class="card-body p-5 relative">
                    <h3 class="font-bold text-red-700 dark:text-red-400 flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i> ข้อมูลการพ้นสภาพ
                    </h3>
                    <div class="mt-3 space-y-3">
                        <div class="flex justify-between items-center bg-white/60 dark:bg-black/20 p-2 rounded-lg">
                            <span class="text-sm text-red-600 dark:text-red-300">วันที่สิ้นสุด</span>
                            <span class="font-bold text-red-800 dark:text-red-200">
                                {{ isset($user->endwork_date) ? $user->endwork_date->format('d/m/Y') : '-' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-red-500 dark:text-red-400 mb-1 ml-1">เหตุผล</p>
                            <div class="p-3 rounded-lg bg-white/60 dark:bg-black/20 text-red-800 dark:text-red-200 text-sm leading-relaxed">
                                {{ $user->endwork_comment ?: '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- Right Column: Detailed Info --}}
        <div class="xl:col-span-2 space-y-8">
            
            {{-- Card 1: Personal Details --}}
            <div class="card bg-base-100 dark:bg-gray-800 shadow-xl border border-base-200 dark:border-gray-700">
                <div class="card-body p-6 md:p-8">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shadow-sm">
                            <i class="fa-regular fa-id-card text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-xl text-gray-800 dark:text-white">ข้อมูลส่วนตัว</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">รายละเอียดข้อมูลพื้นฐานของพนักงาน</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                        <div class="group">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">คำนำหน้า</label>
                            <p class="text-lg font-medium text-gray-800 dark:text-gray-200 border-b border-base-200 dark:border-gray-700 pb-2 group-hover:border-primary/50 transition-colors">
                                {{ $user->prefix }}
                            </p>
                        </div>
                        <div class="group">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">เพศ</label>
                            <p class="text-lg font-medium text-gray-800 dark:text-gray-200 border-b border-base-200 dark:border-gray-700 pb-2 group-hover:border-primary/50 transition-colors">
                                {{ $user->sex }}
                            </p>
                        </div>
                        <div class="group">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">ชื่อจริง</label>
                            <p class="text-lg font-medium text-gray-800 dark:text-gray-200 border-b border-base-200 dark:border-gray-700 pb-2 group-hover:border-primary/50 transition-colors">
                                {{ $user->first_name }}
                            </p>
                        </div>
                        <div class="group">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">นามสกุล</label>
                            <p class="text-lg font-medium text-gray-800 dark:text-gray-200 border-b border-base-200 dark:border-gray-700 pb-2 group-hover:border-primary/50 transition-colors">
                                {{ $user->last_name }}
                            </p>
                        </div>
                        <div class="group">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">วันที่เริ่มงาน</label>
                            <div class="flex items-center gap-3 border-b border-base-200 dark:border-gray-700 pb-2 group-hover:border-primary/50 transition-colors">
                                <i class="fa-regular fa-calendar text-gray-400"></i>
                                <p class="text-lg font-medium text-gray-800 dark:text-gray-200">
                                    {{ isset($user->startwork_date) ? $user->startwork_date->format('d/m/Y') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Work Details --}}
            <div class="card bg-base-100 dark:bg-gray-800 shadow-xl border border-base-200 dark:border-gray-700">
                <div class="card-body p-6 md:p-8">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-secondary/10 flex items-center justify-center text-secondary shadow-sm">
                            <i class="fa-solid fa-briefcase text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-xl text-gray-800 dark:text-white">ข้อมูลการทำงาน</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">รายละเอียดตำแหน่งและสังกัด</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                        <div class="col-span-1 md:col-span-2 group">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">ตำแหน่ง</label>
                            <p class="text-xl font-semibold text-primary border-b border-base-200 dark:border-gray-700 pb-2 group-hover:border-secondary/50 transition-colors">
                                {{ $user->position ?: '-' }}
                            </p>
                        </div>
                        <div class="group">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">แผนก (Department)</label>
                            <p class="text-lg font-medium text-gray-800 dark:text-gray-200 border-b border-base-200 dark:border-gray-700 pb-2 group-hover:border-secondary/50 transition-colors">
                                {{ $user->department->department_name ?? '-' }}
                            </p>
                        </div>
                        <div class="group">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">ฝ่าย (Division)</label>
                            <p class="text-lg font-medium text-gray-800 dark:text-gray-200 border-b border-base-200 dark:border-gray-700 pb-2 group-hover:border-secondary/50 transition-colors">
                                {{ $user->division->division_name ?? '-' }}
                            </p>
                        </div>
                        <div class="group">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">สายงาน (Section)</label>
                            <p class="text-lg font-medium text-gray-800 dark:text-gray-200 border-b border-base-200 dark:border-gray-700 pb-2 group-hover:border-secondary/50 transition-colors">
                                {{ $user->section->section_name ?? '-' }}
                            </p>
                        </div>
                        <div class="group">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">ประเภทพนักงาน</label>
                            <p class="text-lg font-medium text-gray-800 dark:text-gray-200 border-b border-base-200 dark:border-gray-700 pb-2 group-hover:border-secondary/50 transition-colors">
                                {{ $user->employee_type ?: '-' }}
                            </p>
                        </div>
                        <div class="col-span-1 md:col-span-2 group">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">สถานที่ทำงาน</label>
                            <div class="flex items-center gap-3 border-b border-base-200 dark:border-gray-700 pb-2 group-hover:border-secondary/50 transition-colors">
                                <i class="fa-solid fa-location-dot text-error/80"></i>
                                <p class="text-lg font-medium text-gray-800 dark:text-gray-200">
                                    {{ $user->workplace ?: '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection