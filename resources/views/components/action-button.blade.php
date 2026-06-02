@props(['action', 'color' => 'neutral', 'icon' => '', 'href' => null])

@php
    $colorClasses = [
        'primary' => 'text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/30',
        'success' => 'text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-900/30',
        'warning' => 'text-amber-500 hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-amber-900/30',
        'error' => 'text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30',
        'info' => 'text-blue-500 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/30',
        'neutral' => 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800',
    ][$color] ?? 'text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800';
    
    $commonClasses = "inline-flex items-center justify-center rounded-lg p-2 min-h-[44px] min-w-[44px] transition-colors $colorClasses";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $commonClasses, 'aria-label' => $action]) }} title="{{ $action }}">
        @if($icon)
            <i class="fa-solid fa-{{ $icon }}"></i>
        @endif
        <span class="sr-only">{{ $action }}</span>
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => $commonClasses, 'aria-label' => $action]) }} title="{{ $action }}">
        @if($icon)
            <i class="fa-solid fa-{{ $icon }}"></i>
        @endif
        <span class="sr-only">{{ $action }}</span>
    </button>
@endif
