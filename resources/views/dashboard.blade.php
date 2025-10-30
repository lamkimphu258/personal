@extends('layouts.app')

@section('title', 'Dashboard | ' . config('app.name', 'Nutrition Companion'))

@section('body-class', 'bg-slate-950 text-slate-100 min-h-screen')

@section('content')
    @php
        $current = $widgets['currentWeight'];
        $target = $widgets['targetWeight'];
    @endphp

    <div
        class="mx-auto w-full max-w-6xl px-4 py-10 lg:px-6"
        data-dashboard-root
        data-dashboard-endpoint="{{ route('dashboard.data') }}"
        data-initial-range='@json($range)'
        data-initial-data='@json($dashboardData)'
    >
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-slate-100">Dashboard</h1>
                <p class="mt-2 max-w-xl text-sm text-slate-400">
                    Track your progress at a glance. Adjust the filters to review weight trends, calorie discipline, and your most frequent foods.
                </p>
            </div>
            <div class="text-sm text-slate-400" data-range-summary>
                Showing {{ $range['start'] }} to {{ $range['end'] }}
            </div>
        </div>

        <div class="mt-8 grid gap-5 md:grid-cols-2">
            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold tracking-tight text-slate-200">Current Weight</h2>
                    @if ($current['hasValue'])
                        <div @class([
                            'flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium',
                            'bg-emerald-400/10 text-emerald-300' => $current['direction'] === 'down',
                            'bg-rose-500/10 text-rose-300' => $current['direction'] === 'up',
                            'bg-slate-700/60 text-slate-200' => $current['direction'] === 'flat',
                        ])>
                            @if ($current['direction'] === 'down')
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M3 5a1 1 0 1 1 2 0v6.586l3.293-3.293a1 1 0 0 1 1.414 0L13 12.586V6a1 1 0 1 1 2 0v9a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5Z" clip-rule="evenodd" />
                                </svg>
                                <span>Weight down</span>
                            @elseif ($current['direction'] === 'up')
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M17 15a1 1 0 1 1-2 0V8.414l-3.293 3.293a1 1 0 0 1-1.414 0L7 7.414V14a1 1 0 1 1-2 0V5a1 1 0 0 1 1-1h9a1 1 0 0 1 1 1v10Z" clip-rule="evenodd" />
                                </svg>
                                <span>Weight up</span>
                            @else
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M4 10a1 1 0 0 1 1-1h10a1 1 0 1 1 0 2H5a1 1 0 0 1-1-1Z" />
                                </svg>
                                <span>{{ $current['hasComparison'] ? 'No change' : 'No previous data' }}</span>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="mt-6 space-y-4">
                    @if ($current['hasValue'])
                        <div class="text-4xl font-semibold tracking-tight text-slate-50">
                            {{ number_format((float) $current['weight'], 1) }} <span class="text-lg font-medium text-slate-400">kg</span>
                        </div>
                        <div class="text-sm text-slate-400">
                            Recorded on {{ $current['latestRecordedAt'] }}
                        </div>
                        @if ($current['hasComparison'] && $current['delta'] !== null)
                            <div class="text-sm text-slate-300">
                                {{ $current['direction'] === 'down' ? 'Down' : ($current['direction'] === 'up' ? 'Up' : 'Flat change of') }}
                                {{ number_format((float) $current['delta'], 1) }} kg since {{ $current['previousRecordedAt'] }}
                            </div>
                        @elseif (! $current['hasComparison'])
                            <div class="text-sm text-slate-400">
                                Record another weight to see changes over time.
                            </div>
                        @endif
                    @else
                        <div class="space-y-3">
                            <p class="text-sm text-slate-400">No weight entries yet.</p>
                            <a
                                href="{{ route('tracking.index') }}"
                                class="inline-flex items-center justify-center gap-2 rounded-md border border-emerald-400/60 bg-emerald-500/10 px-3 py-2 text-sm font-medium text-emerald-200 transition hover:bg-emerald-500/20"
                            >
                                Record today&rsquo;s weight
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-slate-800/80 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold tracking-tight text-slate-200">Target Weight</h2>
                </div>

                <div class="mt-6 space-y-4">
                    @if ($target['hasProfile'] && $target['goalWeight'] !== null)
                        <div class="text-4xl font-semibold tracking-tight text-slate-50">
                            {{ number_format((float) $target['goalWeight'], 1) }} <span class="text-lg font-medium text-slate-400">kg</span>
                        </div>
                        <div class="text-sm text-slate-400">
                            Update your profile if your goals change.
                        </div>
                        <a
                            href="{{ route('profile.edit') }}"
                            class="inline-flex items-center gap-2 text-sm font-medium text-emerald-200 underline underline-offset-4 transition hover:text-emerald-100"
                        >
                            Open nutrition profile
                        </a>
                    @else
                        <div class="space-y-3">
                            <p class="text-sm text-slate-400">
                                Set a nutrition profile to track progress toward your goal weight.
                            </p>
                            <a
                                href="{{ route('profile.edit') }}"
                                class="inline-flex items-center justify-center gap-2 rounded-md border border-emerald-400/60 bg-emerald-500/10 px-3 py-2 text-sm font-medium text-emerald-200 transition hover:bg-emerald-500/20"
                            >
                                Complete profile
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-10 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex flex-wrap gap-2">
                @php
                    $presets = [
                        'week' => 'Week',
                        'month' => 'Month',
                        'year' => 'Year',
                    ];
                @endphp
                @foreach ($presets as $key => $label)
                    <button
                        type="button"
                        class="rounded-full border px-4 py-2 text-sm font-medium transition data-[active=true]:border-emerald-400/60 data-[active=true]:bg-emerald-500/10 data-[active=true]:text-emerald-200 data-[active=false]:border-slate-800 data-[active=false]:text-slate-300 data-[active=false]:hover:border-slate-700 data-[active=false]:hover:text-slate-100"
                        data-range-button
                        data-preset="{{ $key }}"
                        data-active="{{ $range['preset'] === $key ? 'true' : 'false' }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <form class="flex flex-wrap items-center gap-3 text-sm text-slate-300" data-custom-range-form>
                <label class="flex items-center gap-2">
                    <span class="text-slate-400">Start</span>
                    <input
                        type="date"
                        name="start"
                        value="{{ $range['start'] }}"
                        class="rounded-md border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0"
                    >
                </label>
                <label class="flex items-center gap-2">
                    <span class="text-slate-400">End</span>
                    <input
                        type="date"
                        name="end"
                        value="{{ $range['end'] }}"
                        class="rounded-md border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0"
                    >
                </label>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-md border border-slate-700 bg-slate-800 px-3 py-2 text-sm font-medium text-slate-200 transition hover:border-emerald-400/60 hover:bg-emerald-500/10 hover:text-emerald-100"
                >
                    Apply
                </button>
            </form>
        </div>

        <div class="mt-10 space-y-8">
            <x-chart.line
                chart-id="weight-trend"
                :chart-key="'weight-trend'"
                title="Weight Trend"
                description="Track how your weight moves within the selected range."
                empty-message="No weight entries in this range yet."
            />

            <x-chart.line
                chart-id="calorie-adherence"
                :chart-key="'calorie-adherence'"
                title="Calorie Goal Adherence"
                description="A value of 1 means you hit your calorie target for the day, while 0 means the target was exceeded."
                empty-message="No calorie data for this range."
            />

            <x-chart.line
                chart-id="todo-completions"
                :chart-key="'todo-completions'"
                title="Completed Tasks"
                description="See how many todo occurrences you finished each day within the selected range."
                empty-message="No completed todos in this range yet. Visit the Todo page to start finishing tasks."
            />

            <x-chart.bar
                chart-id="top-foods"
                :chart-key="'top-foods'"
                title="Top Foods"
                description="Horizontal bars rank the foods you logged most often in the selected range."
                empty-message="No food entries recorded in this range."
            />
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module">
        window.dashboardConfig = {
            endpoint: @json(route('dashboard.data')),
            initialRange: @json($range),
            initialData: @json($dashboardData),
        };
    </script>
@endpush
