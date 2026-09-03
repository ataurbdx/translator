<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListingController;

Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');
