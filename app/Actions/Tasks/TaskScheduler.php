<?php

namespace App\Actions\Tasks;

use App\Models\Task;
use App\Models\TaskOccurrence;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Throwable;

class TaskScheduler
{
    public function ensureOccurrencesForDate(string|Carbon $date): EloquentCollection
    {
        $targetDate = $this->resolveDate($date);
        $dateString = $targetDate->toDateString();

        $tasks = Task::query()
            ->whereDate('due_date', '<=', $dateString)
            ->get();

        foreach ($tasks as $task) {
            if (! $task->occursOnDate($targetDate)) {
                continue;
            }

            $alreadyExists = TaskOccurrence::query()
                ->where('task_id', $task->id)
                ->whereDate('occurrence_date', $dateString)
                ->exists();

            if (! $alreadyExists) {
                TaskOccurrence::query()->create([
                    'task_id' => $task->id,
                    'occurrence_date' => $dateString,
                ]);
            }
        }

        return TaskOccurrence::query()
            ->with('task')
            ->whereDate('occurrence_date', $dateString)
            ->orderBy('occurrence_date')
            ->get();
    }

    public function regenerateUpcomingOccurrences(Task $task, string|Carbon $fromDate): void
    {
        $startDate = $this->resolveDate($fromDate)->toDateString();

        TaskOccurrence::query()
            ->where('task_id', $task->id)
            ->whereDate('occurrence_date', '>=', $startDate)
            ->delete();
    }

    public function summarizeForDate(string|Carbon $date): array
    {
        $targetDate = $this->resolveDate($date)->toDateString();

        $query = TaskOccurrence::query()
            ->whereDate('occurrence_date', $targetDate);

        $total = (clone $query)->count();
        $completed = (clone $query)->where('is_completed', true)->count();
        $incomplete = $total - $completed;

        return [
            'total' => $total,
            'completed' => $completed,
            'incomplete' => max(0, $incomplete),
        ];
    }

    private function resolveDate(string|Carbon $date): Carbon
    {
        if ($date instanceof Carbon) {
            return $date->copy()->startOfDay();
        }

        try {
            return Carbon::createFromFormat('Y-m-d', (string) $date)->startOfDay();
        } catch (Throwable $exception) {
            return Carbon::today()->startOfDay();
        }
    }
}
