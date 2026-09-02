<?php

namespace Ataurbdx\TranslatorEngine\Modules\HeadlessApi\Middleware;

use Closure;
use Illuminate\Http\Request;

class TranslatorEngineLocaleMiddleware
{
    /**
     * Automatically set the application locale based on incoming request header, query, or session
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->query('locale')
            ?? $request->header('Accept-Language')
            ?? $request->header('X-Locale')
            ?? session('locale')
            ?? config('translator_engine.default_locale', 'en');

        // Sanitize locale (e.g., 'en-US' -> 'en')
        if (str_contains($locale, '-')) {
            $locale = explode('-', $locale)[0];
        }
        if (str_contains($locale, ',')) {
            $locale = explode(',', $locale)[0];
        }

        $supported = array_keys(config('translator_engine.supported_locales', ['en' => [], 'bn' => []]));

        if (in_array($locale, $supported)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
