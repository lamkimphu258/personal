@props([
    // Data & keys
    'items' => collect(),
    'idKey' => 'id',
    'nameKey' => 'name',
    'dateKey' => 'created_at', // for latest-first default sorting

    // Routes (optional). If not provided, the action is hidden.
    'showRoute' => null,
    'editRoute' => null,
    'deleteRoute' => null,
    'editConfig' => null,

    // Search
    'searchParam' => 'q',
    'searchPlaceholder' => 'Search by name',

    // Popup configuration
    'popupTitleKey' => 'name',
    'popupDescriptionKey' => 'description',
    // array like: [ ['label' => 'Calories', 'key' => 'calories'], ... ]
    'popupFields' => [],

    // Optional table columns configuration, e.g. [['label' => 'Calories', 'key' => 'calories', 'align' => 'right']]
    'columns' => [],
    'templateRelationKey' => null,
    'templatePopupFields' => [],
    'emptyMessage' => 'No items found.',
])

@php
    $collection = collect($items);
    // Default latest-first sorting if dateKey exists on the first item
    $first = $collection->first();
    $canSort = $first !== null && data_get($first, $dateKey) !== null;
    $sorted = $canSort ? $collection->sortByDesc(fn ($i) => data_get($i, $dateKey)) : $collection;
    $currentQuery = request()->query($searchParam, '');
    $columns = collect($columns);
    $hasColumns = $columns->isNotEmpty();
    $templateRelationPath = $templateRelationKey;
    $templatePopupFieldDefinitions = collect(is_array($templatePopupFields) ? $templatePopupFields : [])
        ->map(function (array $field): array {
            return [
                'label' => $field['label'] ?? ucfirst((string) ($field['key'] ?? '')),
                'key' => $field['key'] ?? null,
            ];
        })
        ->filter(fn (array $field): bool => filled($field['key']))
        ->values();

    $editConfiguration = collect($editConfig ?? []);
    $editFields = $editConfiguration->get('fields', []);
    $editFields = collect(is_array($editFields) ? $editFields : []);
    $editMethod = strtoupper((string) $editConfiguration->get('method', 'POST'));
    $hasEditConfig = $editConfiguration->isNotEmpty() && $editConfiguration->get('action') && $editFields->isNotEmpty();

    $editFormTitle = (string) $editConfiguration->get('title', 'Edit Item');
    $editSubmitLabel = (string) $editConfiguration->get('submit_label', 'Save');
    $editCancelLabel = (string) $editConfiguration->get('cancel_label', 'Cancel');
    $requiresMethodField = $hasEditConfig && ! in_array($editMethod, ['GET', 'POST'], true);
    $includesCsrfToken = $hasEditConfig && $editMethod !== 'GET';

    $normalizedEditFields = $editFields
        ->map(function (array $field): array {
            return [
                'name' => $field['name'] ?? null,
                'label' => $field['label'] ?? null,
                'type' => $field['type'] ?? 'text',
                'value_key' => $field['value_key'] ?? null,
                'value' => $field['value'] ?? null,
                'attributes' => $field['attributes'] ?? [],
                'options' => collect(is_array($field['options'] ?? null) ? $field['options'] : [])
                    ->map(function ($option): array {
                        if (is_array($option)) {
                            return [
                                'value' => $option['value'] ?? null,
                                'label' => $option['label'] ?? ($option['value'] ?? null),
                            ];
                        }

                        return [
                            'value' => $option,
                            'label' => $option,
                        ];
                    })
                    ->filter(fn (array $option): bool => filled($option['value']) || $option['value'] === 0 || $option['value'] === '0')
                    ->values()
                    ->all(),
            ];
        })
        ->filter(fn ($field) => filled($field['name']))
        ->values();

    $editConfigPayload = $hasEditConfig ? [
        'action' => $editConfiguration->get('action'),
        'method' => $editMethod,
        'title' => $editFormTitle,
        'submitLabel' => $editSubmitLabel,
        'cancelLabel' => $editCancelLabel,
        'fields' => $normalizedEditFields->map(function (array $field): array {
            return [
                'name' => $field['name'],
                'label' => $field['label'],
                'type' => $field['type'],
                'valueKey' => $field['value_key'],
                'value' => $field['value'],
                'attributes' => $field['attributes'],
            ];
        })->all(),
    ] : null;

    $componentConfig = [
        'edit' => $editConfigPayload,
    ];

    $hasActions = $hasEditConfig || $editRoute || $deleteRoute;
