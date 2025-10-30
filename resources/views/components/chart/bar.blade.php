@props([
    'chartId',
    'chartKey' => null,
    'title',
    'description' => null,
    'emptyMessage' => 'No data available for this range.',
    'loading' => false,
])

@php
    $resolvedKey = $chartKey ?? $chartId;
@endphp

<section
    {{ $attributes->merge(['class' => 'rounded-2xl border border-slate-800/80 bg-slate-900/70 shadow-lg shadow-slate-950/20']) }}
    data-bar-chart
    data-chart-id="{{ $chartId }}"
    data-chart-key="{{ $resolvedKey }}"
    data-empty-message="{{ $emptyMessage }}"
    data-initial-loading="{{ $loading ? 'true' : 'false' }}"
>
    <header class="flex flex-col gap-4 border-b border-slate-800/70 px-6 py-5 md:flex-row md:items-center md:justify-between">
        <div class="space-y-1.5">
            <h2 class="text-lg font-semibold tracking-tight text-slate-100">{{ $title }}</h2>

            @if ($description)
                <p class="text-sm text-slate-400">{{ $description }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-300">
                {{ $actions }}
            </div>
        @endisset
    </header>

    <div class="relative h-72 w-full">
        <div class="absolute inset-0 hidden items-center justify-center text-sm text-slate-400" data-bar-chart-loading>
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-400"></span>
                <span>Loading data…</span>
            </div>
        </div>

        <div class="absolute inset-0 hidden items-center justify-center px-6 text-center text-sm text-slate-400" data-bar-chart-message></div>

        <canvas data-bar-chart-canvas class="h-full w-full"></canvas>
    </div>

    @if (trim($slot) !== '')
        <footer class="border-t border-slate-800/70 px-6 py-4 text-sm text-slate-400">
            {{ $slot }}
        </footer>
    @endif
</section>
