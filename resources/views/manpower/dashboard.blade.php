@extends('layouts.manpower.app')

@section('content')
    <div class="min-h-screen p-6 pt-8 pb-20 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-gray-200">
        <div class="max-w-8xl mx-auto space-y-8">
            
            <!-- Top Navigation & Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 dark:text-white flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-xl shadow-blue-500/20">
                            <i class="fa-solid fa-users-gear text-xl"></i>
                        </div>
                        ฝ่ายบริหารทรัพยากรบุคคล (Manpower Dashboard)
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm font-medium">
                        ภาพรวมทรัพยากรบุคคล สถิติ และข้อมูลเชิงลึกขององค์กรรายบุคคลและสาขา
                    </p>
                </div>

                    {{-- Custom Period Filter --}}
                    <div x-data="{ 
                        open: false, 
                        selected: '{{ ($currentFilter ?? '') == 'quarter' ? 'ไตรมาสนี้' : (($currentFilter ?? '') == 'year' ? 'ปีนี้' : 'เดือนนี้') }}',
                        value: '{{ $currentFilter ?? 'month' }}',
                        options: [
                            { label: 'เดือนนี้', value: 'month' },
                            { label: 'ไตรมาสนี้', value: 'quarter' },
                            { label: 'ปีนี้', value: 'year' }
                        ],
                        select(option) {
                            this.selected = option.label;
                            this.value = option.value;
                            this.open = false;
                            $nextTick(() => { $refs.filterForm.submit(); });
                        }
                    }" class="relative">
                        <form x-ref="filterForm" action="{{ route('manpower.dashboard') }}" method="GET">
                            <input type="hidden" name="period" :value="value">
                        </form>
                        
                        <button @click="open = !open" type="button" 
                            class="flex items-center justify-between gap-3 px-5 py-2.5 bg-white dark:bg-slate-800 border-0 ring-1 ring-slate-200 dark:ring-white/10 rounded-2xl text-sm font-bold shadow-sm transition-all hover:ring-blue-500 min-w-[140px]">
                            <span class="text-slate-700 dark:text-slate-200" x-text="selected"></span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform" :class="{'rotate-180': open}"></i>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2"
                            class="absolute right-0 z-50 mt-3 w-44 origin-top-right rounded-2xl bg-white dark:bg-slate-800 shadow-2xl ring-1 ring-black/5 dark:ring-white/5 overflow-hidden py-1.5" style="display: none;">
                            <template x-for="option in options" :key="option.value">
                                <button @click="select(option)" type="button" 
                                    class="flex items-center w-full px-4 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    <span x-text="option.label"></span>
                                    <i x-show="value === option.value" class="fa-solid fa-check ml-auto text-blue-500 text-xs"></i>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="relative">
                        <button @click="open = !open" @click.away="open = false" 
                            class="inline-flex items-center justify-center gap-2 bg-slate-900 dark:bg-blue-600 text-white font-bold py-2.5 px-6 rounded-2xl text-sm shadow-lg shadow-blue-500/20 hover:scale-[1.02] active:scale-95 transition-all">
                            <i class="fa-solid fa-file-export text-blue-200"></i>
                            Export Report
                            <i class="fa-solid fa-chevron-down text-[8px] ml-1 transition-transform" :class="{'rotate-180': open}"></i>
                        </button>
                        
                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2"
                            class="absolute right-0 z-50 mt-3 w-60 origin-top-right rounded-3xl bg-white dark:bg-slate-800 shadow-2xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 overflow-hidden py-2" style="display: none;">
                            <a href="{{ route('manpower.export.excel', ['period' => $currentFilter ?? 'month']) }}" 
                                class="flex items-center px-5 py-4 text-sm font-bold text-slate-700 dark:text-gray-200 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                <div class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-green-600 mr-4">
                                    <i class="fa-solid fa-file-excel text-lg"></i>
                                </div>
                                Export to Excel
                            </a>
                            <a href="{{ route('manpower.export.pdf', ['period' => $currentFilter ?? 'month']) }}" 
                                class="flex items-center px-5 py-4 text-sm font-bold text-slate-700 dark:text-gray-200 hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-900/20 flex items-center justify-center text-rose-600 mr-4">
                                    <i class="fa-solid fa-file-pdf text-lg"></i>
                                </div>
                                Export to PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats/Summary Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div class="bg-white dark:bg-[#1E2129] p-7 rounded-[2rem] shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 hover:-translate-y-1 transition-all relative overflow-hidden group">
                    <div class="absolute -top-6 -right-6 w-24 h-24 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform"></div>
                    <div class="flex items-center gap-5 relative z-10">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 text-white rounded-2xl flex items-center justify-center text-3xl shadow-lg shadow-blue-500/30 shrink-0">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div class="overflow-hidden flex-1">
                            <p class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">พนักงานทั้งหมด</p>
                            <p class="text-4xl font-black text-slate-800 dark:text-white">{{ number_format($totalEmployees) }}</p>
                            <div class="flex items-center mt-2 text-[11px]">
                                <span class="font-black {{ $growthRate >= 0 ? 'text-emerald-500' : 'text-rose-500' }} flex items-center bg-{{ $growthRate >= 0 ? 'emerald' : 'rose' }}-50 dark:bg-{{ $growthRate >= 0 ? 'emerald' : 'rose' }}-900/20 px-2 py-0.5 rounded-full">
                                    <i class="fa-solid {{ $growthRate >= 0 ? 'fa-caret-up' : 'fa-caret-down' }} mr-1"></i>
                                    {{ number_format(abs($growthRate), 1) }}%
                                </span>
                                <span class="ml-2 text-slate-400 font-bold uppercase tracking-tighter">vs last month</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-white dark:bg-[#1E2129] p-7 rounded-[2rem] shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 hover:-translate-y-1 transition-all relative overflow-hidden group">
                    <div class="absolute -top-6 -right-6 w-24 h-24 bg-emerald-500/5 rounded-full group-hover:scale-150 transition-transform"></div>
                    <div class="flex items-center gap-5 relative z-10">
                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-emerald-600 text-white rounded-2xl flex items-center justify-center text-3xl shadow-lg shadow-emerald-500/30 shrink-0">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <div class="overflow-hidden flex-1">
                            <p class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">พนักงานใหม่ ({{ $currentFilter == 'year' ? 'ปีนี้' : ($currentFilter == 'quarter' ? 'ไตรมาสนี้' : 'เดือนนี้') }})</p>
                            <p class="text-4xl font-black text-slate-800 dark:text-white">{{ number_format($newHiresCount) }}</p>
                            <p class="text-[11px] text-slate-400 mt-2 font-bold italic">Welcome to the team!</p>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-white dark:bg-[#1E2129] p-7 rounded-[2rem] shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 hover:-translate-y-1 transition-all relative overflow-hidden group">
                    <div class="absolute -top-6 -right-6 w-24 h-24 bg-rose-500/5 rounded-full group-hover:scale-150 transition-transform"></div>
                    <div class="flex items-center gap-5 relative z-10">
                        <div class="w-16 h-16 bg-gradient-to-br from-rose-400 to-rose-600 text-white rounded-2xl flex items-center justify-center text-3xl shadow-lg shadow-rose-500/30 shrink-0">
                            <i class="fa-solid fa-user-minus"></i>
                        </div>
                        <div class="overflow-hidden flex-1">
                            <p class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">อัตราการลาออก</p>
                            <p class="text-4xl font-black text-slate-800 dark:text-white">{{ number_format($turnoverRate, 1) }}%</p>
                            <p class="text-[11px] text-slate-400 mt-2 font-bold uppercase tracking-tighter">{{ $resignationsCount }} resignations</p>
                        </div>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="bg-white dark:bg-[#1E2129] p-7 rounded-[2rem] shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 hover:-translate-y-1 transition-all relative overflow-hidden group">
                    <div class="absolute -top-6 -right-6 w-24 h-24 bg-amber-500/5 rounded-full group-hover:scale-150 transition-transform"></div>
                    <div class="flex items-center gap-5 relative z-10">
                        <div class="w-16 h-16 bg-gradient-to-br from-amber-400 to-amber-600 text-white rounded-2xl flex items-center justify-center text-3xl shadow-lg shadow-amber-500/30 shrink-0">
                            <i class="fa-solid fa-business-time"></i>
                        </div>
                        <div class="overflow-hidden flex-1">
                            <p class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">อายุงานเฉลี่ย</p>
                            <p class="text-4xl font-black text-slate-800 dark:text-white">{{ number_format($avgTenureYears, 1) }} <span class="text-lg font-normal text-slate-400 ml-1">ปี</span></p>
                            <p class="text-[11px] text-slate-400 mt-2 font-bold uppercase tracking-tighter">Retention rate</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts Section --}}
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                
                <!-- Main Charts Column -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white dark:bg-[#1E2129] p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 overflow-hidden">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                                    <div class="w-2 h-8 bg-blue-500 rounded-full"></div>
                                    พนักงานแยกตามฝ่าย (Division)
                                </h3>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1 ml-5">Distribution analysis</p>
                            </div>
                        </div>
                        <div id="divisionChart" class="w-full" style="min-height: 350px;"></div>
                    </div>

                    <div class="bg-white dark:bg-[#1E2129] p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                                    <div class="w-2 h-8 bg-indigo-500 rounded-full"></div>
                                    พนักงานแยกตามสายงาน (Section)
                                </h3>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1 ml-5">Detailed breakdown</p>
                            </div>
                        </div>
                        <div id="sectionChart" class="w-full" style="min-height: 350px;"></div>
                    </div>
                </div>

                <!-- Workplace & Gender -->
                <div class="space-y-8">
                    <div class="bg-white dark:bg-[#1E2129] p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5">
                        <h3 class="mb-8 text-xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                            <i class="fa-solid fa-location-dot text-rose-500"></i>
                            สถานที่ทำงาน
                        </h3>
                        <div id="workplaceChart" class="w-full" style="min-height: 300px;"></div>
                    </div>

                    <div class="bg-white dark:bg-[#1E2129] p-8 rounded-[2.5rem] shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5">
                        <h3 class="mb-8 text-xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                            <i class="fa-solid fa-venus-mars text-indigo-500"></i>
                            สัดส่วนเพศ
                        </h3>
                        <div id="genderChart" class="w-full" style="min-height: 280px;"></div>
                    </div>
                </div>
            </div>

            <!-- Monthly Hiring Trend Chart (Full Width) -->
            <div class="bg-white dark:bg-[#1E2129] p-10 rounded-[2.5rem] shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                            <i class="fa-solid fa-chart-line text-emerald-500"></i>
                            เปรียบเทียบพนักงานใหม่แต่ละเดือน (รายปี)
                        </h3>
                        <p class="text-slate-400 text-sm font-bold mt-2 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info italic text-[10px]"></i>
                            ข้อมูลเปรียบเทียบจำนวนการจ้างานรายเดือนของแต่ละปีงบประมาณ
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3" id="comparisonLegend"></div>
                </div>
                <div id="manpowerMonthlyComparisonChart" class="w-full" style="min-height: 420px;"></div>
            </div>

            {{-- Level Distribution (Full Width) --}}
            <div class="bg-white dark:bg-[#1E2129] p-10 rounded-[2.5rem] shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5">
                <h3 class="mb-10 text-2xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                    <i class="fa-solid fa-sitemap text-amber-500"></i>
                    โครงสร้างระดับพนักงาน (Level Hierarchy)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-16 gap-y-8">
                    @if($levelStats->count() > 0)
                        @php $maxLevel = $levelStats->max('count'); @endphp
                        @foreach($levelStats as $stat)
                        @php 
                            $width = ($stat->count / $maxLevel) * 100; 
                            $colorClass = 'from-blue-400 to-blue-600 shadow-blue-500/20';
                            if(str_contains($stat->color, 'error')) $colorClass = 'from-rose-400 to-rose-600 shadow-rose-500/20';
                            elseif(str_contains($stat->color, 'warning')) $colorClass = 'from-amber-400 to-amber-600 shadow-amber-500/20';
                            elseif(str_contains($stat->color, 'success')) $colorClass = 'from-emerald-400 to-emerald-600 shadow-emerald-500/20';
                            elseif(str_contains($stat->color, 'secondary')) $colorClass = 'from-indigo-400 to-indigo-600 shadow-indigo-500/20';
                        @endphp
                        <div class="group">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-black text-slate-500 dark:text-slate-400 group-hover:text-blue-500 transition-colors uppercase tracking-widest text-[11px]">
                                    {{ $stat->label }}
                                </span>
                                <span class="text-lg font-black text-slate-800 dark:text-white">
                                    {{ number_format($stat->count) }} <span class="text-[10px] font-bold text-slate-400 uppercase ml-1">People</span>
                                </span>
                            </div>
                            <div class="h-3 w-full bg-slate-100 dark:bg-slate-800/50 rounded-full overflow-hidden shadow-inner flex border border-slate-100 dark:border-white/5">
                                <div class="h-full bg-gradient-to-r {{ $colorClass }} rounded-full shadow-lg transform origin-left transition-all duration-1000 group-hover:scale-x-[1.02]" style="width: {{ $width }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="col-span-full text-center py-10 text-slate-400 italic">ไม่มีข้อมูลแสดงผล</div>
                    @endif
                </div>
            </div>

            {{-- Tables Section --}}
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                
                <!-- Recent Hires Table -->
                <div class="bg-white dark:bg-[#1E2129] rounded-[2.5rem] shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 dark:border-white/5 flex items-center justify-between">
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                            <i class="fa-solid fa-user-clock text-blue-500"></i>
                            พนักงานเข้าใหม่ล่าสุด
                        </h3>
                        <a href="{{ route('users.index') }}" class="text-xs font-black text-blue-500 hover:text-blue-600 uppercase tracking-widest bg-blue-50 dark:bg-blue-900/20 px-4 py-2 rounded-xl transition-colors">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 dark:bg-white/5">
                                    <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">พนักงาน</th>
                                    <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">วันที่เริ่มงาน</th>
                                    <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                                @forelse($recentHires as $hire)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors group">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-blue-500 group-hover:text-white transition-all">
                                                <i class="fa-solid fa-user"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-slate-800 dark:text-gray-200">{{ $hire->prefix_name }}{{ $hire->first_name }} {{ $hire->last_name }}</div>
                                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tight mt-0.5">{{ $hire->division_name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="text-xs font-bold text-slate-600 dark:text-slate-400">{{ $hire->start_date ? \Carbon\Carbon::parse($hire->start_date)->format('d M Y') : '-' }}</span>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 border border-emerald-100 dark:border-emerald-900/30">
                                            Active
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="px-8 py-10 text-center text-slate-400 italic">ไม่มีข้อมูลพนักงานใหม่</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Probation Table -->
                <div class="bg-white dark:bg-[#1E2129] rounded-[2.5rem] shadow-xl shadow-slate-200/50 dark:shadow-black/50 border border-slate-100 dark:border-white/5 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 dark:border-white/5 flex items-center justify-between">
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                            <i class="fa-solid fa-calendar-check text-amber-500"></i>
                            ครบกำหนดทดลองงาน
                        </h3>
                        <span class="bg-amber-50 dark:bg-amber-900/20 text-amber-600 text-[10px] font-black px-3 py-1.5 rounded-lg uppercase tracking-widest">Next 30 Days</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50/50 dark:bg-white/5">
                                    <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest">พนักงาน</th>
                                    <th class="px-6 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-center">วันครบกำหนด</th>
                                    <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-widest text-right">ระยะเวลา</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-white/5">
                                @forelse($probationUpcoming as $emp)
                                @php 
                                    $probationDate = \Carbon\Carbon::parse($emp->startwork_date)->addDays(119);
                                    $daysLeft = now()->diffInDays($probationDate, false);
                                @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-white/5 transition-colors group">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="w-11 h-11 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-amber-500 group-hover:text-white transition-all">
                                                <i class="fa-solid fa-hourglass-half"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-slate-800 dark:text-gray-200">{{ $emp->fullname }}</div>
                                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-tight mt-0.5">Emp ID: {{ $emp->employee_code }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="text-xs font-black text-rose-500 dark:text-rose-400">{{ $probationDate->format('d M Y') }}</span>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest {{ $daysLeft <= 7 ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-600' : 'bg-amber-50 dark:bg-amber-900/20 text-amber-600' }}">
                                            {{ $daysLeft }} วัน
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="px-8 py-10 text-center text-slate-400 italic">ไม่มีพนักงานครบกำหนดในช่วงนี้</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#94a3b8' : '#64748b';
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';

            const baseOptions = {
                chart: {
                    parentHeightOffset: 0,
                    toolbar: { show: false },
                    fontFamily: 'Prompt, sans-serif'
                },
                theme: { mode: isDark ? 'dark' : 'light' },
                grid: { borderColor: gridColor },
                xaxis: { labels: { style: { colors: textColor, fontWeight: 600 } } },
                yaxis: { labels: { style: { colors: textColor, fontWeight: 600 } } }
            };

            // 1. Division Distribution (Column)
            new ApexCharts(document.querySelector("#divisionChart"), {
                ...baseOptions,
                series: [{ name: 'จำนวนพนักงาน', data: @json($divData) }],
                chart: { ...baseOptions.chart, type: 'bar', height: 350 },
                colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                plotOptions: { bar: { borderRadius: 8, columnWidth: '50%', distributed: true } },
                xaxis: { ...baseOptions.xaxis, categories: @json($divLabels) },
                legend: { show: false }
            }).render();

            // 2. Section Distribution (Horizontal Bar)
            new ApexCharts(document.querySelector("#sectionChart"), {
                ...baseOptions,
                series: [{ name: 'จำนวนพนักงาน', data: @json($secData) }],
                chart: { ...baseOptions.chart, type: 'bar', height: 350 },
                colors: ['#6366f1', '#f43f5e', '#10b981', '#eab308', '#8b5cf6'],
                plotOptions: { bar: { horizontal: true, borderRadius: 6, barHeight: '60%', distributed: true } },
                xaxis: { ...baseOptions.xaxis, categories: @json($secLabels) },
                legend: { show: false }
            }).render();

            // 3. Workplace Distribution (Donut)
            new ApexCharts(document.querySelector("#workplaceChart"), {
                ...baseOptions,
                series: @json($wpData),
                chart: { ...baseOptions.chart, type: 'donut', height: 300 },
                labels: @json($wpLabels),
                colors: ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981'],
                stroke: { show: false },
                legend: { position: 'bottom', labels: { colors: textColor } },
                plotOptions: { pie: { donut: { size: '75%' } } }
            }).render();

            // 4. Gender Distribution (Donut)
            new ApexCharts(document.querySelector("#genderChart"), {
                ...baseOptions,
                series: [{{ $maleCount }}, {{ $femaleCount }}],
                chart: { ...baseOptions.chart, type: 'donut', height: 280 },
                labels: ['Male', 'Female'],
                colors: ['#3b82f6', '#ec4899'],
                stroke: { show: false },
                legend: { position: 'bottom', labels: { colors: textColor } },
                plotOptions: { pie: { donut: { size: '70%', labels: { show: true, total: { show: true, color: textColor } } } } }
            }).render();

            // 5. Monthly Hiring Trend (Area)
            const yearlyData = @json($yearlyComparisonData);
            const years = Object.keys(yearlyData);
            const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            const series = years.map(year => ({ name: 'ปี ' + year, data: yearlyData[year] }));

            new ApexCharts(document.querySelector("#manpowerMonthlyComparisonChart"), {
                ...baseOptions,
                series: series,
                chart: { ...baseOptions.chart, type: 'area', height: 420, stacked: false },
                colors: ['#3b82f6', '#10b981', '#f59e0b'],
                stroke: { curve: 'smooth', width: 3 },
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [20, 100] } },
                xaxis: { ...baseOptions.xaxis, categories: months },
                legend: { show: true, position: 'top', horizontalAlign: 'right', labels: { colors: textColor } },
                markers: { size: 5, hover: { size: 7 } }
            }).render();
        });
    </script>
    @endpush
@endsection
