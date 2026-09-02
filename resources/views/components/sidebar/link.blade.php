@props(['href', 'title', 'icon', 'active' => false])

<div class="relative group">
    <a href="{{ $href }}"
        class="w-full flex items-center px-3 py-2.5 rounded-xl transition-all duration-200 {{ $active ? 'bg-white/5 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white' }}">
        <div class="flex items-center">
            <i class="fa-solid fa-{{ $icon }} text-sm w-6 text-center transition-colors mr-3 {{ $active ? 'text-kumwell-red' : '' }}"></i>
            <span class="sidebar-text font-medium text-sm">{{ $title }}</span>
        </div>
    </a>

    <div class="tooltip absolute left-full top-1/2 -translate-y-1/2 ml-3 bg-gray-900 text-white text-xs font-medium px-3 py-1.5 rounded-lg invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 pointer-events-none z-[9999] whitespace-nowrap shadow-2xl border border-gray-700/90 backdrop-blur-sm">
        {{ $title }}
    </div>
</div>

