<?php

namespace App\Http\Controllers;

use App\Actions\CalculateNutritionTargets;
use App\Http\Requests\NutritionProfileRequest;
use App\Models\NutritionProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NutritionProfileController extends Controller
{
    public function edit(CalculateNutritionTargets $calculator): View
    {
        $profile = NutritionProfile::query()->first();
        $inputs = $profile ? $profile->only([
            'age',
            'sex',
            'current_weight_kg',
            'goal_weight_kg',
            'height_cm',
            'activity_level',
            'desired_loss_per_week_kg',
        ]) : $this->defaults();

        $inputs['desired_loss_per_week_kg'] = $this->normaliseLossRate($inputs['desired_loss_per_week_kg']);

        $targets = null;

        if ($profile) {
            $computed = $calculator->calculate($inputs);
            $targets = array_merge($computed, [
                'calorie_target' => $profile->calorie_target,
                'protein_grams' => $profile->protein_grams,
                'fat_grams' => $profile->fat_grams,
                'carbohydrate_grams' => $profile->carbohydrate_grams,
                'fibre_grams' => $profile->fibre_grams,
            ]);
        }

        return view('profile', [
            'inputs' => $inputs,
            'targets' => $targets,
            'activityLevels' => $this->activityLevels(),
            'lossOptions' => $this->lossOptions(),
        ]);
    }

    public function update(NutritionProfileRequest $request, CalculateNutritionTargets $calculator): RedirectResponse
    {
        $inputs = $request->validated();
        $inputs['desired_loss_per_week_kg'] = $this->normaliseLossRate($inputs['desired_loss_per_week_kg']);
        $targets = $calculator->calculate($inputs);

        $payload = array_merge($inputs, [
            'calorie_target' => $targets['calorie_target'],
            'protein_grams' => $targets['protein_grams'],
            'fat_grams' => $targets['fat_grams'],
            'carbohydrate_grams' => $targets['carbohydrate_grams'],
            'fibre_grams' => $targets['fibre_grams'],
        ]);

        $profile = NutritionProfile::query()->first();

        if ($profile) {
            $profile->fill($payload)->save();
        } else {
            NutritionProfile::create($payload);
        }

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * @return array<string, mixed>
     */
    private function defaults(): array
    {
        return [
            'age' => 35,
            'sex' => 'female',
            'current_weight_kg' => 72.0,
            'goal_weight_kg' => 65.0,
            'height_cm' => 168,
            'activity_level' => 'lightly-active',
            'desired_loss_per_week_kg' => '0.5',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function activityLevels(): array
    {
        return [
            'sedentary' => 'Sedentary (little or no exercise)',
            'lightly-active' => 'Lightly Active (1-2 days/week)',
            'moderately-active' => 'Moderately Active (3-4 days/week)',
            'very-active' => 'Very Active (5-6 days/week)',
            'athlete' => 'Athlete (daily + intense training)',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function lossOptions(): array
    {
        return [
            '0.25' => 'Gentle (0.25 kg per week)',
            '0.5' => 'Steady (0.5 kg per week)',
            '0.75' => 'Focused (0.75 kg per week)',
            '1' => 'Max (1.0 kg per week)',
        ];
    }

    private function normaliseLossRate(string|float|int $value): string
    {
        $numeric = (float) $value;

        return rtrim(rtrim(number_format($numeric, 2, '.', ''), '0'), '.');
    }
}
