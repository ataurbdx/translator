<?php

namespace Ataurbdx\Translator\Modules\DynamicModels\Drivers;

use Ataurbdx\Translator\Core\Contracts\TranslationDriverInterface;
use Illuminate\Database\Eloquent\Model;

class InlineTranslationDriver implements TranslationDriverInterface
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

        $raw = $model->getRawOriginal($field) ?? $model->getAttribute($field);

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $values = is_array($decoded) ? $decoded : null;
        } elseif (is_array($raw)) {
            $values = $raw;
        } else {
            $values = null;
        }

        if (is_array($values)) {
            if (!empty($values[$locale])) {
                return $values[$locale];
            }
            $fallbackLocale = config('translator.fallback_locale', 'en');
            if (!empty($values[$fallbackLocale])) {
                return $values[$fallbackLocale];
            }
        }

        return $default !== null ? $default : $raw;
    }

    public function set(mixed $target, string $field, string|array $localeOrValues, mixed $value = null): bool
    {
        $model = $target instanceof Model ? $target : ($this->model ?? null);
        if (!$model) {
            return false;
        }

        $raw = $model->getRawOriginal($field) ?? $model->getAttribute($field);
        $current = is_array($raw) ? $raw : (json_decode($raw, true) ?? []);

        if (is_array($localeOrValues)) {
            foreach ($localeOrValues as $loc => $val) {
                $current[$loc] = $val;
            }
        } else {
            $current[$localeOrValues] = $value;
        }

        $model->setAttribute($field, $current);
        return true;
    }

    public function delete(mixed $target, ?string $field = null): bool
    {
        $model = $target instanceof Model ? $target : ($this->model ?? null);
        if (!$model) {
            return false;
        }

        if ($field) {
            $model->setAttribute($field, null);
        }
        return true;
    }
}
