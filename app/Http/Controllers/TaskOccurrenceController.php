<?php

namespace App\Http\Controllers;

use App\Actions\Tasks\TaskScheduler;
use App\Models\TaskOccurrence;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskOccurrenceController extends Controller
{
    public function toggle(Request $request, TaskOccurrence $taskOccurrence, TaskScheduler $scheduler): JsonResponse
    {
        $completed = $request->boolean('completed', ! $taskOccurrence->is_completed);
        $taskOccurrence->setCompletion($completed);

        $occurrenceDate = $taskOccurrence->occurrence_date->toDateString();
        $summary = $scheduler->summarizeForDate($occurrenceDate);

        return response()->json([
            'occurrence' => [
                'id' => $taskOccurrence->id,
                'task_id' => $taskOccurrence->task_id,
                'occurrence_date' => $occurrenceDate,
                'is_completed' => $taskOccurrence->is_completed,
                'completed_at' => optional($taskOccurrence->completed_at)->toDateTimeString(),
            ],
            'summary' => $summary,
        ]);
    }
}
