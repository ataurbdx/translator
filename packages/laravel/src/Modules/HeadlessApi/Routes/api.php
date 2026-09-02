<?php

use Ataurbdx\TranslatorEngine\Modules\HeadlessApi\Controllers\TranslatorEngineApiController;
use Illuminate\Support\Facades\Route;

$prefix = config('translator_engine.api.prefix', 'api/v1/translator-engine');
$middleware = config('translator_engine.api.middleware', ['api']);

Route::prefix($prefix)->middleware($middleware)->group(function () {
    Route::get('/static', [TranslatorEngineApiController::class, 'getStatic'])->name('translator-engine.api.static');
    Route::get('/locales', [TranslatorEngineApiController::class, 'getLocales'])->name('translator-engine.api.locales');
    Route::post('/batch', [TranslatorEngineApiController::class, 'batchTranslate'])->name('translator-engine.api.batch');
});
