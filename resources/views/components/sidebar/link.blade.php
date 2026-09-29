@props(['href', 'title', 'icon', 'active' => false])

<div class="relative group my-0.5">
    <a href="{{ $href }}"
        class="w-full flex items-center px-3.5 py-2.5 rounded-lg text-[13.5px] font-medium transition-all duration-200 {{ $active ? 'bg-gradient-to-r from-indigo-500 to-indigo-600 text-white shadow-md shadow-indigo-500/25' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}">
        <div class="flex items-center min-w-0">
            @php
                if (str_starts_with($icon, 'fa-') || str_contains($icon, ' ')) {
                    $iconClass = $icon;
                } elseif ($icon === 'microsoft') {
                    $iconClass = 'fa-brands fa-microsoft';
                } else {
                    $iconClass = 'fa-solid fa-' . $icon;
                }
            @endphp
            <i class="{{ $iconClass }} text-base w-6 text-center transition-colors mr-3 shrink-0 {{ $active ? 'text-white' : 'text-slate-500 dark:text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-200' }}"></i>
            <span class="sidebar-text truncate">{{ $title }}</span>
        </div>
    </a>

    <div class="tooltip absolute left-full top-1/2 -translate-y-1/2 ml-3 bg-slate-900 text-white text-xs font-medium px-3 py-1.5 rounded-lg invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 pointer-events-none z-[9999] whitespace-nowrap shadow-xl border border-slate-700">
        {{ $title }}
    </div>
</div>

