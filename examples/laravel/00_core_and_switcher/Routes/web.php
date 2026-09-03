<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageSwitcherController;

// Language Switcher Route
Route::get('/language/{locale}', [LanguageSwitcherController::class, 'switch'])
    ->name('language.switch');
