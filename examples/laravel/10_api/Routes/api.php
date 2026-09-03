<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HeadlessApiController;

Route::prefix('api/v1/translator')->group(function () {
    Route::get('/locales', [HeadlessApiController::class, 'locales']);
    Route::get('/static', [HeadlessApiController::class, 'staticStrings']);
    Route::post('/batch', [HeadlessApiController::class, 'batch']);
});
