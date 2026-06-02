@props(['href', 'active' => false, 'badge' => null])

<a href="{{ $href }}"
    class="flex justify-between items-center px-3 py-2 rounded-lg text-sm transition-colors {{ $active ? 'bg-gray-800/80 text-kumwell-red font-bold shadow-sm' : 'text-gray-500 hover:text-kumwell-red hover:bg-gray-800/50' }}">
    <span>- {{ $slot }}</span>
    @if($badge)
        <span class="badge badge-error badge-sm text-white font-bold ml-2">{{ $badge }}</span>
    @endif
</a>
