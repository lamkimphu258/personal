<?php

namespace App\Actions;

use InvalidArgumentException;

class CalculateNutritionTargets
{
    private const ACTIVITY_FACTORS = [
        'sedentary' => 1.2,
        'lightly-active' => 1.375,
        'moderately-active' => 1.55,
        'very-active' => 1.725,
        'athlete' => 1.9,
    ];

    private const CALORIE_FLOORS = [
        'female' => 1200,
        'male' => 1500,
    ];

    /**
     * Calculate calorie and macro nutrient targets from profile inputs.
     *
     * @param  array<string, int|float|string>  $inputs
     * @return array<string, int>
     */
    public function calculate(array $inputs): array
    {
        $this->guardInputs($inputs);

        $currentWeight = (float) $inputs['current_weight_kg'];
        $goalWeight = (float) $inputs['goal_weight_kg'];
        $height = (float) $inputs['height_cm'];
        $age = (int) $inputs['age'];
        $sex = (string) $inputs['sex'];
        $activityLevel = (string) $inputs['activity_level'];
        $lossPerWeek = (float) $inputs['desired_loss_per_week_kg'];

        $bmr = $this->calculateBmr($sex, $currentWeight, $height, $age);
        $activityFactor = self::ACTIVITY_FACTORS[$activityLevel];
        $maintenanceCalories = $bmr * $activityFactor;

        $desiredDeficit = ($lossPerWeek * 7700) / 7;
        $maxDeficit = $maintenanceCalories * 0.25;
        $deficit = min($desiredDeficit, $maxDeficit);

        $calorieTarget = (int) round($maintenanceCalories - $deficit);
        $calorieTarget = max(self::CALORIE_FLOORS[$sex], $calorieTarget);

        $proteinMin = (int) round(max(1.6 * $goalWeight, 1.2 * $currentWeight));
        $fatMin = (int) round(max(0.8 * $goalWeight, (0.25 * $calorieTarget) / 9));

        $proteinGrams = max(1, $proteinMin);
        $fatGrams = max(1, $fatMin);

        $remainingCalories = $calorieTarget - (($proteinGrams * 4) + ($fatGrams * 9));
        $carbGrams = (int) round(max(100, $remainingCalories / 4));
        $carbGrams = max(0, $carbGrams);

        $carbCalorieCap = $calorieTarget * 0.45;
        if (($carbGrams * 4) > $carbCalorieCap) {
            $carbGrams = (int) round($carbCalorieCap / 4);
            $carbGrams = max(100, $carbGrams);

            $allocated = ($proteinGrams * 4) + ($fatGrams * 9) + ($carbGrams * 4);
            $remainder = $calorieTarget - $allocated;

            if ($remainder > 0) {
                $proteinShare = max(1, $proteinMin * 4);
                $fatShare = max(1, $fatMin * 9);
                $shareTotal = $proteinShare + $fatShare;

                $proteinGrams += (int) round(($remainder * ($proteinShare / $shareTotal)) / 4);
                $fatGrams += (int) round(($remainder * ($fatShare / $shareTotal)) / 9);
            }
        }

        $fibreGrams = (int) round(max(28, $calorieTarget * 0.014));

        $this->rebalanceMacros(
            $calorieTarget,
            $proteinMin,
            $fatMin,
            $proteinGrams,
            $fatGrams,
            $carbGrams
        );

        return [
            'bmr' => (int) round($bmr),
            'maintenance_calories' => (int) round($maintenanceCalories),
            'calorie_deficit' => (int) round($deficit),
            'calorie_target' => $calorieTarget,
            'protein_grams' => $proteinGrams,
            'fat_grams' => $fatGrams,
            'carbohydrate_grams' => $carbGrams,
            'fibre_grams' => $fibreGrams,
        ];
    }

    /**
     * @param  array<string, int|float|string>  $inputs
     */
    private function guardInputs(array $inputs): void
    {
        $required = [
            'age',
            'sex',
            'current_weight_kg',
            'goal_weight_kg',
            'height_cm',
            'activity_level',
            'desired_loss_per_week_kg',
        ];

        foreach ($required as $key) {
            if (! array_key_exists($key, $inputs)) {
                throw new InvalidArgumentException("Missing nutrition input: {$key}");
            }
        }
    }

    private function calculateBmr(string $sex, float $weightKg, float $heightCm, int $age): float
    {
        $base = (10 * $weightKg) + (6.25 * $heightCm) - (5 * $age);

        return $sex === 'male' ? $base + 5 : $base - 161;
    }

    private function rebalanceMacros(
        int $calorieTarget,
        int $proteinMin,
        int $fatMin,
        int &$proteinGrams,
        int &$fatGrams,
        int &$carbGrams
    ): void {
        $proteinGrams = max($proteinMin, $proteinGrams);
        $fatGrams = max($fatMin, $fatGrams);
        $carbGrams = max(0, $carbGrams);

        $allocated = ($proteinGrams * 4) + ($fatGrams * 9) + ($carbGrams * 4);

        if ($allocated > $calorieTarget) {
            $excess = $allocated - $calorieTarget;
            $carbReducibleCalories = max(0, $carbGrams - 100) * 4;

            if ($carbReducibleCalories > 0) {
                $carbReduction = (int) ceil(min($excess, $carbReducibleCalories) / 4);
                $carbGrams -= $carbReduction;
                $excess -= $carbReduction * 4;
            }

            if ($excess > 0) {
                $fatReducibleCalories = max(0, $fatGrams - $fatMin) * 9;
                if ($fatReducibleCalories > 0) {
                    $fatReduction = (int) ceil(min($excess, $fatReducibleCalories) / 9);
                    $fatGrams -= $fatReduction;
                    $excess -= $fatReduction * 9;
                }
            }

            if ($excess > 0) {
                $proteinReducibleCalories = max(0, $proteinGrams - $proteinMin) * 4;
                if ($proteinReducibleCalories > 0) {
                    $proteinReduction = (int) ceil(min($excess, $proteinReducibleCalories) / 4);
                    $proteinGrams -= $proteinReduction;
                    $excess -= $proteinReduction * 4;
                }
            }
        }

        $allocated = ($proteinGrams * 4) + ($fatGrams * 9) + ($carbGrams * 4);
        if ($allocated < $calorieTarget) {
            $proteinGrams += (int) round(($calorieTarget - $allocated) / 4);
        }

        $proteinGrams = max($proteinMin, $proteinGrams);
        $fatGrams = max($fatMin, $fatGrams);
        $carbGrams = max(0, $carbGrams);
    }
}
