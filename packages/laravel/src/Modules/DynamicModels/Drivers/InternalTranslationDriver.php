<?php

namespace Ataurbdx\Translator\Modules\DynamicModels\Drivers;

use Ataurbdx\Translator\Core\Contracts\TranslationDriverInterface;
use Ataurbdx\Translator\Modules\DynamicModels\Models\TranslatorDynamic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class InternalTranslationDriver implements TranslationDriverInterface
{
    protected ?Model $model;

    public function __construct(?Model $model = null)
    {
        $this->model = $model;
    }

    public function get(mixed $target, string $field, ?string $locale = null, mixed $default = null): mixed
    {
        $model = $target instanceof Model ? $target : ($this->model ?? null);
        $locale = $locale ?? app()->getLocale();

        if (!$model) {
            return $default;
        }

        $fallback = $default !== null ? $default : ($model->getRawOriginal($field) ?? $model->getAttribute($field));

        // 1. Check if translations relation is eager loaded
        if ($model->relationLoaded('translatorDynamics')) {
            $relation = $model->translatorDynamics;
        }

        if ($relation) {
            $record = $relation->where('name', $field)->first();
            if ($record && is_array($record->value)) {
                if (!empty($record->value[$locale])) {
                    return $record->value[$locale];
                }
                $fallbackLocale = config('translator.fallback_locale', 'en');
                if (!empty($record->value[$fallbackLocale])) {
                    return $record->value[$fallbackLocale];
                }
                return $fallback;
            }
        }

        // 2. Query with cache
        $cacheKey = "translator_internal_" . get_class($model) . "_{$model->getKey()}_{$field}";
        $ttl = config('translator.cache.ttl', 86400);

        $translations = Cache::remember($cacheKey, $ttl, function () use ($model, $field) {
            $record = TranslatorDynamic::where('translatable_type', $model->getMorphClass())
                ->where('translatable_id', $model->getKey())
                ->where('name', $field)
                ->first();

            return $record ? $record->value : null;
        });

        if (is_array($translations)) {
            if (!empty($translations[$locale])) {
                return $translations[$locale];
            }
            $fallbackLocale = config('translator.fallback_locale', 'en');
            if (!empty($translations[$fallbackLocale])) {
                return $translations[$fallbackLocale];
            }
        }

        return $fallback;
    }

    public function set(mixed $target, string $field, string|array $localeOrValues, mixed $value = null): bool
    {
        $model = $target instanceof Model ? $target : ($this->model ?? null);
        if (!$model || !$model->exists) {
            return false;
        }

        $record = TranslatorDynamic::firstOrNew([
            'translatable_type' => $model->getMorphClass(),
            'translatable_id'   => $model->getKey(),
            'name'              => $field,
        ]);

        $current = $record->value ?? [];

        if (is_array($localeOrValues)) {
            foreach ($localeOrValues as $loc => $val) {
                if ($val !== null && $val !== '') {
                    $current[$loc] = $val;
                } else {
                    unset($current[$loc]);
                }
            }
        } else {
            if ($value !== null && $value !== '') {
                $current[$localeOrValues] = $value;
            } else {
                unset($current[$localeOrValues]);
            }
        }

        if (empty($current)) {
            if ($record->exists) {
                $record->delete();
            }
        } else {
            $record->value = $current;
            $record->save();
        }

        // Invalidate cache
        $cacheKey = "translator_internal_" . get_class($model) . "_{$model->getKey()}_{$field}";
        Cache::forget($cacheKey);

        return true;
    }

    public function delete(mixed $target, ?string $field = null): bool
    {
        $model = $target instanceof Model ? $target : ($this->model ?? null);
        if (!$model) {
            return false;
        }

        $query = TranslatorDynamic::where('translatable_type', $model->getMorphClass())
            ->where('translatable_id', $model->getKey());

        if ($field) {
            $query->where('name', $field);
            Cache::forget("translator_internal_" . get_class($model) . "_{$model->getKey()}_{$field}");
        } else {
            foreach ($model->translatable ?? [] as $f) {
                Cache::forget("translator_internal_" . get_class($model) . "_{$model->getKey()}_{$f}");
            }
        }

        return (bool) $query->delete();
    }
}
