@props(['id', 'title', 'icon', 'active' => false])

<div class="relative group">
    <button onclick="toggleDropdown('dropdown-{{ $id }}')"
        class="w-full flex items-center justify-between px-3 py-1 rounded-xl transition-all duration-200 {{ $active ? 'bg-white/5 text-white shadow-sm' : 'text-gray-400 hover:bg-gray-800/50 hover:text-white' }}"
        id="btn-{{ $id }}"
        aria-expanded="{{ $active ? 'true' : 'false' }}"
        aria-controls="dropdown-{{ $id }}">
        <div class="flex items-center">
            <i id="icon-{{ $id }}" class="fa-solid fa-{{ $icon }} text-sm w-6 text-center transition-colors mr-3 {{ $active ? 'text-kumwell-red' : '' }}"></i>
            <span class="sidebar-text">{{ $title }}</span>
        </div>
        <i id="arrow-{{ $id }}"
            class="sidebar-text fa-solid fa-chevron-down text-xs transition-transform duration-200 {{ $active ? 'rotate-180' : '' }}"></i>
    </button>

    <div id="dropdown-{{ $id }}" class="{{ $active ? '' : 'hidden' }} pl-10 pr-2 py-1 space-y-1 transition-all duration-300" role="region" aria-labelledby="btn-{{ $id }}">
        {{ $slot }}
    </div>

    <div class="tooltip absolute left-14 top-2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 transition-opacity pointer-events-none z-50 whitespace-nowrap ml-2 shadow-md border border-gray-700 hidden">
        {{ $title }}
    </div>
</div>
