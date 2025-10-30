<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NutritionProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'age',
        'sex',
        'current_weight_kg',
        'goal_weight_kg',
        'height_cm',
        'activity_level',
        'desired_loss_per_week_kg',
        'calorie_target',
        'protein_grams',
        'fat_grams',
        'carbohydrate_grams',
        'fibre_grams',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'current_weight_kg' => 'float',
            'goal_weight_kg' => 'float',
            'height_cm' => 'integer',
            'desired_loss_per_week_kg' => 'float',
            'calorie_target' => 'integer',
            'protein_grams' => 'integer',
            'fat_grams' => 'integer',
            'carbohydrate_grams' => 'integer',
            'fibre_grams' => 'integer',
        ];
    }
}
