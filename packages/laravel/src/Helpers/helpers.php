<?php

use Ataurbdx\TranslatorEngine\Facades\TranslatorEngine;
use Ataurbdx\TranslatorEngine\Modules\Languages\Models\TranslatorEngineLanguage;

// =========================================================================
// THE ONE MASTER FUNCTION: translate()
// =========================================================================

if (!function_exists('translate')) {
    /**
     * The Master Universal Translation & Formatting Function
     *
     * Usage:
     * 1. Static Text:     translate('button.add_to_cart', 'bn')
     * 2. Digits:          translate('2026', type: 'digits', locale: 'bn')
     * 3. Numbers:         translate(1250000, type: 'number', decimals: 0, locale: 'bn')
     * 4. Dates:           translate(now(), type: 'date', withTime: true, locale: 'bn')
     * 5. Words:           translate(1500, type: 'words', currency: 'BDT', locale: 'bn')
     * 6. Flags:           translate('bn', type: 'flag', extraClasses: 'w-6 h-4')
     */
    function translate(
        mixed $value,
        ?string $locale = null,
        mixed $default = null,
        string $type = 'text',
        int $decimals = 0,
        bool $withTime = false,
        ?string $currency = null,
        ?string $extraClasses = ''
    ): mixed {
        $locale = $locale ?? app()->getLocale();

        return match ($type) {
            'digits' => TranslatorEngine::local()->digits($value, $locale),
            'number' => TranslatorEngine::local()->number($value, $decimals, $locale),
            'date'   => TranslatorEngine::local()->date($value, $withTime, $locale),
            'words'  => TranslatorEngine::local()->words($value, $currency, $locale),
            'flag'   => ($lang = TranslatorEngineLanguage::where('code', $value ?? $locale)->first()) 
                            ? $lang->renderFlag($extraClasses) 
                            : '',
            default  => TranslatorEngine::static()->get((string) $value, $locale, $default),
        };
    }
}
