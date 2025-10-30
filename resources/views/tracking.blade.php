@extends('layouts.app')

@section('title', 'Daily Tracking | ' . config('app.name', 'Nutrition Companion'))

@section('body-class', 'bg-slate-950 text-slate-100 min-h-screen')

@section('content')
    @php
        $macroColumns = [
            ['label' => 'Protein (g)', 'key' => 'protein_g', 'align' => 'right'],
            ['label' => 'Carbs (g)', 'key' => 'carbs_g', 'align' => 'right'],
            ['label' => 'Fat (g)', 'key' => 'fat_g', 'align' => 'right'],
            ['label' => 'Calories', 'key' => 'calories', 'align' => 'right'],
        ];

        $popupFields = [
            ['label' => 'Protein (g)', 'key' => 'protein_g'],
            ['label' => 'Carbohydrates (g)', 'key' => 'carbs_g'],
            ['label' => 'Fat (g)', 'key' => 'fat_g'],
            ['label' => 'Calories', 'key' => 'calories'],
        ];

        $templatePopupFields = [
            ['label' => 'Calories', 'key' => 'calories'],
            ['label' => 'Protein (g)', 'key' => 'protein_g'],
            ['label' => 'Carbohydrates (g)', 'key' => 'carbs_g'],
            ['label' => 'Fat (g)', 'key' => 'fat_g'],
        ];

        $templateOptions = collect($templates)
            ->map(fn ($template) => [
                'id' => $template->id,
                'name' => $template->name,
                'description' => $template->description,
                'calories' => $template->calories,
                'protein_g' => $template->protein_g,
                'carbs_g' => $template->carbs_g,
                'fat_g' => $template->fat_g,
            ])
            ->values();

        $emptyMessage = "No entries yet for {$date}.";

        $submitLabel = 'Add';
        $nameValue = old('name');
        $proteinValue = old('protein_g');
        $carbsValue = old('carbs_g');
        $fatValue = old('fat_g');
        $calorieValue = old('calories');
        $selectedTemplateOld = old('food_template_id');

        $editConfig = [
            'action' => route('tracking.food.store'),
            'method' => 'POST',
            'title' => 'Edit Food Entry',
            'submit_label' => 'Update',
            'cancel_label' => 'Cancel',
            'fields' => [
                ['name' => 'entry_id', 'type' => 'hidden', 'value_key' => 'id'],
                ['name' => 'date', 'type' => 'hidden', 'value' => $date],
                ['name' => 'food_template_id', 'type' => 'hidden', 'value_key' => 'food_template_id'],
                ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'value_key' => 'name'],
                ['name' => 'protein_g', 'label' => 'Protein (g)', 'type' => 'number', 'value_key' => 'protein_g', 'attributes' => ['inputmode' => 'numeric', 'min' => 0, 'step' => 1]],
                ['name' => 'carbs_g', 'label' => 'Carbs (g)', 'type' => 'number', 'value_key' => 'carbs_g', 'attributes' => ['inputmode' => 'numeric', 'min' => 0, 'step' => 1]],
                ['name' => 'fat_g', 'label' => 'Fat (g)', 'type' => 'number', 'value_key' => 'fat_g', 'attributes' => ['inputmode' => 'numeric', 'min' => 0, 'step' => 1]],
                ['name' => 'calories', 'label' => 'Calories', 'type' => 'number', 'value_key' => 'calories', 'attributes' => ['inputmode' => 'numeric', 'min' => 0, 'step' => 1]],
            ],
        ];
    @endphp

    <div class="mx-auto w-full max-w-6xl px-4 py-12 space-y-10">
        <header class="space-y-2">
            <h1 class="text-3xl font-semibold tracking-tight">Daily Tracking</h1>
            <p class="text-slate-400">Track today’s weight and food entries. Navigate days to view history.</p>
        </header>

        @if (session('status') === 'weight-saved')
            <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                Weight saved for {{ $date }}.
            </div>
        @endif
        @if (session('status') === 'food-saved')
            <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                Food entry added for {{ $date }}.
            </div>
        @endif
        @if (session('status') === 'food-updated')
            <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                Food entry updated for {{ $date }}.
            </div>
        @endif
        @if (session('status') === 'food-deleted')
            <div class="rounded-lg border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                Food entry removed from {{ $date }}.
            </div>
        @endif

        <section class="rounded-xl border border-slate-800 bg-slate-900/70 p-6 space-y-6">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-2">
                    <a href="{{ route('tracking.index', ['date' => $prevDate]) }}" class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm hover:border-slate-600">Previous</a>
                    <a href="{{ route('tracking.index', ['date' => $nextDate]) }}" class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm hover:border-slate-600">Next</a>
                </div>
                <form action="{{ route('tracking.index') }}" method="get" class="flex items-center gap-2">
                    <input type="date" name="date" value="{{ $date }}" class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0" />
                    <button type="submit" class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm hover:border-slate-600">Go</button>
                </form>
            </div>

            <div class="space-y-4">
                @if ($targets)
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-4">
                            <dl>
                                <dt class="text-xs uppercase tracking-wide text-slate-400">Calories</dt>
                                <dd class="mt-2 text-2xl font-semibold">{{ $totals['calories'] }} / {{ $targets['calorie_target'] }}</dd>
                            </dl>
                            <p class="mt-1 text-xs text-slate-400">Remaining: {{ $remaining['calories'] }}</p>
                        </div>
                        <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-4">
                            <dl>
                                <dt class="text-xs uppercase tracking-wide text-slate-400">Protein</dt>
                                <dd class="mt-2 text-2xl font-semibold">{{ $totals['protein_g'] }} g / {{ $targets['protein_grams'] }} g</dd>
                            </dl>
                            <p class="mt-1 text-xs text-slate-400">Remaining: {{ $remaining['protein_g'] }} g</p>
                        </div>
                        <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-4">
                            <dl>
                                <dt class="text-xs uppercase tracking-wide text-slate-400">Carbs</dt>
                                <dd class="mt-2 text-2xl font-semibold">{{ $totals['carbs_g'] }} g / {{ $targets['carbohydrate_grams'] }} g</dd>
                            </dl>
                            <p class="mt-1 text-xs text-slate-400">Remaining: {{ $remaining['carbs_g'] }} g</p>
                        </div>
                        <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-4">
                            <dl>
                                <dt class="text-xs uppercase tracking-wide text-slate-400">Fat</dt>
                                <dd class="mt-2 text-2xl font-semibold">{{ $totals['fat_g'] }} g / {{ $targets['fat_grams'] }} g</dd>
                            </dl>
                            <p class="mt-1 text-xs text-slate-400">Remaining: {{ $remaining['fat_g'] }} g</p>
                        </div>
                    </div>
                @else
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-4">
                            <dl>
                                <dt class="text-xs uppercase tracking-wide text-slate-400">Calories</dt>
                                <dd class="mt-2 text-2xl font-semibold">{{ $totals['calories'] }}</dd>
                            </dl>
                        </div>
                        <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-4">
                            <dl>
                                <dt class="text-xs uppercase tracking-wide text-slate-400">Protein</dt>
                                <dd class="mt-2 text-2xl font-semibold">{{ $totals['protein_g'] }} g</dd>
                            </dl>
                        </div>
                        <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-4">
                            <dl>
                                <dt class="text-xs uppercase tracking-wide text-slate-400">Carbs</dt>
                                <dd class="mt-2 text-2xl font-semibold">{{ $totals['carbs_g'] }} g</dd>
                            </dl>
                        </div>
                        <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-4">
                            <dl>
                                <dt class="text-xs uppercase tracking-wide text-slate-400">Fat</dt>
                                <dd class="mt-2 text-2xl font-semibold">{{ $totals['fat_g'] }} g</dd>
                            </dl>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400">Set up your <a href="{{ route('profile.edit') }}" class="text-emerald-300 underline underline-offset-4">Nutrition Profile</a> to unlock daily targets.</p>
                @endif
            </div>
        </section>

        <section class="rounded-xl border border-slate-800 bg-slate-900/70 p-6 space-y-4">
            <h2 class="text-lg font-semibold">Daily Weight</h2>
            <form action="{{ route('tracking.weight.store') }}" method="post" class="grid gap-3 sm:grid-cols-3">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}" />
                <div class="sm:col-span-2 space-y-1">
                    <label for="weight_kg" class="text-sm text-slate-300">Weight (kg)</label>
                    <input id="weight_kg" name="weight_kg" type="number" step="0.1" min="20" max="400" value="{{ old('weight_kg', optional($weight)->weight_kg) }}" required class="w-full rounded-md border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0" />
                    @error('weight_kg')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="self-end">
                    <button type="submit" class="w-full rounded-md border border-slate-700 bg-slate-900 px-3 py-2 text-sm hover:border-slate-600">Save</button>
                </div>
            </form>
        </section>

        <section class="rounded-xl border border-slate-800 bg-slate-900/70 p-6 space-y-6">
            <div>
                <h2 class="text-lg font-semibold">Food Entries</h2>
                <p class="mt-1 text-sm text-slate-400">Log each meal or snack with its macros to keep the totals accurate.</p>
            </div>

            <form
                action="{{ route('tracking.food.store') }}"
                method="post"
                class="grid gap-3 sm:grid-cols-6"
                x-data="(() => {
                    return {
                        templates: @js($templateOptions),
                        selectedTemplateId: @js($selectedTemplateOld),
                        findTemplate(id) {
                            return this.templates.find((template) => String(template.id) === String(id));
                        },
                        applyTemplate(id) {
                            const template = this.findTemplate(id);

                            if (! template) {
                                return;
                            }

                            if (this.$refs.nameField) {
                                this.$refs.nameField.value = template.name;
                            }

                            if (this.$refs.proteinField) {
                                this.$refs.proteinField.value = template.protein_g;
                            }

                            if (this.$refs.carbsField) {
                                this.$refs.carbsField.value = template.carbs_g;
                            }

                            if (this.$refs.fatField) {
                                this.$refs.fatField.value = template.fat_g;
                            }

                            if (this.$refs.caloriesField) {
                                this.$refs.caloriesField.value = template.calories;
                            }
                        },
                        handleTemplateChange(value) {
                            const normalized = value || null;
                            this.selectedTemplateId = normalized;

                            if (normalized) {
                                this.applyTemplate(normalized);
                            }
                        },
                        clearTemplate() {
                            this.selectedTemplateId = null;
                            if (this.$refs.templateSelect) {
                                this.$refs.templateSelect.value = '';
                            }
                        },
                    };
                })()"
                x-init="if (selectedTemplateId) { applyTemplate(selectedTemplateId); }"
            >
                @csrf
                <input type="hidden" name="date" value="{{ $date }}" />

                <div class="sm:col-span-2 space-y-1">
                    <label for="food_template_id" class="text-sm text-slate-300">Template</label>
                    <div class="flex items-center gap-2">
                        <select
                            id="food_template_id"
                            name="food_template_id"
                            x-ref="templateSelect"
                            x-model="selectedTemplateId"
                            x-on:change="handleTemplateChange($event.target.value)"
                            class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0"
                        >
                            <option value="">Select a template</option>
                            @foreach ($templates as $template)
                                <option value="{{ $template->id }}" @selected($selectedTemplateOld == $template->id)>{{ $template->name }}</option>
                            @endforeach
                        </select>
                        <button
                            type="button"
                            class="rounded-md border border-slate-700 bg-transparent px-3 py-2 text-xs text-slate-300 hover:border-slate-600"
                            x-on:click="clearTemplate()"
                        >Clear</button>
                    </div>
                    @error('food_template_id')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2 space-y-1">
                    <label for="name" class="text-sm text-slate-300">Name</label>
                    <input id="name" name="name" type="text" value="{{ $nameValue }}" required class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0" x-ref="nameField" />
                    @error('name')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-1">
                    <label for="protein_g" class="text-sm text-slate-300">Protein (g)</label>
                    <input id="protein_g" name="protein_g" type="number" inputmode="numeric" min="1" value="{{ $proteinValue }}" required class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0" x-ref="proteinField" />
                    @error('protein_g')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-1">
                    <label for="carbs_g" class="text-sm text-slate-300">Carbs (g)</label>
                    <input id="carbs_g" name="carbs_g" type="number" inputmode="numeric" min="1" value="{{ $carbsValue }}" required class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0" x-ref="carbsField" />
                    @error('carbs_g')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-1">
                    <label for="fat_g" class="text-sm text-slate-300">Fat (g)</label>
                    <input id="fat_g" name="fat_g" type="number" inputmode="numeric" min="1" value="{{ $fatValue }}" required class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0" x-ref="fatField" />
                    @error('fat_g')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-1">
                    <label for="calories" class="text-sm text-slate-300">Calories</label>
                    <input id="calories" name="calories" type="number" inputmode="numeric" min="1" value="{{ $calorieValue }}" required class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0" x-ref="caloriesField" />
                    @error('calories')
                        <p class="text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="self-end">
                    <div class="flex items-center gap-2">
                        <button type="submit" class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm hover:border-slate-600">{{ $submitLabel }}</button>
                    </div>
                </div>
            </form>
        </section>

        <x-listing
            :items="$entries"
            id-key="id"
            name-key="name"
            date-key="created_at"
            search-param="food"
            search-placeholder="Search foods"
            :popup-fields="$popupFields"
            :columns="$macroColumns"
            template-relation-key="foodTemplate"
            :template-popup-fields="$templatePopupFields"
            :empty-message="$emptyMessage"
            :edit-config="$editConfig"
            delete-route="tracking.food.destroy"
        ></x-listing>
    </div>
@endsection
