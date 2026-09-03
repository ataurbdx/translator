<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CulturalFormatterController;

Route::get('/cultural-formatting', [CulturalFormatterController::class, 'index'])->name('cultural.index');
