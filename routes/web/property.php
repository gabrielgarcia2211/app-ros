<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Property\PropertyController;

Route::prefix('properties')->middleware(['auth'])->group(function () {
    Route::get('/list', [PropertyController::class, 'index'])->name('properties.index');
    Route::post('/store', [PropertyController::class, 'store'])->name('properties.store');
    Route::delete('/{id}', [PropertyController::class, 'destroy'])->name('properties.destroy');
});