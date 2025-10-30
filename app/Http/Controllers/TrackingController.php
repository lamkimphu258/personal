<?php

namespace App\Http\Controllers;

use App\Http\Requests\FoodEntryRequest;
use App\Http\Requests\WeightEntryRequest;
use App\Models\DailyWeight;
use App\Models\FoodEntry;
use App\Models\NutritionProfile;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $inputDate = (string) $request->query('date', Carbon::today()->toDateString());

        try {
            $date = Carbon::createFromFormat('Y-m-d', $inputDate)->toDateString();
        } catch (\Throwable $e) {
            $date = Carbon::today()->toDateString();
        }

        $prevDate = Carbon::createFromFormat('Y-m-d', $date)->subDay()->toDateString();
        $nextDate = Carbon::createFromFormat('Y-m-d', $date)->addDay()->toDateString();

        $weight = DailyWeight::query()->forDate($date)->first();
        $entries = FoodEntry::query()->forDate($date)->orderBy('created_at')->get();

        $totals = [
            'protein_g' => (int) $entries->sum('protein_g'),
            'carbs_g' => (int) $entries->sum('carbs_g'),
            'fat_g' => (int) $entries->sum('fat_g'),
            'calories' => (int) $entries->sum('calories'),
        ];

        $profile = NutritionProfile::query()->first();
        $targets = $profile ? [
            'calorie_target' => (int) $profile->calorie_target,
            'protein_grams' => (int) $profile->protein_grams,
            'fat_grams' => (int) $profile->fat_grams,
            'carbohydrate_grams' => (int) $profile->carbohydrate_grams,
        ] : null;

        $remaining = null;
        if ($targets) {
            $remaining = [
                'calories' => max(0, $targets['calorie_target'] - $totals['calories']),
                'protein_g' => max(0, $targets['protein_grams'] - $totals['protein_g']),
                'fat_g' => max(0, $targets['fat_grams'] - $totals['fat_g']),
                'carbs_g' => max(0, $targets['carbohydrate_grams'] - $totals['carbs_g']),
            ];
        }

        return view('tracking', [
            'date' => $date,
            'prevDate' => $prevDate,
            'nextDate' => $nextDate,
            'weight' => $weight,
            'entries' => $entries,
            'totals' => $totals,
            'targets' => $targets,
            'remaining' => $remaining,
        ]);
    }

    public function storeWeight(WeightEntryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DailyWeight::query()->updateOrCreate(
            ['date' => $data['date']],
            ['weight_kg' => (float) $data['weight_kg']]
        );

        return redirect()->route('tracking.index', ['date' => $data['date']])->with('status', 'weight-saved');
    }

    public function storeFood(FoodEntryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $entryId = $data['entry_id'] ?? null;

        $payload = [
            'date' => $data['date'],
            'name' => $data['name'],
            'protein_g' => (int) $data['protein_g'],
            'carbs_g' => (int) $data['carbs_g'],
            'fat_g' => (int) $data['fat_g'],
            'calories' => (int) $data['calories'],
        ];

        if ($entryId) {
            $entry = FoodEntry::query()->findOrFail($entryId);
            $entry->fill($payload)->save();

            return redirect()->route('tracking.index', ['date' => $data['date']])->with('status', 'food-updated');
        }

        FoodEntry::create($payload);

        return redirect()->route('tracking.index', ['date' => $data['date']])->with('status', 'food-saved');
    }

    public function editFood(FoodEntry $foodEntry): RedirectResponse
    {
        return redirect()->route('tracking.index', [
            'date' => $foodEntry->date->toDateString(),
            'entry' => $foodEntry->id,
        ]);
    }

    public function destroyFood(FoodEntry $foodEntry): RedirectResponse
    {
        $date = $foodEntry->date->toDateString();
        $foodEntry->delete();

        return redirect()->route('tracking.index', ['date' => $date])->with('status', 'food-deleted');
    }
}
