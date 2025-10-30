<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FoodTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'calories' => ['required', 'integer', 'min:0', 'max:5000'],
            'protein_g' => ['required', 'integer', 'min:0', 'max:2000'],
            'carbs_g' => ['required', 'integer', 'min:0', 'max:2000'],
            'fat_g' => ['required', 'integer', 'min:0', 'max:2000'],
        ];
    }
}
