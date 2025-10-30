<?php

namespace Database\Factories;

use App\Actions\CalculateNutritionTargets;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NutritionProfile>
 */
class NutritionProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sex = $this->faker->randomElement(['female', 'male']);
        $age = $this->faker->numberBetween(25, 60);
        $currentWeight = $this->faker->randomFloat(1, 60, 110);
        $goalWeight = $currentWeight - $this->faker->randomFloat(1, 5, 15);
        $height = $this->faker->numberBetween(160, 190);
        $activityLevel = $this->faker->randomElement([
            'sedentary',
            'lightly-active',
            'moderately-active',
            'very-active',
            'athlete',
        ]);
        $lossPerWeek = $this->faker->randomElement([0.25, 0.5, 0.75, 1]);

        $inputs = [
            'age' => $age,
            'sex' => $sex,
            'current_weight_kg' => $currentWeight,
            'goal_weight_kg' => $goalWeight,
            'height_cm' => $height,
            'activity_level' => $activityLevel,
            'desired_loss_per_week_kg' => $lossPerWeek,
        ];

        $targets = (new CalculateNutritionTargets)->calculate($inputs);

        return array_merge($inputs, [
            'calorie_target' => $targets['calorie_target'],
            'protein_grams' => $targets['protein_grams'],
            'fat_grams' => $targets['fat_grams'],
            'carbohydrate_grams' => $targets['carbohydrate_grams'],
            'fibre_grams' => $targets['fibre_grams'],
        ]);
    }
}
