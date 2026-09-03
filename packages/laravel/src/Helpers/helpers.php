<?php

use Ataurbdx\Translator\Facades\Translator;
use Ataurbdx\Translator\Modules\Languages\Models\TranslatorLanguage;

// =========================================================================
// THE MASTER TRANSLATION FUNCTIONS: translate() and translator()
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
            'digits' => Translator::local()->digits($value, $locale),
            'number' => Translator::local()->number($value, $decimals, $locale),
            'date'   => Translator::local()->date($value, $withTime, $locale),
            'words'  => Translator::local()->words($value, $currency, $locale),
            'flag'   => ($lang = TranslatorLanguage::where('code', $value ?? $locale)->first()) 
                            ? $lang->renderFlag($extraClasses) 
                            : '',
            default  => Translator::static()->get((string) $value, $locale, $default),
        };
    }
}

if (!function_exists('translator')) {
    /**
     * Fluent access to the Translator engine or quick translate helper
     */
    function translator(mixed $key = null, ?string $locale = null, mixed $default = null): mixed {
        if ($key === null) {
            return app('translator');
        }

        return translate($key, $locale, $default);
    }
}
