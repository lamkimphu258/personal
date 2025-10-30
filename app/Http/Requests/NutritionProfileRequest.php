<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class NutritionProfileRequest extends FormRequest
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
        return [
            'age' => ['required', 'integer', 'between:18,80'],
            'sex' => ['required', 'in:female,male'],
            'current_weight_kg' => ['required', 'numeric', 'between:40,250'],
            'goal_weight_kg' => ['required', 'numeric', 'between:35,250', 'lt:current_weight_kg'],
            'height_cm' => ['required', 'integer', 'between:120,210'],
            'activity_level' => ['required', 'in:sedentary,lightly-active,moderately-active,very-active,athlete'],
            'desired_loss_per_week_kg' => ['required', 'numeric', Rule::in(['0.25', '0.5', '0.75', '1'])],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $currentWeight = (float) $this->input('current_weight_kg', 0);
            $goalWeight = (float) $this->input('goal_weight_kg', 0);

            if ($currentWeight > 0 && $goalWeight > 0 && ($currentWeight - $goalWeight) < 2) {
                $validator->errors()->add('goal_weight_kg', 'Goal weight must be at least 2 kg below current weight.');
            }
        });
    }
}
