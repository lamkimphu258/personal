<?php

namespace App\Http\Controllers;

use App\Models\DailyWeight;
use App\Models\FoodEntry;
use App\Models\NutritionProfile;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $range = $this->resolveRange($request, allowDefault: true);

        return view('dashboard', [
            'range' => $range,
            'widgets' => $this->buildWidgets(),
            'dashboardData' => $this->buildDashboardData($range['start'], $range['end']),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $range = $this->resolveRange($request, allowDefault: false);

        if (! $range) {
            return response()->json([
                'message' => 'Please provide a valid preset or start/end dates.',
            ], 422);
        }

        return response()->json([
            'range' => $range,
            'data' => $this->buildDashboardData($range['start'], $range['end']),
        ]);
    }

    /**
     * @return array{start: string, end: string, preset: string}|null
     */
    private function resolveRange(Request $request, bool $allowDefault): ?array
    {
        $preset = strtolower((string) $request->query('preset', ''));
        $startInput = (string) $request->query('start', '');
        $endInput = (string) $request->query('end', '');
        $today = Carbon::today();

        if ($startInput !== '' && $endInput !== '') {
            try {
                $startDate = Carbon::createFromFormat('Y-m-d', $startInput)->startOfDay();
                $endDate = Carbon::createFromFormat('Y-m-d', $endInput)->startOfDay();
            } catch (\Throwable $e) {
                return null;
            }

            if ($startDate->greaterThan($endDate)) {
                [$startDate, $endDate] = [$endDate, $startDate];
            }

            return [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
                'preset' => $preset === '' ? 'custom' : $preset,
            ];
        }

        if (in_array($preset, ['week', 'month', 'year'], true)) {
            $startDate = match ($preset) {
                'week' => $today->copy()->subDays(6),
                'month' => $today->copy()->subDays(29),
                'year' => $today->copy()->subDays(364),
                default => $today->copy()->subDays(29),
            };

            return [
                'start' => $startDate->toDateString(),
                'end' => $today->toDateString(),
                'preset' => $preset,
            ];
        }

        if (! $allowDefault) {
            return null;
        }

        return [
            'start' => $today->copy()->subDays(29)->toDateString(),
            'end' => $today->toDateString(),
            'preset' => 'month',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildDashboardData(string $start, string $end): array
    {
        return [
            'weight-trend' => $this->weightSeries($start, $end),
            'calorie-adherence' => $this->calorieAdherenceSeries($start, $end),
            'top-foods' => $this->topFoodsSeries($start, $end),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function weightSeries(string $start, string $end): array
    {
        $weights = DailyWeight::query()
            ->whereBetween('date', [$start, $end])
            ->orderBy('date')
            ->get();

        return [
            'points' => $weights->map(static fn (DailyWeight $weight) => [
                'label' => $weight->date->toDateString(),
                'value' => round((float) $weight->weight_kg, 2),
            ])->values()->all(),
            'valueSuffix' => ' kg',
            'valueDecimals' => 1,
            'labelsAreDates' => true,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function calorieAdherenceSeries(string $start, string $end): array
    {
        $profile = NutritionProfile::query()->first();

        if (! $profile || $profile->calorie_target === null) {
            return [
                'points' => [],
                'valueSuffix' => '',
                'valueDecimals' => 1,
                'labelsAreDates' => true,
                'meta' => [
                    'hasTarget' => false,
                    'message' => 'Calorie goals require a nutrition profile.',
                ],
            ];
        }

        $target = (int) $profile->calorie_target;

        $dailyCalories = FoodEntry::query()
            ->whereBetween('date', [$start, $end])
            ->select('date', DB::raw('SUM(calories) as total_calories'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'points' => $dailyCalories->map(static function ($record) use ($target) {
                $date = $record->date instanceof Carbon
                    ? $record->date->toDateString()
                    : Carbon::parse((string) $record->date)->toDateString();

                return [
                    'label' => $date,
                    'value' => (int) $record->total_calories <= $target ? 1 : 0,
                ];
            })->values()->all(),
            'valueSuffix' => '',
            'valueDecimals' => 1,
            'labelsAreDates' => true,
            'meta' => [
                'hasTarget' => true,
                'target' => $target,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function topFoodsSeries(string $start, string $end): array
    {
        $topFoods = FoodEntry::query()
            ->whereBetween('date', [$start, $end])
            ->select('name', DB::raw('COUNT(*) as total'))
            ->groupBy('name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'points' => $topFoods->map(static fn ($row) => [
                'label' => (string) $row->name,
                'value' => (int) $row->total,
            ])->values()->all(),
            'valueSuffix' => '×',
            'valueDecimals' => 0,
            'labelsAreDates' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildWidgets(): array
    {
        $currentWeight = DailyWeight::query()->orderByDesc('date')->first();

        $previousWeight = null;
        $delta = null;
        $direction = 'none';
        $latestRecordedAt = null;

        if ($currentWeight) {
            $previousWeight = DailyWeight::query()
                ->whereDate('date', '<', $currentWeight->date->toDateString())
                ->orderByDesc('date')
                ->first();

            if ($previousWeight) {
                $rawDelta = (float) $currentWeight->weight_kg - (float) $previousWeight->weight_kg;
                $delta = round(abs($rawDelta), 1);

                if ($rawDelta > 0) {
                    $direction = 'up';
                } elseif ($rawDelta < 0) {
                    $direction = 'down';
                } else {
                    $direction = 'flat';
                }
            } else {
                $direction = 'flat';
            }

            $latestRecordedAt = $currentWeight->date->toDateString();
        }

        $profile = NutritionProfile::query()->first();

        return [
            'currentWeight' => [
                'hasValue' => $currentWeight !== null,
                'weight' => $currentWeight ? round((float) $currentWeight->weight_kg, 1) : null,
                'direction' => $direction,
                'delta' => $delta,
                'previousWeight' => $previousWeight ? round((float) $previousWeight->weight_kg, 1) : null,
                'previousRecordedAt' => $previousWeight?->date?->toDateString(),
                'latestRecordedAt' => $latestRecordedAt,
                'hasComparison' => $previousWeight !== null,
            ],
            'targetWeight' => [
                'hasProfile' => $profile !== null,
                'goalWeight' => $profile?->goal_weight_kg !== null ? round((float) $profile->goal_weight_kg, 1) : null,
            ],
        ];
    }
}
