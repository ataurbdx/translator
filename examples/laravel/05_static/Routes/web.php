<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaticTranslationController;

Route::get('/static-ui', [StaticTranslationController::class, 'index'])->name('static.index');
Route::post('/static-ui', [StaticTranslationController::class, 'store'])->name('static.store');
