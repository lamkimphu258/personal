<?php

use App\Http\Controllers\NutritionProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/profile', [NutritionProfileController::class, 'edit'])->name('profile.edit');
Route::post('/profile', [NutritionProfileController::class, 'update'])->name('profile.update');
