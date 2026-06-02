@props(['id', 'title', 'icon' => ''])

<div id="{{ $id }}" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/50 transition-opacity">
    
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 w-full max-w-lg transform transition-all scale-100">
        
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 id="{{ $id }}-title" class="text-lg font-bold text-gray-800 dark:text-gray-100">
                @if($icon)
                    <i class="fa-solid fa-{{ $icon }} mr-2"></i>
                @endif
                {{ $title }}
            </h2>
            
            <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors" data-close-modal="{{ $id }}" aria-label="Close modal">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        
        <div class="px-6 py-4">
            {{ $slot }}
        </div>
        
    </div>
</div>
