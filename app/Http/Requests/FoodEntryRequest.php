<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FoodEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'entry_id' => ['nullable', 'integer', Rule::exists('food_entries', 'id')->where(fn ($query) => $query->whereDate('date', $this->input('date')))],
            'name' => ['required', 'string', 'max:255'],
            'protein_g' => ['required', 'integer', 'min:1', 'max:2000'],
            'carbs_g' => ['required', 'integer', 'min:1', 'max:2000'],
            'fat_g' => ['required', 'integer', 'min:1', 'max:2000'],
            'calories' => ['required', 'integer', 'min:1', 'max:5000'],
            'food_template_id' => ['nullable', 'integer', Rule::exists('food_templates', 'id')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date.required' => 'Please select a date for the entry.',
            'entry_id.exists' => 'The selected entry is invalid for the chosen date.',
            'name.required' => 'Please enter a food name.',
        ];
    }
}
