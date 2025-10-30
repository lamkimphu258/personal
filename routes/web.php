<?php

use App\Http\Controllers\FoodTemplateController;
use App\Http\Controllers\NutritionProfileController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/profile', [NutritionProfileController::class, 'edit'])->name('profile.edit');
Route::post('/profile', [NutritionProfileController::class, 'update'])->name('profile.update');

Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
Route::post('/tracking/weight', [TrackingController::class, 'storeWeight'])->name('tracking.weight.store');
Route::post('/tracking/food', [TrackingController::class, 'storeFood'])->name('tracking.food.store');
Route::get('/tracking/food/{foodEntry}/edit', [TrackingController::class, 'editFood'])->name('tracking.food.edit');
Route::delete('/tracking/food/{foodEntry}', [TrackingController::class, 'destroyFood'])->name('tracking.food.destroy');

Route::get('/food-templates', [FoodTemplateController::class, 'index'])->name('food-templates.index');
Route::post('/food-templates', [FoodTemplateController::class, 'store'])->name('food-templates.store');
Route::get('/food-templates/{foodTemplate}', [FoodTemplateController::class, 'show'])->name('food-templates.show');
