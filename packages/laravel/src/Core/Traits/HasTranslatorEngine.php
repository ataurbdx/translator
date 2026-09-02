<?php

namespace Ataurbdx\TranslatorEngine\Core\Traits;

use Ataurbdx\TranslatorEngine\Modules\DynamicModels\Drivers\InlineTranslationDriver;
use Ataurbdx\TranslatorEngine\Modules\DynamicModels\Drivers\InternalTranslationDriver;
use Ataurbdx\TranslatorEngine\Modules\DynamicModels\Drivers\ExternalTranslationDriver;
use Ataurbdx\TranslatorEngine\Modules\DynamicModels\Drivers\HybridTranslationDriver;
use Ataurbdx\TranslatorEngine\Modules\DynamicModels\Models\TranslatorEngineDynamic;
use Illuminate\Database\Eloquent\Builder;

trait HasTranslatorEngine
{
    /**
     * Boot the trait and register model lifecycle events
     */
    protected static function bootHasTranslatorEngine(): void
    {
        static::deleted(function ($model) {
            $driver = $model->getTranslatorEngineDriver();
            $driver->delete($model);
        });
    }

    /**
     * Resolve the appropriate translation driver for this model
     */
    public function getTranslatorEngineDriver()
    {
        $type = $this->translatorEngineType ?? 'internal';

        return match ($type) {
            'inline'   => new InlineTranslationDriver($this),
            'internal' => new InternalTranslationDriver($this),
            'external' => new ExternalTranslationDriver($this),
            'hybrid'   => new HybridTranslationDriver($this),
            default    => new InternalTranslationDriver($this),
        };
    }

    /**
     * Internal morph relationship for Type 2 (internal)
     */
    public function translatorEngineDynamics()
    {
        return $this->morphMany(TranslatorEngineDynamic::class, 'translatable');
    }

    /**
     * Get translated value for a field
     */
    public function translate(string $field, ?string $locale = null, mixed $default = null): mixed
    {
        return $this->getTranslatorEngineDriver()->get($this, $field, $locale, $default);
    }

    /**
     * Set a translation for a field
     */
    public function setTranslation(string $field, string|array $localeOrValues, mixed $value = null): self
    {
        $this->getTranslatorEngineDriver()->set($this, $field, $localeOrValues, $value);
        return $this;
    }

    /**
     * Save multiple translations from array (e.g. from request payload)
     * Format: ['name' => ['en' => '...', 'bn' => '...']]
     */
    public function saveTranslations(array $data): self
    {
        $translatable = $this->translatable ?? [];

        foreach ($data as $field => $locales) {
            if (in_array($field, $translatable) && is_array($locales)) {
                $this->setTranslation($field, $locales);
            }
        }

        return $this;
    }

    /**
     * Overridden to seamlessly auto-translate translatable attributes
     */
    public function getAttribute($key)
    {
        $translatable = $this->translatable ?? [];

        if (in_array($key, $translatable)) {
            return $this->translate($key);
        }

        return parent::getAttribute($key);
    }

    /**
     * Overridden for JSON serialization (ensures API / Flutter receives translated attributes)
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();
        $translatable = $this->translatable ?? [];

        foreach ($translatable as $field) {
            $attributes[$field] = $this->getAttribute($field);
        }

        return $attributes;
    }

    /**
     * Scope to eager load translations to eliminate N+1 queries
     */
    public function scopeWithTranslations(Builder $query): Builder
    {
        $type = $this->translatorEngineType ?? 'internal';

        if ($type === 'internal') {
            return $query->with('translatorEngineDynamics');
        }

        return $query;
    }
}
