<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Ataurbdx\Translator\Modules\Languages\Models\TranslatorLanguage;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request and apply the user's active locale.
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Determine active locale (Priority: Session -> Cookie -> Header -> Config Default)
        $locale = session('locale', $request->cookie('locale'));

        if (!$locale && $request->hasHeader('Accept-Language')) {
            $locale = substr($request->header('Accept-Language'), 0, 2);
        }

        if (!$locale) {
            $defaultLang = TranslatorLanguage::where('is_default', true)->first();
            $locale = $defaultLang ? $defaultLang->code : config('translator.default_locale', 'en');
        }

        // 2. Set application locale
        app()->setLocale($locale);

        return $next($request);
    }
}
