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

    // Search
    'searchParam' => 'q',
    'searchPlaceholder' => 'Search by name',

    // Popup configuration
    'popupTitleKey' => 'name',
    'popupDescriptionKey' => 'description',
    // array like: [ ['label' => 'Calories', 'key' => 'calories'], ... ]
    'popupFields' => [],
])

@php
    $collection = collect($items);
    // Default latest-first sorting if dateKey exists on the first item
    $first = $collection->first();
    $canSort = $first !== null && data_get($first, $dateKey) !== null;
    $sorted = $canSort ? $collection->sortByDesc(fn ($i) => data_get($i, $dateKey)) : $collection;
    $currentQuery = request()->query($searchParam, '');
@endphp

<section class="rounded-xl border border-slate-800 bg-slate-900/70 p-6" x-data="listingComponent()">
    <form method="get" class="flex items-center gap-3">
        <input type="text" name="{{ $searchParam }}" value="{{ $currentQuery }}" placeholder="{{ $searchPlaceholder }}"
               class="w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0"
               x-on:input.debounce.200ms="filter($event.target.value)">
        <button type="submit" class="rounded-md bg-slate-700 px-3 py-2 text-sm">Search</button>
        @if ($currentQuery !== '')
            <a href="{{ url()->current() }}" class="text-sm text-slate-300 underline">Clear</a>
        @endif
    </form>

    <div class="mt-6 divide-y divide-slate-800" id="listing-items">
        @foreach ($sorted as $item)
            @php
                $title = (string) data_get($item, $nameKey);
                $id = data_get($item, $idKey);
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
            @endphp
            <div class="flex items-center justify-between py-3"
                 x-data="{ name: @js(Str::of($title)->lower()), visible: true }"
                 x-show="visible"
                 x-on:listing-filter.window="visible = $event.detail === '' || name.includes($event.detail)">
                <div>
                    @if ($showRoute)
                        <a href="{{ route($showRoute, $item) }}" class="text-emerald-300 hover:underline" x-on:click.prevent="openPopup($el)">
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
                </div>
                <div class="flex items-center gap-2">
                    @if ($editRoute)
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
        @endforeach
    </div>

    <div x-ref="modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-950/80" x-on:click="closePopup()"></div>
        <div class="relative mx-auto mt-24 w-full max-w-md rounded-xl border border-slate-800 bg-slate-900 p-6 shadow-xl">
            <div class="flex items-start justify-between">
                <h3 x-ref="popupTitle" class="text-xl font-semibold text-slate-100">Item</h3>
                <button type="button" class="rounded-md bg-slate-800 px-2 py-1 text-sm" x-on:click="closePopup()">Close</button>
            </div>
            <p x-ref="popupDescription" class="mt-2 text-sm text-slate-300"></p>
            <dl x-ref="popupFields" class="mt-4 grid grid-cols-2 gap-3"></dl>
        </div>
    </div>
</section>

@once
    @push('head')
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @endpush
@endonce

<script>
    function listingComponent() {
        return {
            filter(term) {
                const query = (term || '').toLowerCase();
                window.dispatchEvent(new CustomEvent('listing-filter', { detail: query }));
            },
            openPopup(el) {
                const span = el.querySelector('.listing-title');
                try {
                    const data = JSON.parse(span.getAttribute('data-popup'));
                    this.$refs.popupTitle.textContent = data.title || '';
                    this.$refs.popupDescription.textContent = data.description || '';
                    const dl = this.$refs.popupFields;
                    dl.innerHTML = '';
                    (data.fields || []).forEach(f => {
                        const wrap = document.createElement('div');
                        wrap.className = 'rounded-lg border border-slate-800 bg-slate-950/70 p-3';
                        const dt = document.createElement('dt');
                        dt.className = 'text-xs text-slate-400';
                        dt.textContent = f.label;
                        const dd = document.createElement('dd');
                        dd.className = 'text-lg font-medium';
                        dd.textContent = f.value;
                        wrap.appendChild(dt); wrap.appendChild(dd); dl.appendChild(wrap);
                    });
                    this.$refs.modal.classList.remove('hidden');
                } catch (e) {}
            },
            closePopup() { this.$refs.modal.classList.add('hidden'); },
            init() {
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') this.closePopup();
                });
            }
        }
    }
</script>

