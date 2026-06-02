@props(['color' => 'neutral', 'label'])

@php
    // Bright, vibrant & modern light tones matching modern design systems
    $colorClasses = [
        'success'   => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-500/20 dark:text-emerald-300 dark:border-emerald-500/30',
        'error'     => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-500/20 dark:text-rose-300 dark:border-rose-500/30',
        'warning'   => 'bg-amber-100 text-amber-900 border-amber-200 dark:bg-amber-500/20 dark:text-amber-300 dark:border-amber-500/30',
        'info'      => 'bg-sky-100 text-sky-800 border-sky-200 dark:bg-sky-500/20 dark:text-sky-300 dark:border-sky-500/30',
        'neutral'   => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-500/20 dark:text-slate-300 dark:border-slate-500/30',
        'primary'   => 'bg-red-100 text-red-800 border-red-200 dark:bg-red-500/20 dark:text-red-300 dark:border-red-500/30',
        'secondary' => 'bg-indigo-100 text-indigo-800 border-indigo-200 dark:bg-indigo-500/20 dark:text-indigo-300 dark:border-indigo-500/30',
        'accent'    => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-500/20 dark:text-purple-300 dark:border-purple-500/30',
        'base-100'  => 'bg-teal-100 text-teal-800 border-teal-200 dark:bg-teal-500/20 dark:text-teal-300 dark:border-teal-500/30',
        'base-200'  => 'bg-orange-100 text-orange-800 border-orange-200 dark:bg-orange-500/20 dark:text-orange-300 dark:border-orange-500/30',
    ][$color] ?? 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-500/20 dark:text-slate-300 dark:border-slate-500/30';

    // Circular indicator dot colors
    $dotClasses = [
        'success'   => 'bg-emerald-500',
        'error'     => 'bg-rose-500',
        'warning'   => 'bg-amber-500',
        'info'      => 'bg-sky-500',
        'neutral'   => 'bg-slate-500',
        'primary'   => 'bg-red-500',
        'secondary' => 'bg-indigo-500',
        'accent'    => 'bg-purple-500',
        'base-100'  => 'bg-teal-500',
        'base-200'  => 'bg-orange-500',
    ][$color] ?? 'bg-slate-500';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded border transition-all duration-150 $colorClasses"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $dotClasses }} shrink-0"></span>
    <span>{{ $label }}</span>
</span>
