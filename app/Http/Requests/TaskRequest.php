<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $repeatMode = $this->input('repeat_mode', Task::REPEAT_NONE);
        $task = $this->route('task');

        $dueDateRules = [
            'required',
            'date_format:Y-m-d',
        ];

        if (! $task) {
            $dueDateRules[] = 'after_or_equal:context_date';
        }

        return [
            'context_date' => ['required', 'date_format:Y-m-d'],
            'title' => ['required', 'string', 'max:120'],
            'priority' => ['required', Rule::in(Task::PRIORITIES)],
            'due_date' => $dueDateRules,
            'repeat_mode' => ['required', Rule::in(Task::REPEAT_MODES)],
            'repeat_days' => collect([
                Rule::requiredIf($repeatMode === Task::REPEAT_SELECTED),
                'array',
            ])
                ->when(
                    $repeatMode === Task::REPEAT_SELECTED,
                    fn ($rules) => $rules->push('min:1')
                )
                ->all(),
            'repeat_days.*' => ['integer', 'between:0,6'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $repeatDays = $this->input('repeat_days');

        if (is_null($repeatDays)) {
            $repeatDays = [];
        } elseif (is_string($repeatDays)) {
            $repeatDays = array_filter(explode(',', $repeatDays));
        }

        $this->merge([
            'repeat_days' => $repeatDays,
        ]);
    }

    protected function passedValidation(): void
    {
        $days = collect($this->input('repeat_days', []))
            ->map(fn ($day) => is_int($day) ? $day : (int) $day)
            ->filter(fn (int $day) => $day >= 0 && $day <= 6)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $this->merge([
            'repeat_days' => $days,
        ]);
    }
}
