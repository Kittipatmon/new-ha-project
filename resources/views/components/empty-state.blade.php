@props([
    'icon' => 'inbox',
    'title' => 'ไม่พบข้อมูล',
    'description' => 'ยังไม่มีข้อมูลในระบบ หรือไม่พบข้อมูลตามเงื่อนไขที่ค้นหา',
    'actionUrl' => null,
    'actionText' => 'เพิ่มข้อมูลใหม่',
    'actionIcon' => 'plus'
])

<div class="flex flex-col items-center justify-center py-16 px-4 text-center">
    <div class="w-24 h-24 mb-6 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center shadow-inner border border-gray-100 dark:border-gray-700">
        <i class="fa-solid fa-{{ $icon }} text-4xl text-gray-400 dark:text-gray-500"></i>
    </div>
    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">{{ $title }}</h3>
    <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto mb-6">{{ $description }}</p>
    
    @if($actionUrl)
        <a href="{{ $actionUrl }}" class="btn btn-primary bg-kumwell-red hover:bg-red-700 border-none text-white shadow-lg shadow-red-900/20">
            <i class="fa-solid fa-{{ $actionIcon }} mr-2"></i>
            {{ $actionText }}
        </a>
    @endif
</div>
