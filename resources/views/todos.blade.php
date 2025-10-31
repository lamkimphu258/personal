@php
    use App\Models\Task;
    use Illuminate\Support\HtmlString;

    $columns = [
        ['label' => 'Priority', 'key' => 'priority_badge', 'raw' => true],
        ['label' => 'Due Date', 'key' => 'due_display'],
        ['label' => 'Status', 'key' => 'status_display', 'raw' => true],
    ];

    $popupFields = [
        ['label' => 'Priority', 'key' => 'priority_label'],
        ['label' => 'Due Date', 'key' => 'due_display'],
        ['label' => 'Status', 'key' => 'status_label'],
    ];

    $priorityOptions = collect($priorities)->map(fn ($label, $value) => [
        'value' => $value,
        'label' => $label,
    ])->values()->all();

    $repeatOptions = collect($repeatModes)->map(fn ($label, $value) => [
        'value' => $value,
        'label' => $label,
    ])->values()->all();

    $weekdayOptionList = collect($weekdayOptions)->map(fn ($label, $value) => [
        'value' => $value,
        'label' => $label,
    ])->values()->all();

    $editConfig = [
        'action' => route('todos.update', ['task' => 0]),
        'method' => 'PUT',
        'title' => 'Edit Task',
        'submit_label' => 'Save Task',
        'fields' => [
            [
                'name' => 'context_date',
                'type' => 'hidden',
                'value' => $selectedDate,
            ],
            [
                'name' => 'title',
                'label' => 'Title',
                'type' => 'text',
                'value_key' => 'name',
                'attributes' => [
                    'required' => 'required',
                    'maxlength' => 120,
                ],
            ],
            [
                'name' => 'priority',
                'label' => 'Priority',
                'type' => 'select',
                'value_key' => 'priority',
                'options' => $priorityOptions,
                'attributes' => [
                    'required' => 'required',
                ],
            ],
            [
                'name' => 'due_date',
                'label' => 'Due Date',
                'type' => 'date',
                'value_key' => 'due_date',
                'attributes' => [
                    'required' => 'required',
                ],
            ],
            [
                'name' => 'repeat_mode',
                'label' => 'Repeat',
                'type' => 'select',
                'value_key' => 'repeat_mode',
                'options' => $repeatOptions,
                'attributes' => [
                    'required' => 'required',
                ],
            ],
            [
                'name' => 'repeat_days',
                'label' => 'Repeat On',
                'type' => 'checkbox-group',
                'value_key' => 'repeat_days',
                'options' => $weekdayOptionList,
                'attributes' => [
                    'x-show' => "editForm.values['repeat_mode'] === '" . Task::REPEAT_SELECTED . "'",
                    'x-cloak' => 'true',
                ],
            ],
        ],
    ];
@endphp

@extends('layouts.app')

@section('title', 'Todos | ' . config('app.name', 'Planner'))

@section('body-class', 'bg-slate-950 text-slate-100 min-h-screen')

