@extends('layouts.app')

@section('title', 'Nutrition Profile | ' . config('app.name', 'Nutrition Companion'))

@section('body-class', 'bg-slate-950 text-slate-100 min-h-screen')

@section('content')
    <div class="mx-auto w-full max-w-6xl px-4 py-12 space-y-10">
        <header class="space-y-2">
            <h1 class="text-3xl font-semibold tracking-tight">Weight Loss Profile</h1>
            <p class="text-slate-400">Update your current stats so the app can calculate a safe calorie target and macro plan that emphasises protein and fibre.</p>
        </header>

        @if (session('status') === 'profile-updated')
            <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                Profile updated. Targets refreshed below.
            </div>
        @endif

        <section class="rounded-xl border border-slate-800 bg-slate-900/70 p-6">
            <form action="{{ route('profile.update') }}" method="post" class="grid gap-6 lg:grid-cols-2">
                @csrf
                <div class="space-y-2">
                    <label for="age" class="text-sm font-medium text-slate-200">Age (years)</label>
                    <input id="age" name="age" type="number" inputmode="numeric" min="18" max="80" value="{{ old('age', $inputs['age']) }}" required class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0" />
                    @error('age')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="sex" class="text-sm font-medium text-slate-200">Sex</label>
                    <select id="sex" name="sex" required class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0">
                        <option value="female" @selected(old('sex', $inputs['sex']) === 'female')>Female</option>
                        <option value="male" @selected(old('sex', $inputs['sex']) === 'male')>Male</option>
                    </select>
                    @error('sex')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="current_weight_kg" class="text-sm font-medium text-slate-200">Current Weight (kg)</label>
                    <input id="current_weight_kg" name="current_weight_kg" type="number" step="0.1" min="40" max="250" value="{{ old('current_weight_kg', $inputs['current_weight_kg']) }}" required class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0" />
                    @error('current_weight_kg')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="goal_weight_kg" class="text-sm font-medium text-slate-200">Goal Weight (kg)</label>
                    <input id="goal_weight_kg" name="goal_weight_kg" type="number" step="0.1" min="35" max="250" value="{{ old('goal_weight_kg', $inputs['goal_weight_kg']) }}" required class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0" />
                    @error('goal_weight_kg')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="height_cm" class="text-sm font-medium text-slate-200">Height (cm)</label>
                    <input id="height_cm" name="height_cm" type="number" min="120" max="210" value="{{ old('height_cm', $inputs['height_cm']) }}" required class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0" />
                    @error('height_cm')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="activity_level" class="text-sm font-medium text-slate-200">Activity Level</label>
                    <select id="activity_level" name="activity_level" required class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0">
                        @foreach ($activityLevels as $key => $label)
                            <option value="{{ $key }}" @selected(old('activity_level', $inputs['activity_level']) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('activity_level')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="desired_loss_per_week_kg" class="text-sm font-medium text-slate-200">Weight Loss Per Week</label>
                    <select id="desired_loss_per_week_kg" name="desired_loss_per_week_kg" required class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0">
                        @foreach ($lossOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('desired_loss_per_week_kg', $inputs['desired_loss_per_week_kg']) == $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('desired_loss_per_week_kg')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="lg:col-span-2 flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-emerald-500 px-4 py-2 text-sm font-medium text-emerald-950 shadow-lg shadow-emerald-500/20 transition hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300 focus:ring-offset-2 focus:ring-offset-slate-900">
                        Save Profile
                    </button>
                </div>
            </form>
        </section>

        <section class="rounded-xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 class="text-xl font-semibold text-slate-100">Daily Targets</h2>
            <p class="text-sm text-slate-400">The calorie budget and macro plan update as soon as you save new details.</p>

            @if ($targets)
                <dl class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-4">
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Calories</dt>
                        <dd class="mt-2 text-2xl font-semibold">{{ number_format($targets['calorie_target']) }} kcal</dd>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-4">
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Protein</dt>
                        <dd class="mt-2 text-2xl font-semibold">{{ $targets['protein_grams'] }} g</dd>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-4">
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Fat</dt>
                        <dd class="mt-2 text-2xl font-semibold">{{ $targets['fat_grams'] }} g</dd>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-4">
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Carbohydrates</dt>
                        <dd class="mt-2 text-2xl font-semibold">{{ $targets['carbohydrate_grams'] }} g</dd>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-4">
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Fibre</dt>
                        <dd class="mt-2 text-2xl font-semibold">{{ $targets['fibre_grams'] }} g</dd>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-4">
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Maintenance Calories</dt>
                        <dd class="mt-2 text-2xl font-semibold">{{ number_format($targets['maintenance_calories']) }} kcal</dd>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-4">
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Basal Metabolic Rate</dt>
                        <dd class="mt-2 text-2xl font-semibold">{{ number_format($targets['bmr']) }} kcal</dd>
                    </div>
                    <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-4">
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Daily Deficit</dt>
                        <dd class="mt-2 text-2xl font-semibold">{{ number_format($targets['calorie_deficit']) }} kcal</dd>
                    </div>
                </dl>
            @else
                <p class="mt-4 text-sm text-slate-400">Save your profile to see daily calorie and macro targets.</p>
            @endif
        </section>
    </div>

@endsection
