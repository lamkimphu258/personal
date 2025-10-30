@extends('layouts.app')

@section('title', 'Food Templates | ' . config('app.name', 'Nutrition Companion'))

@section('content')
    @php
        $columns = [
            ['label' => 'Calories', 'key' => 'calories', 'align' => 'right'],
            ['label' => 'Protein (g)', 'key' => 'protein_g', 'align' => 'right'],
            ['label' => 'Carbs (g)', 'key' => 'carbs_g', 'align' => 'right'],
            ['label' => 'Fat (g)', 'key' => 'fat_g', 'align' => 'right'],
        ];

        $popupFields = [
            ['label' => 'Calories', 'key' => 'calories'],
            ['label' => 'Protein (g)', 'key' => 'protein_g'],
            ['label' => 'Carbohydrates (g)', 'key' => 'carbs_g'],
            ['label' => 'Fat (g)', 'key' => 'fat_g'],
        ];

        $emptyMessage = $searchTerm !== ''
            ? 'No templates match your search.'
            : 'No templates created yet.';
    @endphp

    <div class="mx-auto flex w-full max-w-6xl flex-col gap-10 px-4 py-12">
        <header class="space-y-2">
            <h1 class="text-3xl font-semibold tracking-tight">Food Templates</h1>
            <p class="text-slate-400">Create reusable templates for meals and reuse them when logging foods.</p>
        </header>

        @if (session('status') === 'template-created')
            <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                Template saved successfully.
            </div>
        @endif

        <section class="rounded-xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 class="text-lg font-semibold">Add Template</h2>
            <p class="mt-1 text-sm text-slate-400">Fill in the nutrition details for a template you reuse often.</p>

            <form action="{{ route('food-templates.store') }}" method="post" class="mt-6 grid gap-4 sm:grid-cols-2">
                @csrf

                <div class="sm:col-span-2 space-y-1">
                    <label for="name" class="text-sm text-slate-300">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0" />
                    @error('name')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

            <div class="sm:col-span-2 space-y-1">
                <label for="description" class="text-sm text-slate-300">Description <span class="text-xs text-slate-500">(optional)</span></label>
                <textarea id="description" name="description" rows="3" class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

                <div class="space-y-1">
                    <label for="calories" class="text-sm text-slate-300">Calories</label>
                    <input id="calories" name="calories" type="number" inputmode="numeric" min="0" value="{{ old('calories') }}" required class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0" />
                    @error('calories')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label for="protein_g" class="text-sm text-slate-300">Protein (g)</label>
                    <input id="protein_g" name="protein_g" type="number" inputmode="numeric" min="0" value="{{ old('protein_g') }}" required class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0" />
                    @error('protein_g')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label for="carbs_g" class="text-sm text-slate-300">Carbs (g)</label>
                    <input id="carbs_g" name="carbs_g" type="number" inputmode="numeric" min="0" value="{{ old('carbs_g') }}" required class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0" />
                    @error('carbs_g')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-1">
                    <label for="fat_g" class="text-sm text-slate-300">Fat (g)</label>
                    <input id="fat_g" name="fat_g" type="number" inputmode="numeric" min="0" value="{{ old('fat_g') }}" required class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0" />
                    @error('fat_g')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2 flex justify-end">
                    <button type="submit" class="rounded-md border border-emerald-500 bg-emerald-500/10 px-4 py-2 text-sm font-medium text-emerald-200 hover:border-emerald-400">Save Template</button>
                </div>
            </form>
        </section>

        <section class="space-y-6">
            <div>
                <h2 class="text-lg font-semibold">Template Library</h2>
                <p class="mt-1 text-sm text-slate-400">Browse and search your saved templates. Click a template name to see the details.</p>
            </div>

            <x-listing
                :items="$templates"
                show-route="food-templates.show"
                search-param="q"
                search-placeholder="Search templates"
                :columns="$columns"
                :popup-fields="$popupFields"
                :empty-message="$emptyMessage"
            ></x-listing>
        </section>
    </div>
@endsection
