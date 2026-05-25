@extends('layouts.sidebar')

@section('title', 'จัดการผู้สมัครฝึกอบรม')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold dark:text-white text-gray-800">จัดการผู้สมัครฝึกอบรม</h2>
                @if(isset($course_name))
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">หลักสูตร: {{ $course_name }}</p>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('backend.training.index') }}"
                    class="bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold py-2 px-4 rounded-xl transition-all flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    กลับไปที่รายการ
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <div
            class="bg-white dark:bg-kumwell-card rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-gray-800 transition-all">
            <form action="{{ route('backend.training.applicants') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1 relative group">
                    <div
                        class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-kumwell-red transition-colors">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="ค้นหาชื่อพนักงาน หรือ รหัสพนักงาน..."
                        class="w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-kumwell-dark border-none rounded-2xl text-sm focus:ring-2 focus:ring-kumwell-red/20 dark:text-white transition-all">
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <select name="training_id"
                        class="px-4 py-3 bg-gray-50 dark:bg-kumwell-dark border-none rounded-2xl text-sm focus:ring-2 focus:ring-kumwell-red/20 dark:text-white appearance-none cursor-pointer min-w-[200px]">
                        <option value="">ทุกหลักสูตร</option>
                        @foreach($trainings as $training)
                            <option value="{{ $training->id }}" {{ (request('training_id') == $training->id || (isset($selected_training_id) && $selected_training_id == $training->id)) ? 'selected' : '' }}>
                                {{ $training->branch }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="bg-kumwell-red hover:bg-red-700 text-white font-bold py-3 px-6 rounded-2xl shadow-lg shadow-red-500/20 transition-all active:scale-95">
                        <i class="fa-solid fa-filter mr-2"></i> กรองข้อมูล
                    </button>

                    @if(request()->anyFilled(['search', 'training_id']))
                        <a href="{{ route('backend.training.applicants') }}"
                            class="p-3 text-gray-400 hover:text-kumwell-red transition-colors">
                            <i class="fa-solid fa-rotate-right"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Applicants Table -->
        <div
            class="bg-white dark:bg-kumwell-card rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-kumwell-dark/30">
                            <th
                                class="px-6 py-4 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center">
                                ลำดับ</th>
                            <th
                                class="px-6 py-4 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                                พนักงาน</th>
                            <th
                                class="px-6 py-4 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                                จำนวนสมัคร</th>
                            <th
                                class="px-6 py-4 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center">
                                รายละเอียด</th>
                            <th
                                class="px-6 py-4 text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest text-right">
                                สถานะ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($applicants as $index => $user)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-kumwell-dark/20 transition-colors group">
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="text-xs text-gray-500 dark:text-gray-400">{{ $applicants->firstItem() + $index }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-kumwell-red group-hover:text-white transition-all">
                                            <i class="fa-solid fa-user text-xs"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-700 dark:text-slate-200">
                                                {{ $user->fullname }}
                                            </p>
                                            <p class="text-[10px] text-slate-400">{{ $user->employee_code }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 rounded-full bg-kumwell-red/10 text-kumwell-red text-[10px] font-bold">
                                        {{ $user->trainingApplies->count() }} หลักสูตร
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="toggleDetails('details-{{ $user->id }}')"
                                        class="rounded-md bg-blue-600 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-blue-700 focus:shadow-none active:bg-blue-700 hover:bg-blue-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2">
                                        <i class="fa-solid fa-eye mr-1"></i> ดูรายละเอียด
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span
                                        class="inline-flex items-center gap-1.5 py-0.5 px-2 rounded-full text-[9px] font-black bg-emerald-50 dark:bg-emerald-900/10 text-emerald-600 border border-emerald-200/50 dark:border-emerald-900/30 uppercase">
                                        <span class="w-1 h-1 rounded-full bg-emerald-600 animate-pulse"></span>
                                        ACTIVE
                                    </span>
                                </td>
                            </tr>
                            <!-- Details Row (Hidden by default) -->
                            <tr id="details-{{ $user->id }}" class="hidden bg-slate-50/50 dark:bg-slate-900/10">
                                <td colspan="5" class="px-8 py-6">
                                    <div
                                        class="bg-white dark:bg-kumwell-dark rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-800">
                                        <div class="flex items-center gap-2 mb-4">
                                            <div class="w-1.5 h-4 bg-kumwell-red rounded-full"></div>
                                            <h4
                                                class="text-xs font-bold text-gray-700 dark:text-slate-200 uppercase tracking-wider">
                                                ประวัติการลงทะเบียนฝึกอบรม</h4>
                                        </div>

                                        <div class="overflow-hidden rounded-xl border border-gray-100 dark:border-gray-800">
                                            <table class="w-full text-left text-xs">
                                                <thead>
                                                    <tr class="bg-gray-50/50 dark:bg-kumwell-dark/50">
                                                        <th class="px-4 py-3 font-bold text-slate-500">หลักสูตร</th>
                                                        <th class="px-4 py-3 font-bold text-slate-500 text-center">รูปแบบ</th>
                                                        <th class="px-4 py-3 font-bold text-slate-500 text-center">จำนวนชม.</th>
                                                        <th class="px-4 py-3 font-bold text-slate-500 text-center">วันที่อบรม
                                                        </th>
                                                        <th class="px-4 py-3 font-bold text-slate-500 text-right">
                                                            วันที่ลงทะเบียน</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                                    @foreach($user->trainingApplies as $apply)
                                                        @php $t = $apply->training; @endphp
                                                        <tr class="hover:bg-gray-50/30 dark:hover:bg-gray-800/30 transition-colors">
                                                            <td class="px-4 py-3">
                                                                <span class="font-bold text-gray-700 dark:text-slate-300">
                                                                    {{ $t ? $t->branch : 'ไม่พบข้อมูล' }}
                                                                </span>
                                                            </td>
                                                            <td class="px-4 py-3 text-center">
                                                                <span
                                                                    class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-[10px] text-slate-600 dark:text-slate-400">
                                                                    {{ $t ? $t->format : '-' }}
                                                                </span>
                                                            </td>
                                                            <td class="px-4 py-3 text-center">
                                                                <span class="font-mono text-kumwell-red font-bold">
                                                                    {{ $t ? $t->hours : '-' }}
                                                                </span>
                                                            </td>
                                                            <td class="px-4 py-3 text-center">
                                                                <div class="text-[10px] text-slate-500">
                                                                    @if($t)
                                                                        {{ $t->start_date }} - {{ $t->end_date }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td class="px-4 py-3 text-right">
                                                                <span class="text-[10px] text-slate-400 font-mono">
                                                                    {{ $apply->created_at->format('d/m/Y H:i') }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center text-slate-400">
                                    <i class="fa-solid fa-inbox text-5xl mb-4 opacity-10"></i>
                                    <p class="text-xs uppercase tracking-widest font-bold">
                                        ไม่พบข้อมูลพนักงานที่สมัครเข้าร่วมอบรม</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($applicants->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $applicants->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function toggleDetails(id) {
            const el = document.getElementById(id);
            if (el.classList.contains('hidden')) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        }
    </script>
@endsection