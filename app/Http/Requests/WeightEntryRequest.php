<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeightEntryRequest extends FormRequest
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
            'weight_kg' => ['required', 'numeric', 'min:20', 'max:400'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date.required' => 'Please select a date.',
            'weight_kg.required' => 'Please enter your weight for the day.',
        ];
    }
}
