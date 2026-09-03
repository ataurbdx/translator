<?php

namespace Ataurbdx\Translator\Modules\StaticUI\Drivers;

use Ataurbdx\Translator\Modules\StaticUI\Models\TranslatorStatic;
use Illuminate\Support\Facades\Cache;

class StaticTranslationDriver
{
    /**
     * Get a static translation by key with multi-tier fallback
     */
    public function get(string $key, ?string $locale = null, mixed $default = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $cachePrefix = config('translator.cache.prefix', 'translator_');
        $cacheKey = "{$cachePrefix}static_{$key}_{$locale}";
        $ttl = config('translator.cache.ttl', 86400);

        return Cache::remember($cacheKey, $ttl, function () use ($key, $locale, $default) {
            $record = TranslatorStatic::where('key', $key)->first();

            if ($record) {
                if (!empty($record->value[$locale])) {
                    return $record->value[$locale];
                }

                $fallbackLocale = config('translator.fallback_locale', 'en');
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
    public function set(string $key, string $name, array $values = [], string $group = 'common'): TranslatorStatic
    {
        $record = TranslatorStatic::updateOrCreate(
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
        $cachePrefix = config('translator.cache.prefix', 'translator_');
        $locales = array_keys(config('translator.supported_locales', ['en', 'bn']));

        foreach ($locales as $loc) {
            Cache::forget("{$cachePrefix}static_{$key}_{$loc}");
        }
    }
}
