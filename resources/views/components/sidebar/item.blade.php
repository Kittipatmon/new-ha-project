@props(['href', 'active' => false, 'badge' => null])

<a href="{{ $href }}"
    class="flex justify-between items-center px-3 py-2 my-0.5 rounded-lg text-[13px] font-medium transition-all duration-150 {{ $active ? 'bg-gradient-to-r from-indigo-500 to-indigo-600 text-white shadow-md shadow-indigo-500/25 font-semibold' : 'text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100/70 dark:hover:bg-slate-800/50' }}">
    <div class="flex items-center gap-2.5 truncate">
        <span class="w-1.5 h-1.5 rounded-full {{ $active ? 'border-2 border-white bg-transparent ring-1 ring-white/30' : 'border border-slate-400 dark:border-slate-500 bg-transparent' }} shrink-0"></span>
        <span class="truncate">{{ $slot }}</span>
    </div>
    @if($badge)
        <span class="px-2 py-0.5 text-[11px] font-bold rounded-full {{ $active ? 'bg-white text-indigo-600' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300' }} ml-2 shrink-0">{{ $badge }}</span>
    @endif
</a>

