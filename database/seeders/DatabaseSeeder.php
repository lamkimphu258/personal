<?php

namespace Database\Seeders;

use App\Models\DailyWeight;
use App\Models\FoodEntry;
use App\Models\NutritionProfile;
use App\Models\Task;
use App\Models\TaskOccurrence;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $today = Carbon::today();

        DailyWeight::query()->delete();
        FoodEntry::query()->delete();
        NutritionProfile::query()->delete();
        TaskOccurrence::query()->delete();
        Task::withTrashed()->forceDelete();

        NutritionProfile::query()->create([
            'age' => 34,
            'sex' => 'male',
            'current_weight_kg' => 84.0,
            'goal_weight_kg' => 78.5,
            'height_cm' => 178,
            'activity_level' => 'moderately-active',
            'desired_loss_per_week_kg' => 0.50,
            'calorie_target' => 2100,
            'protein_grams' => 170,
            'fat_grams' => 65,
            'carbohydrate_grams' => 230,
            'fibre_grams' => 30,
        ]);

        $weightPoints = [
            ['offset' => 28, 'weight' => 86.4],
            ['offset' => 21, 'weight' => 85.8],
            ['offset' => 14, 'weight' => 85.1],
            ['offset' => 7, 'weight' => 84.5],
            ['offset' => 0, 'weight' => 84.0],
        ];

        foreach ($weightPoints as $point) {
            DailyWeight::query()->create([
                'date' => $today->copy()->subDays($point['offset'])->toDateString(),
                'weight_kg' => $point['weight'],
            ]);
        }

        $foodOptions = [
            'Grilled Chicken Breast' => ['protein_g' => 48, 'carbs_g' => 2, 'fat_g' => 10, 'calories' => 320],
            'Quinoa Salad' => ['protein_g' => 12, 'carbs_g' => 60, 'fat_g' => 12, 'calories' => 420],
            'Roasted Sweet Potatoes' => ['protein_g' => 4, 'carbs_g' => 58, 'fat_g' => 3, 'calories' => 250],
            'Berry Smoothie' => ['protein_g' => 2, 'carbs_g' => 40, 'fat_g' => 1, 'calories' => 180],
            'Overnight Oats' => ['protein_g' => 12, 'carbs_g' => 55, 'fat_g' => 8, 'calories' => 350],
            'Turkey Stir-Fry' => ['protein_g' => 45, 'carbs_g' => 35, 'fat_g' => 18, 'calories' => 500],
            'Avocado Toast' => ['protein_g' => 10, 'carbs_g' => 35, 'fat_g' => 14, 'calories' => 320],
            'Protein Pancakes' => ['protein_g' => 30, 'carbs_g' => 50, 'fat_g' => 10, 'calories' => 400],
            'Baked Salmon' => ['protein_g' => 42, 'carbs_g' => 0, 'fat_g' => 36, 'calories' => 550],
            'Veggie Omelette' => ['protein_g' => 18, 'carbs_g' => 6, 'fat_g' => 12, 'calories' => 220],
        ];

        $foodSchedule = [
            $today->copy()->subDays(6)->toDateString() => [
                'Grilled Chicken Breast',
                'Quinoa Salad',
                'Roasted Sweet Potatoes',
                'Berry Smoothie',
                'Avocado Toast',
            ],
            $today->copy()->subDays(5)->toDateString() => [
                'Overnight Oats',
                'Turkey Stir-Fry',
                'Grilled Chicken Breast',
                'Protein Pancakes',
            ],
            $today->copy()->subDays(4)->toDateString() => [
                'Baked Salmon',
                'Quinoa Salad',
                'Turkey Stir-Fry',
                'Avocado Toast',
                'Protein Pancakes',
            ],
            $today->copy()->subDays(3)->toDateString() => [
                'Overnight Oats',
                'Berry Smoothie',
                'Veggie Omelette',
                'Grilled Chicken Breast',
                'Roasted Sweet Potatoes',
            ],
            $today->copy()->subDays(2)->toDateString() => [
                'Baked Salmon',
                'Protein Pancakes',
                'Turkey Stir-Fry',
                'Quinoa Salad',
                'Avocado Toast',
                'Berry Smoothie',
            ],
            $today->copy()->subDays(1)->toDateString() => [
                'Veggie Omelette',
                'Overnight Oats',
                'Grilled Chicken Breast',
                'Protein Pancakes',
                'Roasted Sweet Potatoes',
                'Berry Smoothie',
                'Avocado Toast',
            ],
        ];

        foreach ($foodSchedule as $date => $foods) {
            foreach ($foods as $name) {
                $nutrients = $foodOptions[$name];

                FoodEntry::query()->create([
                    'date' => $date,
                    'name' => $name,
                    'protein_g' => $nutrients['protein_g'],
                    'carbs_g' => $nutrients['carbs_g'],
                    'fat_g' => $nutrients['fat_g'],
                    'calories' => $nutrients['calories'],
                ]);
            }
        }

        $dailyTask = Task::query()->create([
            'title' => 'Morning Walk',
            'priority' => Task::PRIORITY_MEDIUM,
            'due_date' => $today->copy()->subDays(14)->toDateString(),
            'repeat_mode' => Task::REPEAT_DAILY,
        ]);

        $deepWorkTask = Task::query()->create([
            'title' => 'Deep Work Session',
            'priority' => Task::PRIORITY_HIGH,
            'due_date' => $today->copy()->subDays(14)->toDateString(),
            'repeat_mode' => Task::REPEAT_SELECTED,
            'repeat_days' => [1, 2, 3, 4, 5],
        ]);

        $weekendResetTask = Task::query()->create([
            'title' => 'Weekend Reset',
            'priority' => Task::PRIORITY_LOW,
            'due_date' => $today->copy()->subDays(14)->toDateString(),
            'repeat_mode' => Task::REPEAT_SELECTED,
            'repeat_days' => [0, 6],
        ]);

        $todoSchedule = [
            $today->copy()->subDays(6)->toDateString() => [
                ['task' => $dailyTask, 'completed' => true],
                ['task' => $deepWorkTask, 'completed' => true],
            ],
            $today->copy()->subDays(5)->toDateString() => [
                ['task' => $dailyTask, 'completed' => true],
                ['task' => $deepWorkTask, 'completed' => false],
            ],
            $today->copy()->subDays(4)->toDateString() => [
                ['task' => $dailyTask, 'completed' => false],
                ['task' => $deepWorkTask, 'completed' => true],
            ],
            $today->copy()->subDays(3)->toDateString() => [
                ['task' => $dailyTask, 'completed' => true],
                ['task' => $deepWorkTask, 'completed' => true],
            ],
            $today->copy()->subDays(2)->toDateString() => [
                ['task' => $dailyTask, 'completed' => true],
                ['task' => $deepWorkTask, 'completed' => true],
                ['task' => $weekendResetTask, 'completed' => true],
            ],
            $today->copy()->subDays(1)->toDateString() => [
                ['task' => $dailyTask, 'completed' => true],
                ['task' => $deepWorkTask, 'completed' => true],
            ],
            $today->toDateString() => [
                ['task' => $dailyTask, 'completed' => false],
                ['task' => $deepWorkTask, 'completed' => false],
                ['task' => $weekendResetTask, 'completed' => false],
            ],
        ];

        foreach ($todoSchedule as $date => $entries) {
            $dateCarbon = Carbon::createFromFormat('Y-m-d', $date);

            foreach ($entries as $entry) {
                /** @var \App\Models\Task $task */
                $task = $entry['task'];
                $isCompleted = (bool) $entry['completed'];

                TaskOccurrence::query()->create([
                    'task_id' => $task->id,
                    'occurrence_date' => $dateCarbon->toDateString(),
                    'is_completed' => $isCompleted,
                    'completed_at' => $isCompleted ? $dateCarbon->copy()->setHour(18) : null,
                ]);
            }
        }
    }
}
