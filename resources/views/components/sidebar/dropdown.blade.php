@props(['id', 'title', 'icon', 'active' => false])

<div class="relative group my-0.5">
    <button onclick="toggleDropdown('dropdown-{{ $id }}')"
        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-[13.5px] font-medium transition-all duration-200 {{ $active ? 'bg-slate-100/90 dark:bg-slate-800/80 text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100/80 dark:hover:bg-slate-800/60 hover:text-slate-900 dark:hover:text-white' }}"
        id="btn-{{ $id }}"
        aria-expanded="{{ $active ? 'true' : 'false' }}"
        aria-controls="dropdown-{{ $id }}">
        <div class="flex items-center min-w-0">
            <i id="icon-{{ $id }}" class="fa-solid fa-{{ $icon }} text-base w-6 text-center transition-colors mr-3 shrink-0 {{ $active ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-500 dark:text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-200' }}"></i>
            <span class="sidebar-text truncate">{{ $title }}</span>
        </div>
        <span class="sidebar-text ml-2 shrink-0">
            <i id="arrow-{{ $id }}"
                class="fa-solid fa-chevron-right text-[11px] text-slate-400 transition-transform duration-200"></i>
        </span>
    </button>

    <div id="dropdown-{{ $id }}" class="dropdown-menu-list hidden pl-6 pr-1 py-1 space-y-0.5 transition-all duration-300" role="region" aria-labelledby="btn-{{ $id }}">
        {{ $slot }}
    </div>

    <div class="tooltip absolute left-full top-1/2 -translate-y-1/2 ml-3 bg-slate-900 text-white text-xs font-medium px-3 py-1.5 rounded-lg invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 pointer-events-none z-[9999] whitespace-nowrap shadow-xl border border-slate-700">
        {{ $title }}
    </div>
</div>

