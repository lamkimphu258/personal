<?php

namespace App\Http\Controllers;

use App\Actions\Tasks\TaskScheduler;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;

class TodoController extends Controller
{
    public function __construct(private TaskScheduler $scheduler) {}

    public function index(Request $request): View
    {
        [$selectedDate, $carbonDate] = $this->resolveDate((string) $request->query('date'));

        $occurrences = $this->scheduler->ensureOccurrencesForDate($carbonDate);
        $summary = $this->scheduler->summarizeForDate($carbonDate);

        $priorities = [
            Task::PRIORITY_LOW => 'Low',
            Task::PRIORITY_MEDIUM => 'Medium',
            Task::PRIORITY_HIGH => 'High',
        ];

        $repeatModes = [
            Task::REPEAT_NONE => 'Do not repeat',
            Task::REPEAT_DAILY => 'Repeat daily',
            Task::REPEAT_SELECTED => 'Repeat on selected days',
        ];

        $weekdayOptions = collect(range(0, 6))->mapWithKeys(function (int $day) {
            $label = Carbon::now()->startOfWeek(Carbon::SUNDAY)->addDays($day)->format('l');

            return [$day => $label];
        })->all();

        $items = $occurrences->map(function ($occurrence) {
            $task = $occurrence->task;
            $priority = $task?->priority ?? Task::PRIORITY_MEDIUM;
            $priorityLabel = ucfirst($priority);
            $priorityClasses = match ($priority) {
                Task::PRIORITY_HIGH => 'bg-rose-500/15 text-rose-200 border border-rose-500/40',
                Task::PRIORITY_LOW => 'bg-slate-800/80 text-slate-200 border border-slate-700/60',
                default => 'bg-amber-500/15 text-amber-200 border border-amber-400/40',
            };
            $priorityBadgeHtml = sprintf(
                '<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium %s">%s</span>',
                $priorityClasses,
                $priorityLabel
            );

            $statusLabel = $occurrence->is_completed ? 'Completed' : 'Incomplete';
            $statusClasses = $occurrence->is_completed
                ? 'bg-emerald-500/15 text-emerald-200 border border-emerald-500/40'
                : 'bg-slate-800/80 text-slate-200 border border-slate-700/60';
            $statusBadgeHtml = sprintf(
                '<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium %s">%s</span>',
                $statusClasses,
                $statusLabel
            );
            $togglePayload = json_encode([
                'id' => $occurrence->id,
                'url' => route('todos.occurrences.toggle', $occurrence),
                'completed' => $occurrence->is_completed,
            ], JSON_UNESCAPED_SLASHES);
            $toggleButtonLabel = $occurrence->is_completed ? 'Mark incomplete' : 'Mark complete';
            $statusDisplay = sprintf(
                '<div class="flex items-center gap-2" x-data="{ completed: %s }" x-on:todo-occurrence-updated.window="if ($event.detail.id === %d) { completed = $event.detail.completed; }">
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium border transition" :class="completed ? \'border-emerald-500/40 bg-emerald-500/15 text-emerald-200\' : \'border-slate-700/60 bg-slate-800/80 text-slate-200\'" x-text="completed ? \'Completed\' : \'Incomplete\'"></span>
                    <button type="button" class="rounded-md border border-slate-700 bg-slate-800 px-2 py-1 text-xs font-medium text-slate-200 transition hover:border-emerald-400 hover:text-emerald-200" x-on:click.prevent=\'toggleOccurrence(%s)\' x-text="completed ? \'Mark incomplete\' : \'Mark complete\'"></button>
                </div>',
                $occurrence->is_completed ? 'true' : 'false',
                $occurrence->id,
                htmlspecialchars($togglePayload, ENT_NOQUOTES, 'UTF-8')
            );

            $repeatMode = $task?->repeat_mode ?? Task::REPEAT_NONE;

            return [
                'id' => $occurrence->id,
                'name' => $task?->title ?? 'Task',
                'priority' => $priority,
                'priority_label' => $priorityLabel,
                'priority_badge' => new HtmlString($priorityBadgeHtml),
                'due_display' => optional($task?->due_date)->format('M j, Y') ?? '—',
                'due_date' => optional($task?->due_date)->toDateString(),
                'occurrence_date' => $occurrence->occurrence_date->toDateString(),
                'is_completed' => $occurrence->is_completed,
                'task_id' => $task?->id,
                'repeat_mode' => $repeatMode,
                'repeat_days' => $task?->repeatDayNumbers() ?? [],
                'status_label' => $statusLabel,
                'status_display' => new HtmlString($statusDisplay),
                'status_badge' => new HtmlString($statusBadgeHtml),
                'edit_action' => $task ? route('todos.update', $task) : null,
                'delete_action' => $task ? route('todos.destroy', $task) : null,
            ];
        })->values();

        return view('todos', [
            'selectedDate' => $selectedDate,
            'prevDate' => $carbonDate->copy()->subDay()->toDateString(),
            'nextDate' => $carbonDate->copy()->addDay()->toDateString(),
            'items' => $items,
            'summary' => $summary,
            'priorities' => $priorities,
            'repeatModes' => $repeatModes,
            'weekdayOptions' => $weekdayOptions,
        ]);
    }

