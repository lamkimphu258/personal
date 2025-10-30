<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nutrition_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('age');
            $table->enum('sex', ['female', 'male']);
            $table->decimal('current_weight_kg', 5, 1);
            $table->decimal('goal_weight_kg', 5, 1);
            $table->unsignedSmallInteger('height_cm');
            $table->enum('activity_level', ['sedentary', 'lightly-active', 'moderately-active', 'very-active', 'athlete']);
            $table->decimal('desired_loss_per_week_kg', 4, 2);
            $table->unsignedSmallInteger('calorie_target');
            $table->unsignedSmallInteger('protein_grams');
            $table->unsignedSmallInteger('fat_grams');
            $table->unsignedSmallInteger('carbohydrate_grams');
            $table->unsignedSmallInteger('fibre_grams');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrition_profiles');
    }
};
