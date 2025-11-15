<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const PRIORITY_LOW = 'low';

    public const PRIORITY_MEDIUM = 'medium';

    public const PRIORITY_HIGH = 'high';

    public const PRIORITIES = [
        self::PRIORITY_LOW,
        self::PRIORITY_MEDIUM,
        self::PRIORITY_HIGH,
    ];

    public const REPEAT_NONE = 'none';

    public const REPEAT_DAILY = 'daily';

    public const REPEAT_SELECTED = 'selected';

    public const REPEAT_MODES = [
        self::REPEAT_NONE,
        self::REPEAT_DAILY,
        self::REPEAT_SELECTED,
    ];

    protected $fillable = [
        'title',
        'priority',
        'due_date',
        'repeat_mode',
        'repeat_days',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'repeat_days' => 'array',
        ];
    }

    public function occurrences(): HasMany
    {
        return $this->hasMany(TaskOccurrence::class);
    }

    public function occursOnDate(Carbon $date): bool
    {
        $dueDate = $this->due_date instanceof Carbon
            ? $this->due_date->copy()
            : Carbon::parse((string) $this->due_date);

        if ($date->lt($dueDate->startOfDay())) {
            return false;
        }

        return match ($this->repeat_mode) {
            self::REPEAT_NONE => $date->isSameDay($dueDate),
            self::REPEAT_DAILY => true,
            self::REPEAT_SELECTED => in_array($date->dayOfWeek, $this->repeatDayNumbers(), true),
            default => false,
        };
    }

    /**
     * @return array<int, int>
     */
    public function repeatDayNumbers(): array
    {
        $days = $this->repeat_days;

        if (! is_array($days)) {
            return [];
        }

        return collect($days)
            ->filter(fn ($day) => is_int($day) || ctype_digit((string) $day))
            ->map(fn ($day) => (int) $day)
            ->filter(fn (int $day) => $day >= 0 && $day <= 6)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }
}
