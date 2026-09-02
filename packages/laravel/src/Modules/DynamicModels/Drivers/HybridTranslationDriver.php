<?php

namespace Ataurbdx\TranslatorEngine\Modules\DynamicModels\Drivers;

use Ataurbdx\TranslatorEngine\Core\Contracts\TranslationDriverInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HybridTranslationDriver implements TranslationDriverInterface
{
    protected ?Model $model;

    public function __construct(?Model $model = null)
    {
        $this->model = $model;
    }

    protected function getTableName(Model $model): string
    {
        return $model->translatorEngineTable ?? config('translator_engine.tables.prefix', 'translator_engine_') . 'worlds';
    }

    protected function getEntityType(Model $model): string
    {
        return $model->entityType ?? strtolower(class_basename($model));
    }

    public function get(mixed $target, string $field, ?string $locale = null, mixed $default = null): mixed
    {
        $model = $target instanceof Model ? $target : ($this->model ?? null);
        $locale = $locale ?? app()->getLocale();

        if (!$model) {
            return $default;
        }

        $fallback = $default !== null ? $default : ($model->getRawOriginal($field) ?? $model->getAttribute($field));

        $table = $this->getTableName($model);
        $entityType = $this->getEntityType($model);
        $id = $model->getKey();

        $cacheKey = "translator_engine_hybrid_{$table}_{$entityType}_{$id}_{$locale}_{$field}";
        $ttl = config('translator_engine.cache.ttl', 86400);

        return Cache::remember($cacheKey, $ttl, function () use ($table, $entityType, $id, $locale, $field, $fallback) {
            // First look for specific field column if exists
            $row = DB::table($table)
                ->where('entity_type', $entityType)
                ->where('entity_id', $id)
                ->where('locale', $locale)
                ->where('field', $field)
                ->first();

            if ($row && !empty($row->value)) {
                return $row->value;
            }

            // Fallback locale check
            $fallbackLocale = config('translator_engine.fallback_locale', 'en');
            if ($locale !== $fallbackLocale) {
                $fbRow = DB::table($table)
                    ->where('entity_type', $entityType)
                    ->where('entity_id', $id)
                    ->where('locale', $fallbackLocale)
                    ->where('field', $field)
                    ->first();

                if ($fbRow && !empty($fbRow->value)) {
                    return $fbRow->value;
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
        $entityType = $this->getEntityType($model);
        $id = $model->getKey();

        if (is_array($localeOrValues)) {
            foreach ($localeOrValues as $loc => $val) {
                $this->setSingle($table, $entityType, $id, $field, $loc, $val);
            }
        } else {
            $this->setSingle($table, $entityType, $id, $field, $localeOrValues, $value);
        }

        return true;
    }

    protected function setSingle(string $table, string $entityType, mixed $id, string $field, string $locale, mixed $value): void
    {
        $exists = DB::table($table)
            ->where('entity_type', $entityType)
            ->where('entity_id', $id)
            ->where('locale', $locale)
            ->where('field', $field)
            ->first();

        if ($exists) {
            DB::table($table)
                ->where('id', $exists->id)
                ->update(['value' => $value, 'updated_at' => now()]);
        } else {
            DB::table($table)->insert([
                'entity_type' => $entityType,
                'entity_id'   => $id,
                'locale'      => $locale,
                'field'       => $field,
                'value'       => $value,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        Cache::forget("translator_engine_hybrid_{$table}_{$entityType}_{$id}_{$locale}_{$field}");
    }

    public function delete(mixed $target, ?string $field = null): bool
    {
        $model = $target instanceof Model ? $target : ($this->model ?? null);
        if (!$model) {
            return false;
        }

        $table = $this->getTableName($model);
        $entityType = $this->getEntityType($model);

        $query = DB::table($table)
            ->where('entity_type', $entityType)
            ->where('entity_id', $model->getKey());

        if ($field) {
            $query->where('field', $field);
        }

        return (bool) $query->delete();
    }
}
