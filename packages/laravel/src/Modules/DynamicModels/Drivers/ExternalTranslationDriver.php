<?php

namespace Ataurbdx\Translator\Modules\DynamicModels\Drivers;

use Ataurbdx\Translator\Core\Contracts\TranslationDriverInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ExternalTranslationDriver implements TranslationDriverInterface
{
    protected ?Model $model;

    public function __construct(?Model $model = null)
    {
        $this->model = $model;
    }

    protected function getTableName(Model $model): string
    {
        if (!empty($model->translatorTable)) {
            return $model->translatorTable;
        }

        $base = $model->getTable();
        $prefix = config('translator.tables.prefix', 'translator_');
        return $prefix . $base;
    }

    protected function getForeignKey(Model $model): string
    {
        return $model->getForeignKey();
    }

    public function get(mixed $target, string $field, ?string $locale = null, mixed $default = null): mixed
    {
        $model = $target instanceof Model ? $target : ($this->model ?? null);
        $locale = $locale ?? app()->getLocale();

        if (!$model) {
            return $default;
        }

        $fallback = $default !== null ? $default : ($model->getRawOriginal($field) ?? $model->getAttribute($field));

        // 1. Check if translation relation is eager loaded
        $relation = null;
        if ($model->relationLoaded('translatorExternal')) {
            $relation = $model->translatorExternal;
        }

        if ($relation) {
            $record = $relation->where('locale', $locale)->first();
            if ($record && !empty($record->{$field})) {
                return $record->{$field};
            }
            // Check fallback locale
            $fallbackLocale = config('translator.fallback_locale', 'en');
            $fbRecord = $relation->where('locale', $fallbackLocale)->first();
            if ($fbRecord && !empty($fbRecord->{$field})) {
                return $fbRecord->{$field};
            }
            return $fallback;
        }

        // 2. Query with cache
        $table = $this->getTableName($model);
        $fk = $this->getForeignKey($model);
        $cacheKey = "translator_ext_{$table}_{$model->getKey()}_{$locale}_{$field}";
        $ttl = config('translator.cache.ttl', 86400);

        return Cache::remember($cacheKey, $ttl, function () use ($model, $table, $fk, $locale, $field, $fallback) {
            $row = DB::table($table)
                ->where($fk, $model->getKey())
                ->where('locale', $locale)
                ->first();

            if ($row && !empty($row->{$field})) {
                return $row->{$field};
            }

            // Fallback locale check
            $fallbackLocale = config('translator.fallback_locale', 'en');
            if ($locale !== $fallbackLocale) {
                $fbRow = DB::table($table)
                    ->where($fk, $model->getKey())
                    ->where('locale', $fallbackLocale)
                    ->first();

                if ($fbRow && !empty($fbRow->{$field})) {
                    return $fbRow->{$field};
                }
            }

            return $fallback;
        });
    }

    public function set(mixed $target, string $field, string|array $localeOrValues, mixed $value = null): bool
    {
        $model = $target instanceof Model ? $target : ($this->model ?? null);
        if (!$model || !$model->exists) {
            return false;
        }

        $table = $this->getTableName($model);
        $fk = $this->getForeignKey($model);

        if (is_array($localeOrValues)) {
            foreach ($localeOrValues as $loc => $val) {
                $this->setSingle($table, $fk, $model->getKey(), $field, $loc, $val);
            }
        } else {
            $this->setSingle($table, $fk, $model->getKey(), $field, $localeOrValues, $value);
        }

        return true;
    }

    protected function setSingle(string $table, string $fk, mixed $id, string $field, string $locale, mixed $value): void
    {
        $exists = DB::table($table)->where($fk, $id)->where('locale', $locale)->first();

        if ($exists) {
            DB::table($table)->where($fk, $id)->where('locale', $locale)->update([
                $field => $value,
                'updated_at' => now(),
            ]);
        } else {
            DB::table($table)->insert([
                $fk => $id,
                'locale' => $locale,
                $field => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Cache::forget("translator_ext_{$table}_{$id}_{$locale}_{$field}");
    }

    public function delete(mixed $target, ?string $field = null): bool
    {
        $model = $target instanceof Model ? $target : ($this->model ?? null);
        if (!$model) {
            return false;
        }

        $table = $this->getTableName($model);
        $fk = $this->getForeignKey($model);

        return (bool) DB::table($table)->where($fk, $model->getKey())->delete();
    }
}
