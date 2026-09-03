<?php

use Ataurbdx\Translator\Modules\HeadlessApi\Controllers\TranslatorApiController;
use Illuminate\Support\Facades\Route;

$prefix = config('translator.api.prefix', 'api/v1/translator');
$middleware = config('translator.api.middleware', ['api']);

Route::prefix($prefix)->middleware($middleware)->group(function () {
    Route::get('/static', [TranslatorApiController::class, 'getStatic'])->name('translator.api.static');
    Route::get('/locales', [TranslatorApiController::class, 'getLocales'])->name('translator.api.locales');
    Route::post('/batch', [TranslatorApiController::class, 'batchTranslate'])->name('translator.api.batch');
});