    public function store(TaskRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $contextDate = $data['context_date'];
        $repeatMode = $data['repeat_mode'];
        $repeatDays = $repeatMode === Task::REPEAT_SELECTED ? $data['repeat_days'] : [];

        $task = Task::create([
            'title' => $data['title'],
            'priority' => $data['priority'],
            'due_date' => $data['due_date'],
            'repeat_mode' => $repeatMode,
            'repeat_days' => $repeatMode === Task::REPEAT_SELECTED ? $repeatDays : null,
        ]);

        $this->scheduler->ensureOccurrencesForDate($task->due_date);
        if ($contextDate !== $task->due_date->toDateString()) {
            $this->scheduler->ensureOccurrencesForDate($contextDate);
        }

        return redirect()
            ->route('todos.index', ['date' => $contextDate])
            ->with('status', 'task-created');
    }

    public function update(TaskRequest $request, Task $task): RedirectResponse
    {
        $data = $request->validated();
        $contextDate = $data['context_date'];
        $repeatMode = $data['repeat_mode'];
        $repeatDays = $repeatMode === Task::REPEAT_SELECTED ? $data['repeat_days'] : [];

        $task->fill([
            'title' => $data['title'],
            'priority' => $data['priority'],
            'due_date' => $data['due_date'],
            'repeat_mode' => $repeatMode,
            'repeat_days' => $repeatMode === Task::REPEAT_SELECTED ? $repeatDays : null,
        ]);
        $task->save();

        $dueDateString = $task->due_date->toDateString();
        $regenerateFrom = strcmp($contextDate, $dueDateString) < 0 ? $contextDate : $dueDateString;

        $this->scheduler->regenerateUpcomingOccurrences($task, $regenerateFrom);
        $this->scheduler->ensureOccurrencesForDate($dueDateString);
        $this->scheduler->ensureOccurrencesForDate($contextDate);

        return redirect()
            ->route('todos.index', ['date' => $contextDate])
            ->with('status', 'task-updated');
    }

    public function destroy(Request $request, Task $task): RedirectResponse
    {
        $contextDate = (string) $request->input('context_date', Carbon::today()->toDateString());
        [$normalizedDate, $carbonDate] = $this->resolveDate($contextDate);

        $task->occurrences()
            ->whereDate('occurrence_date', '>=', $normalizedDate)
            ->delete();

        $task->delete();

        $this->scheduler->ensureOccurrencesForDate($carbonDate);

        return redirect()
            ->route('todos.index', ['date' => $normalizedDate])
            ->with('status', 'task-deleted');
    }

    /**
     * @return array{string, Carbon}
     */
    private function resolveDate(string $input): array
    {
        try {
            $date = Carbon::createFromFormat('Y-m-d', $input)->toDateString();
        } catch (\Throwable $exception) {
            $date = Carbon::today()->toDateString();
        }

        return [$date, Carbon::createFromFormat('Y-m-d', $date)->startOfDay()];
    }
}
