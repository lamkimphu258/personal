@extends('layouts.app')

@section('title', $template->name . ' | Food Templates | ' . config('app.name', 'Nutrition Companion'))

@section('content')
    <div class="mx-auto w-full max-w-6xl px-4 py-12">
        <a href="{{ route('food-templates.index') }}" class="text-sm text-emerald-300 hover:underline">&larr; Back to templates</a>

        <div class="mt-6 space-y-6 rounded-xl border border-slate-800 bg-slate-900/70 p-6">
            <header class="space-y-2">
                <h1 class="text-3xl font-semibold tracking-tight">{{ $template->name }}</h1>
                @if ($template->description)
                    <p class="text-sm text-slate-300">{{ $template->description }}</p>
                @endif
            </header>

            <dl class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-4">
                    <dt class="text-xs uppercase tracking-wide text-slate-400">Calories</dt>
                    <dd class="mt-1 text-xl font-semibold text-slate-100">{{ number_format($template->calories) }}</dd>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-4">
                    <dt class="text-xs uppercase tracking-wide text-slate-400">Protein (g)</dt>
                    <dd class="mt-1 text-xl font-semibold text-slate-100">{{ number_format($template->protein_g) }}</dd>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-4">
                    <dt class="text-xs uppercase tracking-wide text-slate-400">Carbs (g)</dt>
                    <dd class="mt-1 text-xl font-semibold text-slate-100">{{ number_format($template->carbs_g) }}</dd>
                </div>
                <div class="rounded-lg border border-slate-800 bg-slate-950/60 p-4">
                    <dt class="text-xs uppercase tracking-wide text-slate-400">Fat (g)</dt>
                    <dd class="mt-1 text-xl font-semibold text-slate-100">{{ number_format($template->fat_g) }}</dd>
                </div>
            </dl>
        </div>
    </div>
@endsection