@endphp

<section class="rounded-xl border border-slate-800 bg-slate-900/70 p-6" x-data='listingComponent(@json($componentConfig))'>
    <form method="get" class="flex items-center gap-3">
        <input type="text" name="{{ $searchParam }}" value="{{ $currentQuery }}" placeholder="{{ $searchPlaceholder }}"
               class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0"
               x-on:input.debounce.200ms="filter($event.target.value)">
        <button type="submit" class="rounded-md bg-slate-700 px-3 py-2 text-sm">Search</button>
        @if ($currentQuery !== '')
            <a href="{{ url()->current() }}" class="text-sm text-slate-300 underline">Clear</a>
        @endif
    </form>

    @if ($hasColumns)
        @php
            $headerColspan = 1 + $columns->count() + ($hasActions ? 1 : 0);
        @endphp
        <div class="mt-6 overflow-x-auto" id="listing-items">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-slate-400">
                        <th class="px-3 py-2 text-left font-medium">Name</th>
                        @foreach ($columns as $column)
                            @php
                                $align = $column['align'] ?? 'left';
                                $textClass = $align === 'right' ? 'text-right' : ($align === 'center' ? 'text-center' : 'text-left');
                            @endphp
                            <th class="px-3 py-2 font-medium {{ $textClass }}">{{ $column['label'] ?? ucfirst((string) ($column['key'] ?? '')) }}</th>
                        @endforeach
                        @if ($hasActions)
                            <th class="px-3 py-2 text-right font-medium">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse ($sorted as $item)
                        @php
                            $title = (string) data_get($item, $nameKey);
                            $id = data_get($item, $idKey);
                            $itemData = is_array($item) ? $item : $item->toArray();
                            $popupData = [
                                'title' => (string) data_get($item, $popupTitleKey),
                                'description' => (string) data_get($item, $popupDescriptionKey),
                            ];
                            foreach ($popupFields as $field) {
                                $popupData['fields'][] = [
                                    'label' => $field['label'] ?? ucfirst((string) $field['key']),
                                    'value' => data_get($item, $field['key'] ?? ''),
                                ];
                            }
                            $templatePopup = null;
                            $templateLink = null;

                            if ($templateRelationPath) {
                                $rawTemplate = data_get($item, $templateRelationPath);

                                if (! $rawTemplate && is_array($item)) {
                                    $rawTemplate = data_get($item, \Illuminate\Support\Str::snake($templateRelationPath));
                                }

                                $templateArray = $rawTemplate instanceof \Illuminate\Database\Eloquent\Model
                                    ? $rawTemplate->toArray()
                                    : (is_array($rawTemplate) ? $rawTemplate : null);

                    if ($templateArray && filled($templateArray['name'] ?? null)) {
                        $templatePopup = [
                            'title' => (string) ($templateArray['name'] ?? ''),
                            'description' => (string) ($templateArray['description'] ?? ''),
                                    'fields' => $templatePopupFieldDefinitions
                                            ->map(function (array $field) use ($templateArray): array {
                                                return [
                                                    'label' => $field['label'],
                                                    'value' => data_get($templateArray, $field['key']),
                                                ];
                                            })
                                            ->all(),
                                    ];

                                    if (! empty($templateArray['id'])) {
                                        $templateLink = route('food-templates.show', $templateArray['id']);
                                    }
                                }
                            }

                            $editAction = $itemData['edit_action'] ?? null;
                        @endphp
                        <tr
                            x-data="{ name: @js(Str::of($title)->lower()), visible: true }"
                            x-show="visible"
                            x-on:listing-filter.window="visible = $event.detail === '' || name.includes($event.detail)"
                        >
                            <td class="px-3 py-2 align-top">
                                <button type="button" class="text-emerald-300 hover:underline" x-on:click="openPopup($el)">
                                    <span class="listing-title" data-popup='@json($popupData)'>{{ $title }}</span>
                                </button>
                                @if ($canSort)
                                    @php
                                        $dateValue = data_get($item, $dateKey);
                                    @endphp
                                    <div class="mt-1 text-xs text-slate-400">
                                        {{ $dateValue instanceof \Carbon\Carbon ? $dateValue->toDateTimeString() : (string) $dateValue }}
                                    </div>
                                @endif
                                @if ($templatePopup)
                                    <div class="mt-2 text-xs">
                                        <a
                                            href="{{ $templateLink ?? '#' }}"
                                            class="text-emerald-300 hover:underline"
                                            x-on:click="if (! ($event.metaKey || $event.ctrlKey || $event.shiftKey || $event.button !== 0)) { $event.preventDefault(); openTemplate($el); }"
                                            data-template='@json($templatePopup)'
                                        >
                                            Template: {{ $templatePopup['title'] }}
                                        </a>
                                    </div>
                                @endif
                            </td>
                            @foreach ($columns as $column)
                            @php
                                $align = $column['align'] ?? 'left';
                                $textClass = $align === 'right' ? 'text-right' : ($align === 'center' ? 'text-center' : 'text-left');
                                $rawOutput = (bool) ($column['raw'] ?? false);
                                $columnValue = data_get($item, $column['key'] ?? '');
                            @endphp
                            <td class="px-3 py-2 align-top {{ $textClass }}">
                                    @if ($rawOutput)
                                        {!! $columnValue !!}
                                    @else
                                        {{ $columnValue }}
                                    @endif
                                </td>
                            @endforeach
                            @if ($hasActions)
                                <td class="px-3 py-2 text-right align-top">
                                    <div class="flex justify-end gap-2">
                                        @if ($hasEditConfig)
                                            <button
                                                type="button"
                                                class="text-xs text-slate-300 underline"
                                                x-on:click="openEdit($el.dataset.editItem, $el.dataset.editAction)"
                                                data-edit-item='@json($itemData)'
                                                @if ($editAction)
                                                    data-edit-action="{{ $editAction }}"
                                                @endif
                                            >Edit</button>
                                        @elseif ($editRoute)
                                            <a href="{{ route($editRoute, $item) }}" class="text-xs text-slate-300 underline">Edit</a>
                                        @endif
                                        @if ($deleteRoute)
                                            <form action="{{ route($deleteRoute, $item) }}" method="post" onsubmit="return confirm('Delete this item?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-300 underline">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td class="px-3 py-6 text-center text-sm text-slate-400" colspan="{{ $headerColspan }}">{{ $emptyMessage }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <div class="mt-6 divide-y divide-slate-800" id="listing-items">
            @forelse ($sorted as $item)
                @php
                    $title = (string) data_get($item, $nameKey);
                    $id = data_get($item, $idKey);
                    $itemData = is_array($item) ? $item : $item->toArray();
                    $popupData = [
                        'title' => (string) data_get($item, $popupTitleKey),
                        'description' => (string) data_get($item, $popupDescriptionKey),
                    ];
                    foreach ($popupFields as $field) {
                        $popupData['fields'][] = [
                            'label' => $field['label'] ?? ucfirst((string) $field['key']),
                            'value' => data_get($item, $field['key'] ?? ''),
                        ];
                    }

                    $templatePopup = null;
                    $templateLink = null;

                    if ($templateRelationPath) {
                        $rawTemplate = data_get($item, $templateRelationPath);

                        if (! $rawTemplate && is_array($item)) {
                            $rawTemplate = data_get($item, \Illuminate\Support\Str::snake($templateRelationPath));
                        }

                        $templateArray = $rawTemplate instanceof \Illuminate\Database\Eloquent\Model
                            ? $rawTemplate->toArray()
                            : (is_array($rawTemplate) ? $rawTemplate : null);

                        if ($templateArray && filled($templateArray['name'] ?? null)) {
                            $templatePopup = [
                                'title' => (string) ($templateArray['name'] ?? ''),
                                'description' => (string) ($templateArray['description'] ?? ''),
                                'fields' => $templatePopupFieldDefinitions
                                    ->map(function (array $field) use ($templateArray): array {
                                        return [
                                            'label' => $field['label'],
                                            'value' => data_get($templateArray, $field['key']),
                                        ];
                                    })
                                    ->all(),
                            ];

                            if (! empty($templateArray['id'])) {
                                $templateLink = route('food-templates.show', $templateArray['id']);
                            }
                        }
                    }

                    $editAction = $itemData['edit_action'] ?? null;
                @endphp
                <div class="flex items-center justify-between py-3"
                 x-data="{ name: @js(Str::of($title)->lower()), visible: true }"
                 x-show="visible"
                 x-on:listing-filter.window="visible = $event.detail === '' || name.includes($event.detail)">
                <div>
                    @if ($showRoute)
                        <a
                            href="{{ route($showRoute, $item) }}"
                            class="text-emerald-300 hover:underline"
                            x-on:click="if (! ($event.metaKey || $event.ctrlKey || $event.shiftKey || $event.button !== 0)) { $event.preventDefault(); openPopup($el); }"
                        >
                            <span class="listing-title" data-popup='@json($popupData)'>{{ $title }}</span>
                        </a>
                    @else
                        <button type="button" class="text-emerald-300 hover:underline" x-on:click="openPopup($el)">
                            <span class="listing-title" data-popup='@json($popupData)'>{{ $title }}</span>
                        </button>
                    @endif

                    @if ($canSort)
                        <div class="text-xs text-slate-400 mt-1">
                            {{ ucfirst($dateKey) }}: {{ 
                                optional(data_get($item, $dateKey)) instanceof \Carbon\Carbon
                                ? optional(data_get($item, $dateKey))->toDateTimeString()
                                : (string) data_get($item, $dateKey)
                            }}
                        </div>
                    @endif
                    @if ($templatePopup)
                        <div class="mt-2 text-xs">
                            <a
                                href="{{ $templateLink ?? '#' }}"
                                class="text-emerald-300 hover:underline"
                                x-on:click="if (! ($event.metaKey || $event.ctrlKey || $event.shiftKey || $event.button !== 0)) { $event.preventDefault(); openTemplate($el); }"
                                data-template='@json($templatePopup)'
                            >
                                Template: {{ $templatePopup['title'] }}
                            </a>
                        </div>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    @if ($hasEditConfig)
                        <button
                            type="button"
                            class="text-xs text-slate-300 underline"
                            x-on:click="openEdit($el.dataset.editItem, $el.dataset.editAction)"
                            data-edit-item='@json($itemData)'
                            @if ($editAction)
                                data-edit-action="{{ $editAction }}"
                            @endif
                        >Edit</button>
                    @elseif ($editRoute)
                        <a href="{{ route($editRoute, $item) }}" class="text-xs text-slate-300 underline">Edit</a>
                    @endif
                    @if ($deleteRoute)
                        <form action="{{ route($deleteRoute, $item) }}" method="post" onsubmit="return confirm('Delete this item?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-300 underline">Delete</button>
                        </form>
                    @endif
                </div>
            </div>
            @empty
                <p class="py-6 text-center text-sm text-slate-400">{{ $emptyMessage }}</p>
            @endforelse
        </div>
    @endif

    <div x-ref="modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-950/80" x-on:click="closePopup()"></div>
        <div class="relative mx-auto mt-24 w-full max-w-md rounded-xl border border-slate-800 bg-slate-900 p-6 shadow-xl">
            <div class="flex items-start justify-between">
                <h3 class="text-xl font-semibold text-slate-100" x-text="modalMode === 'edit' ? editForm.title : detail.title"></h3>
                <button type="button" class="rounded-md bg-slate-800 px-2 py-1 text-sm" x-on:click="closePopup()">Close</button>
            </div>

            <template x-if="modalMode === 'detail'">
                <div>
                    <p class="mt-2 text-sm text-slate-300" x-text="detail.description"></p>
                    <dl class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <template x-for="field in detail.fields" :key="field.label + field.value">
                            <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                                <dt class="text-xs text-slate-400" x-text="field.label"></dt>
                                <dd class="text-lg font-medium" x-text="field.value"></dd>
                            </div>
                        </template>
                    </dl>
                </div>
            </template>

            @if ($hasEditConfig)
                <template x-if="modalMode === 'edit'">
                    <form x-ref="editForm" x-bind:action="editForm.action" method="{{ $editMethod === 'GET' ? 'GET' : 'POST' }}" class="mt-4 space-y-4">
                        @if ($includesCsrfToken)
                            @csrf
                        @endif
                        @if ($requiresMethodField)
                            @method($editMethod)
                        @endif

                        @foreach ($normalizedEditFields as $field)
                            @php
                                $fieldName = $field['name'];
                                $fieldLabel = $field['label'] ?? \Illuminate\Support\Str::headline($fieldName);
                                $fieldType = $field['type'] ?? 'text';
                                $fieldAttributes = collect(is_array($field['attributes']) ? $field['attributes'] : [])
                                    ->map(fn ($value, $key) => sprintf('%s=\"%s\"', $key, e($value)))
                                    ->implode(' ');
                                $fieldId = \Illuminate\Support\Str::slug($fieldName);
                                $fieldOptions = collect(is_array($field['options']) ? $field['options'] : []);
                            @endphp

                            @if ($fieldType === 'hidden')
                                <input
                                    type="hidden"
                                    name="{{ $fieldName }}"
                                    x-model="editForm.values['{{ $fieldName }}']"
                                    {!! $fieldAttributes ? ' '.$fieldAttributes : '' !!}
                                >
                            @elseif ($fieldType === 'select')
                                <div class="space-y-1">
                                    <label for="edit-{{ $fieldId }}" class="text-sm text-slate-300">{{ $fieldLabel }}</label>
                                    <select
                                        id="edit-{{ $fieldId }}"
                                        name="{{ $fieldName }}"
                                        class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0"
                                        x-model="editForm.values['{{ $fieldName }}']"
                                        data-edit-input
                                        {!! $fieldAttributes ? ' '.$fieldAttributes : '' !!}
                                    >
                                        @foreach ($fieldOptions as $option)
                                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif ($fieldType === 'checkbox-group')
                                <fieldset class="space-y-2">
                                    <legend class="text-sm text-slate-300">{{ $fieldLabel }}</legend>
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach ($fieldOptions as $option)
                                            <label class="inline-flex items-center gap-2 rounded-md border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-200">
                                                <input
                                                    type="checkbox"
                                                    name="{{ $fieldName }}[]"
                                                    value="{{ $option['value'] }}"
                                                    class="rounded border-slate-700 bg-slate-950 text-emerald-500 focus:ring-emerald-500"
                                                    x-model="editForm.values['{{ $fieldName }}']"
                                                    @if ($loop->first)
                                                        data-edit-input
                                                    @endif
                                                >
                                                <span>{{ $option['label'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>
                            @elseif ($fieldType === 'textarea')
                                <div class="space-y-1">
                                    <label for="edit-{{ $fieldId }}" class="text-sm text-slate-300">{{ $fieldLabel }}</label>
                                    <textarea
                                        id="edit-{{ $fieldId }}"
                                        name="{{ $fieldName }}"
                                        class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0"
                                        x-model="editForm.values['{{ $fieldName }}']"
                                        data-edit-input
                                        {!! $fieldAttributes ? ' '.$fieldAttributes : '' !!}
                                    ></textarea>
                                </div>
                            @else
                                <div class="space-y-1">
                                    <label for="edit-{{ $fieldId }}" class="text-sm text-slate-300">{{ $fieldLabel }}</label>
                                    <input
                                        id="edit-{{ $fieldId }}"
                                        type="{{ $fieldType }}"
                                        name="{{ $fieldName }}"
                                        class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0"
                                        x-model="editForm.values['{{ $fieldName }}']"
                                        data-edit-input
                                        {!! $fieldAttributes ? ' '.$fieldAttributes : '' !!}
                                    >
                                </div>
                            @endif
                        @endforeach

                        <div class="flex items-center justify-end gap-2">
                            <button type="button" class="rounded-md border border-slate-700 bg-transparent px-3 py-2 text-sm text-slate-300 hover:border-slate-600" x-on:click="closePopup()">{{ $editCancelLabel }}</button>
                            <button type="submit" class="rounded-md border border-emerald-500 bg-emerald-500/10 px-3 py-2 text-sm font-medium text-emerald-200 hover:border-emerald-400">{{ $editSubmitLabel }}</button>
                        </div>
                    </form>
                </template>
            @endif
        </div>
    </div>
</section>

@once
    @push('head')
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @endpush
@endonce

<script>
    function listingComponent(config = {}) {
        const editConfig = config?.edit ?? null;

        return {
            modalMode: 'detail',
            detail: { title: '', description: '', fields: [] },
            edit: editConfig,
            editForm: {
                action: editConfig?.action ?? '',
                defaultAction: editConfig?.action ?? '',
                method: editConfig?.method ?? 'POST',
                title: editConfig?.title ?? 'Edit Item',
                submitLabel: editConfig?.submitLabel ?? 'Save',
                cancelLabel: editConfig?.cancelLabel ?? 'Cancel',
                fields: editConfig?.fields ?? [],
                values: {},
            },
            filter(term) {
                const query = (term || '').toLowerCase();
                window.dispatchEvent(new CustomEvent('listing-filter', { detail: query }));
            },
            openPopup(el) {
                const span = el.querySelector('.listing-title');

                if (!span) {
                    return;
                }

                try {
                    const data = JSON.parse(span.getAttribute('data-popup') || '{}');
                    this.modalMode = 'detail';
                    this.detail = {
                        title: data.title || '',
                        description: data.description || '',
                        fields: Array.isArray(data.fields) ? data.fields : [],
                    };
                    this.showModal();
                } catch (error) {
                    // Ignore malformed popup payloads
                }
            },
            openTemplate(el) {
                if (! el) {
                    return;
                }

                const payload = el.getAttribute('data-template') || '';

                if (! payload) {
                    return;
                }

                try {
                    const data = JSON.parse(payload);
                    this.modalMode = 'detail';
                    this.detail = {
                        title: data.title || '',
                        description: data.description || '',
                        fields: Array.isArray(data.fields) ? data.fields : [],
                    };
                    this.showModal();
                } catch (error) {
                    // Ignore malformed template payloads
                }
            },
            openEdit(itemPayload, action = null) {
                if (!this.editForm.fields.length) {
                    return;
                }

                let parsedItem = itemPayload;

                if (typeof parsedItem === 'string') {
                    try {
                        parsedItem = JSON.parse(parsedItem);
                    } catch (error) {
                        parsedItem = {};
                    }
                }

                this.modalMode = 'edit';
                this.editForm.action = action || this.editForm.defaultAction;
                this.populateEditValues(parsedItem);
                this.showModal();

                this.$nextTick(() => {
                    if (!this.$refs.editForm) {
                        return;
                    }

                    const firstInput = this.$refs.editForm.querySelector('[data-edit-input]');
                    if (firstInput) {
                        firstInput.focus();
                        if (typeof firstInput.select === 'function') {
                            firstInput.select();
                        }
                    }
                });
            },
            populateEditValues(item) {
                const values = {};
                const safeItem = item || {};

                this.editForm.fields.forEach((field) => {
                    if (!field.name) {
                        return;
                    }

                    let value = field.value ?? '';
                    if (field.valueKey) {
                        const segments = String(field.valueKey).split('.');
                        let current = safeItem;

                        for (const segment of segments) {
                            if (current == null) {
                                current = undefined;
                                break;
                            }

                            current = current[segment];
                        }

                        if (current !== undefined && current !== null) {
                            value = current;
                        }
                    }

                    if (field.type === 'checkbox-group') {
                        if (!Array.isArray(value)) {
                            value = value === undefined || value === null || value === ''
                                ? []
                                : [value];
                        }

                        value = value
                            .filter((entry) => entry !== undefined && entry !== null && entry !== '')
                            .map((entry) => entry.toString());
                    } else if (typeof value === 'number') {
                        value = value.toString();
                    } else if (value === undefined || value === null) {
                        value = '';
                    }

                    values[field.name] = value ?? '';
                });

                this.editForm.values = values;
            },
            showModal() {
                this.$refs.modal.classList.remove('hidden');
            },
            closePopup() {
                this.$refs.modal.classList.add('hidden');
                this.detail = { title: '', description: '', fields: [] };
                this.editForm.values = {};
                this.modalMode = 'detail';
                this.editForm.action = this.editForm.defaultAction;
            },
            init() {
                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        this.closePopup();
                    }
                });
            },
        };
    }
</script>
