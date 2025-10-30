<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodTemplate extends Model
{
    /** @use HasFactory<\Database\Factories\FoodTemplateFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'calories',
        'protein_g',
        'carbs_g',
        'fat_g',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'calories' => 'integer',
            'protein_g' => 'integer',
            'carbs_g' => 'integer',
            'fat_g' => 'integer',
        ];
    }

    public function foodEntries(): HasMany
    {
        return $this->hasMany(FoodEntry::class);
    }
}
