<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiSyncController;

Route::get('/ai-translation', [AiSyncController::class, 'index'])->name('ai.index');
Route::post('/ai-translation/text', [AiSyncController::class, 'translateText'])->name('ai.text');
Route::post('/ai-translation/post/{id}', [AiSyncController::class, 'translatePost'])->name('ai.post');