@section('content')
    <div
        class="mx-auto w-full max-w-6xl px-4 py-10 lg:px-6"
        x-data="todoPage({
            summary: {{ json_encode($summary ?? ['total' => 0, 'completed' => 0, 'incomplete' => 0]) }},
            contextDate: '{{ $selectedDate }}'
        })"
    >
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-semibold tracking-tight text-slate-100">Todo Planner</h1>
                <p class="mt-2 text-sm text-slate-400">
                    Capture chores and routines for each day, track completion, and keep recurring tasks on schedule.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-300">
                <a
                    href="{{ route('todos.index', ['date' => $prevDate]) }}"
                    class="rounded-md border border-slate-700 bg-slate-900 px-3 py-2 transition hover:border-slate-600 hover:text-slate-100"
                >
                    Previous
                </a>
                <form method="get" action="{{ route('todos.index') }}" class="flex items-center gap-2">
                    <label class="text-slate-400" for="date">Date</label>
                    <input
                        type="date"
                        id="date"
                        name="date"
                        value="{{ $selectedDate }}"
                        class="rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0"
                    >
                    <button type="submit" class="rounded-md border border-emerald-500/60 bg-emerald-500/10 px-3 py-2 text-sm font-medium text-emerald-200 transition hover:bg-emerald-500/20">
                        Go
                    </button>
                </form>
                <a
                    href="{{ route('todos.index', ['date' => $nextDate]) }}"
                    class="rounded-md border border-slate-700 bg-slate-900 px-3 py-2 transition hover:border-slate-600 hover:text-slate-100"
                >
                    Next
                </a>
            </div>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20">
                <dt class="text-sm font-medium text-slate-400">Total Tasks</dt>
                <dd class="mt-3 text-3xl font-semibold tracking-tight text-slate-50" x-text="summary.total"></dd>
                <p class="mt-2 text-xs text-slate-500">All tasks scheduled for {{ $selectedDate }}.</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20">
                <dt class="text-sm font-medium text-slate-400">Completed</dt>
                <dd class="mt-3 text-3xl font-semibold tracking-tight text-emerald-200" x-text="summary.completed"></dd>
                <p class="mt-2 text-xs text-slate-500">Occurrences marked done for this day.</p>
            </div>
            <div class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20">
                <dt class="text-sm font-medium text-slate-400">Incomplete</dt>
                <dd class="mt-3 text-3xl font-semibold tracking-tight text-amber-200" x-text="summary.incomplete"></dd>
                <p class="mt-2 text-xs text-slate-500">Tasks still waiting for attention.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="mt-6 rounded-xl border border-rose-500/40 bg-rose-500/10 p-4 text-sm text-rose-100">
                <p class="font-medium">Please fix the errors below before saving your task.</p>
            </div>
        @endif

        <div class="mt-8 grid gap-6 lg:grid-cols-[420px,1fr]">
            <section
                class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20"
                x-data="{ repeatMode: '{{ old('repeat_mode', Task::REPEAT_NONE) }}' }"
            >
                <h2 class="text-lg font-semibold tracking-tight text-slate-100">Add Task</h2>
                <p class="mt-1 text-xs text-slate-400">Create a one-time or repeating task for the selected date.</p>

                <form method="post" action="{{ route('todos.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="context_date" value="{{ $selectedDate }}">

                    <div>
                        <label for="title" class="text-sm font-medium text-slate-300">Title</label>
                        <input
                            id="title"
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            maxlength="120"
                            required
                            class="mt-1 w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0"
                        >
                        @error('title')
                            <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="priority" class="text-sm font-medium text-slate-300">Priority</label>
                            <select
                                id="priority"
                                name="priority"
                                required
                                class="mt-1 w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0"
                            >
                                @foreach ($priorities as $value => $label)
                                    <option value="{{ $value }}" @selected(old('priority', Task::PRIORITY_MEDIUM) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('priority')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="due_date" class="text-sm font-medium text-slate-300">Due Date</label>
                            <input
                                id="due_date"
                                type="date"
                                name="due_date"
                                value="{{ old('due_date', $selectedDate) }}"
                                required
                                class="mt-1 w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0"
                            >
                            @error('due_date')
                                <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="repeat_mode" class="text-sm font-medium text-slate-300">Repeat</label>
                        <select
                            id="repeat_mode"
                            name="repeat_mode"
                            x-model="repeatMode"
                            required
                            class="mt-1 w-full rounded-md border border-slate-700 bg-slate-950 px-3 py-2 text-sm text-slate-100 focus:border-emerald-400 focus:outline-none focus:ring-0"
                        >
                            @foreach ($repeatModes as $value => $label)
                                <option value="{{ $value }}" @selected(old('repeat_mode', Task::REPEAT_NONE) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('repeat_mode')
                            <p class="mt-1 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <fieldset x-cloak x-show="repeatMode === '{{ Task::REPEAT_SELECTED }}'">
                        <legend class="text-sm font-medium text-slate-300">Repeat on</legend>
                        <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-slate-200 sm:grid-cols-3">
                            @foreach ($weekdayOptions as $value => $label)
                                <label class="inline-flex items-center gap-2 rounded-md border border-slate-800 bg-slate-950 px-3 py-2">
                                    <input
                                        type="checkbox"
                                        name="repeat_days[]"
                                        value="{{ $value }}"
                                        @checked(in_array($value, old('repeat_days', [])))
                                        class="rounded border-slate-700 bg-slate-950 text-emerald-500 focus:ring-emerald-500"
                                    >
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('repeat_days')
                            <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <div class="pt-2">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-md border border-emerald-500/60 bg-emerald-500/10 px-4 py-2 text-sm font-medium text-emerald-200 transition hover:border-emerald-400 hover:bg-emerald-500/20"
                        >
                            Save Task
                        </button>
                    </div>
                </form>
            </section>

            <section class="rounded-2xl border border-slate-800 bg-slate-900/70 p-6 shadow-lg shadow-slate-950/20">
                <h2 class="text-lg font-semibold tracking-tight text-slate-100">Tasks for {{ \Carbon\Carbon::createFromFormat('Y-m-d', $selectedDate)->format('F j, Y') }}</h2>
                <p class="mt-1 text-xs text-slate-400">Toggle completion to keep the summary in sync. Edit tasks inline to adjust recurrence or due dates.</p>

                @php
                    $listingItems = $items->map(function (array $item) use ($selectedDate) {
                        $deleteForm = '';

                        if (! empty($item['delete_action']) && ! empty($item['task_id'])) {
                            $deleteForm = new HtmlString(
                                '<form method="post" action="'.$item['delete_action'].'" class="inline-flex items-center gap-2" onsubmit="return confirm(\'Delete this task and future occurrences?\')">
                                    '.csrf_field().method_field('DELETE').'
                                    <input type="hidden" name="context_date" value="'.$selectedDate.'">
                                    <button type="submit" class="rounded-md border border-rose-500/40 bg-rose-500/10 px-2 py-1 text-xs font-medium text-rose-200 transition hover:border-rose-400 hover:text-rose-100">Delete</button>
                                </form>'
                            );
                        }

                        $item['manage_display'] = $deleteForm;

                        return $item;
                    });

                    $manageColumn = ['label' => 'Manage', 'key' => 'manage_display', 'raw' => true];
                    $columnsWithManage = array_merge($columns, [$manageColumn]);
                @endphp

                <x-listing
                    :items="$listingItems"
                    name-key="name"
                    id-key="id"
                    date-key="occurrence_date"
                    :columns="$columnsWithManage"
                    :popup-fields="$popupFields"
                    :edit-config="$editConfig"
                    empty-message="No tasks scheduled for this date."
                />
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function todoPage(config) {
            return {
                summary: config.summary ?? { total: 0, completed: 0, incomplete: 0 },
                contextDate: config.contextDate ?? null,
                isProcessing: false,
                async toggleOccurrence(payload) {
                    if (this.isProcessing || !payload?.url) {
                        return;
                    }

                    this.isProcessing = true;

                    try {
                        const response = await axios.patch(payload.url, {
                            completed: !payload.completed,
                        });

                        if (response?.data?.summary) {
                            this.summary = response.data.summary;
                        }

                        if (response?.data?.occurrence) {
                            window.dispatchEvent(new CustomEvent('todo-occurrence-updated', {
                                detail: {
                                    id: response.data.occurrence.id,
                                    completed: response.data.occurrence.is_completed,
                                },
                            }));
                        }
                    } catch (error) {
                        console.error('Unable to toggle task occurrence', error);
                        alert('Failed to update the task. Please try again.');
                    } finally {
                        this.isProcessing = false;
                    }
                },
            };
        }
    </script>
@endpush
