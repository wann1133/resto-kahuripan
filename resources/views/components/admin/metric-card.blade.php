@props([
    'title',
    'value',
    'trend' => null,
    'color' => 'emerald',
    'icon' => 'spark',
])

@php
    $colorMap = [
        'emerald' => ['bg' => 'bg-emerald-500', 'soft' => 'bg-emerald-100/70', 'text' => 'text-emerald-600'],
        'sky' => ['bg' => 'bg-sky-500', 'soft' => 'bg-sky-100/70', 'text' => 'text-sky-600'],
        'violet' => ['bg' => 'bg-violet-500', 'soft' => 'bg-violet-100/70', 'text' => 'text-violet-600'],
        'amber' => ['bg' => 'bg-amber-500', 'soft' => 'bg-amber-100/70', 'text' => 'text-amber-600'],
    ];
    $palette = $colorMap[$color] ?? $colorMap['emerald'];

    $icons = [
        'table' => '<path d="M3.75 5.75a1 1 0 0 1 1-1h14.5a1 1 0 0 1 1 1v2.5a1 1 0 0 1-1 1h-.75v7a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-7H9.5v7a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-7H4.75a1 1 0 0 1-1-1z" />',
        'receipt' => '<path d="M7 3.5h10.25a1 1 0 0 1 1 1V19l-2.5-1.5L13.25 19l-2.5-1.5L8.25 19 5.75 17.5 3.25 19V4.5a1 1 0 0 1 1-1z" /><path d="M7.75 8.25h8.5" /><path d="M7.75 11.25h8.5" /><path d="M7.75 14.25h4.5" />',
        'cash' => '<path d="M3.75 7.5h16.5a1.25 1.25 0 0 1 1.25 1.25v6.5a1.25 1.25 0 0 1-1.25 1.25H3.75A1.25 1.25 0 0 1 2.5 15.25v-6.5A1.25 1.25 0 0 1 3.75 7.5Z" /><path d="M6 10.5h.01" /><path d="M18 13.5h-.01" /><path d="M12 10a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5Z" />',
        'menu' => '<path d="M5.5 6.25h13" /><path d="M5.5 10h9" /><path d="M5.5 13.75h13" /><path d="M5.5 17.5h9" />',
        'spark' => '<path d="M11.25 4.5v3" /><path d="M16.25 8.75l-2.25 1.5" /><path d="M6.25 8.75 8.5 10.25" /><path d="M13 18.5 12 15.75" /><path d="M9.5 18.5 10.5 15.75" /><path d="M11.25 13l-2.5-1.5 2.5-1.5 2.5 1.5-2.5 1.5Z" />',
    ];
    $iconPath = $icons[$icon] ?? $icons['spark'];
@endphp

<div class="rounded-3xl border border-white/60 bg-white/90 p-6 shadow-sm shadow-slate-900/5">
    <div class="flex items-center justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-[0.25em] text-slate-400">{{ $title }}</p>
            <p class="mt-3 text-2xl font-semibold text-slate-800">{{ $value }}</p>
            @if ($trend)
                <p class="mt-2 text-xs font-medium text-slate-400">{{ $trend }}</p>
            @endif
        </div>
        <span class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $palette['soft'] }}">
            <svg class="h-6 w-6 {{ $palette['text'] }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">{!! $iconPath !!}</svg>
        </span>
    </div>
</div>
