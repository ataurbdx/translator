<?php

namespace Ataurbdx\TranslatorEngine\Modules\StaticUI\Drivers;

use Ataurbdx\TranslatorEngine\Modules\StaticUI\Models\TranslatorEngineStatic;
use Illuminate\Support\Facades\Cache;

class StaticTranslationDriver
{
    /**
     * Get a static translation by key with multi-tier fallback
     */
    public function get(string $key, ?string $locale = null, mixed $default = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $cachePrefix = config('translator_engine.cache.prefix', 'translator_engine_');
        $cacheKey = "{$cachePrefix}static_{$key}_{$locale}";
        $ttl = config('translator_engine.cache.ttl', 86400);

        return Cache::remember($cacheKey, $ttl, function () use ($key, $locale, $default) {
            $record = TranslatorEngineStatic::where('key', $key)->first();

            if ($record) {
                if (!empty($record->value[$locale])) {
                    return $record->value[$locale];
                }

                $fallbackLocale = config('translator_engine.fallback_locale', 'en');
                if (!empty($record->value[$fallbackLocale])) {
                    return $record->value[$fallbackLocale];
                }

                if (!empty($record->name)) {
                    return $record->name;
                }
            }

            // Fallback to default or the key itself
            return $default !== null ? $default : $key;
        });
    }

    /**
     * Set or update a static key
     */
    public function set(string $key, string $name, array $values = [], string $group = 'common'): TranslatorEngineStatic
    {
        $record = TranslatorEngineStatic::updateOrCreate(
            ['key' => $key],
            [
                'name'  => $name,
                'value' => $values,
                'group' => $group,
            ]
        );

        $this->forget($key);
        return $record;
    }

    /**
     * Clear cache for a specific key
     */
    public function forget(string $key): void
    {
        $cachePrefix = config('translator_engine.cache.prefix', 'translator_engine_');
        $locales = array_keys(config('translator_engine.supported_locales', ['en', 'bn']));

        foreach ($locales as $loc) {
            Cache::forget("{$cachePrefix}static_{$key}_{$loc}");
        }
    }
}
