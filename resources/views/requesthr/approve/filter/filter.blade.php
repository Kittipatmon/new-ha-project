<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-3">
    <div class="relative group">
        <label for="" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase">
            กรอกคำค้นหา
        </label>
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="ค้นหาเลขที่คำร้อง ชื่อ-สกุล หัวข้อคำร้อง"
            class="input input-bordered w-full rounded-lg border-gray-300  dark:bg-gray-700 dark:border-gray-600 placeholder-gray-400">
    </div>
    <div class="relative group">
        <label for="section" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase">
            สายงาน
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 group-hover:text-red-500 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 3h-1a2 2 0 00-2 2v1H9V5a2 2 0 00-2-2H6a2 2 0 00-2 2v1h16V5a2 2 0 00-2-2z">
                    </path>
                </svg>
            </div>
            <select id="section" name="section"
                class="select select-bordered w-full rounded-lg border-gray-300  dark:bg-gray-700 dark:border-gray-600">
                <option value="">ทั้งหมด</option>
                @foreach ($sections as $section)
                <option value="{{ $section->section_id }}"
                    {{ request('section')==$section->section_id ? 'selected' : '' }}>
                    {{ $section->section_code }} ({{ $section->section_name }})
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="relative group">
        <label for="division" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase">
            ฝ่าย
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 group-hover:text-red-500 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 3h-1a2 2 0 00-2 2v1H9V5a2 2 0 00-2-2H6a2 2 0 00-2 2v1h16V5a2 2 0 00-2-2z">
                    </path>
                </svg>
            </div>
            <select id="division" name="division"
                class="select select-bordered w-full rounded-lg border-gray-300  dark:bg-gray-700 dark:border-gray-600">
                <option value="">ทั้งหมด</option>
                @foreach ($divisions as $division)
                <option value="{{ $division->division_id }}"
                    {{ request('division')==$division->division_id ? 'selected' : '' }}>
                    {{ $division->division_name }} ({{ $division->division_fullname }})
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="relative group">
        <label for="department" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase">
            แผนก
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 group-hover:text-red-500 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 3h-1a2 2 0 00-2 2v1H9V5a2 2 0 00-2-2H6a2 2 0 00-2 2v1h16V5a2 2 0 00-2-2z">
                    </path>
                </svg>
            </div>
            <select id="department" name="department"
                class="select select-bordered w-full rounded-lg border-gray-300  dark:bg-gray-700 dark:border-gray-600">
                <option value="">ทั้งหมด</option>
                @foreach ($departments as $department)
                <option value="{{ $department->department_id }}"
                    {{ request('department')==$department->department_id ? 'selected' : '' }}>
                    {{ $department->department_name }} ({{ $department->department_fullname }})
                </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 xl:grid-cols-6 gap-3 mt-3">
    <div class="relative group">
        <label for="category"
            class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase">หมวดหมู่</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 group-hover:text-red-500 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                    </path>
                </svg>
            </div>
            <select id="category" name="category"
                class="select select-bordered w-full rounded-lg border-gray-300  dark:bg-gray-700 dark:border-gray-600">
                <option value="">ทั้งหมด</option>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}" {{ request('category')==$category->id ? 'selected' : '' }}>
                    {{ $category->name_th }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="relative group">
        <label for="type"
            class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase">ประเภท</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 group-hover:text-red-500 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                    </path>
                </svg>
            </div>
            <select id="type" name="type"
                class="select select-bordered w-full rounded-lg border-gray-300  dark:bg-gray-700 dark:border-gray-600">
                <option value="">ทั้งหมด</option>
                @foreach ($types as $type)
                <option value="{{ $type->id }}" {{ request('type')==$type->id ? 'selected' : '' }}>
                    {{ $type->name_th }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="relative group">
        <label for="subtype"
            class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase">ประเภทย่อย</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 group-hover:text-red-500 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                    </path>
                </svg>
            </div>
            <select id="subtype" name="subtype"
                class="select select-bordered w-full rounded-lg border-gray-300  dark:bg-gray-700 dark:border-gray-600">
                <option value="">ทั้งหมด</option>
                @foreach ($subtypes as $subtype)
                <option value="{{ $subtype->id }}" {{ request('subtype')==$subtype->id ? 'selected' : '' }}>
                    {{ $subtype->name_th }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="relative group">
        <label for="status"
            class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase">สถานะ</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 group-hover:text-red-500 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <select id="status" name="status"
                class="select select-bordered w-full rounded-lg border-gray-300  dark:bg-gray-700 dark:border-gray-600">
                <option value="">ทั้งหมด</option>
                @foreach ($statuses as $status)
                <option value="{{ $status['id'] }}" {{ request('status')==$status['id'] ? 'selected' : '' }}>
                    {{ $status['name'] }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="relative group">
        <label for="start_date"
            class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase">วันที่เริ่มต้น</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 group-hover:text-red-500 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
            </div>
            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                class="input pika-single w-full rounded-lg border-gray-300  dark:bg-gray-700 dark:border-gray-600 placeholder-gray-400">
        </div>
    </div>

    <div class="relative group">
        <label for="end_date"
            class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase">วันที่สิ้นสุด</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 group-hover:text-red-500 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
            </div>
            <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                class="input input-bordered w-full rounded-lg border-gray-300  dark:bg-gray-700 dark:border-gray-600 placeholder-gray-400">
        </div>
    </div>
</div>

<div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between space-x-3">
    <div class="flex space-x-3">
        <a href="{{ route('approve.approvehrlistall.export', request()->query()) }}"
            class="btn btn-success btn-outline">
            <i class="fas fa-file-excel mr-1"></i>
            Excel
        </a>
        <a href="{{ route('approve.approvehrlistall.pdf', request()->query()) }}" target="_blank"
            class="btn btn-error btn-outline">
            <i class="fas fa-file-pdf mr-1"></i>
            PDF
        </a>
    </div>
    <div class="flex space-x-3">

        <a href="{{ route('approve.approvehrlistall') }}"
            class="btn btn-secondary btn-outline border border-gray-300 rounded-lg gap-2 px-5 shadow-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                </path>
            </svg>
            ล้างค่า
        </a>
        <button type="submit"
            class="btn btn-primary btn-outline rounded-lg gap-2 px-6 shadow-md hover:shadow-lg transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            ค้นหาข้อมูล
        </button>

    </div>
</div>