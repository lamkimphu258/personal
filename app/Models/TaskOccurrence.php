<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskOccurrence extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'occurrence_date',
        'is_completed',
        'completed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'occurrence_date' => 'date',
            'completed_at' => 'datetime',
            'is_completed' => 'bool',
        ];
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class)->withTrashed();
    }

    public function setCompletion(bool $completed): void
    {
        $this->is_completed = $completed;
        $this->completed_at = $completed ? Carbon::now() : null;
        $this->save();
    }
}
