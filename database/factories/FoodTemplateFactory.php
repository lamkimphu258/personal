<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FoodTemplate>
 */
class FoodTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->optional()->sentence(10),
            'calories' => fake()->numberBetween(50, 1200),
            'protein_g' => fake()->numberBetween(0, 120),
            'carbs_g' => fake()->numberBetween(0, 150),
            'fat_g' => fake()->numberBetween(0, 120),
        ];
    }
}
