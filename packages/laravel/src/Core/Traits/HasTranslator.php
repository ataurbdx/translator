<?php

namespace Ataurbdx\Translator\Core\Traits;

use Ataurbdx\Translator\Modules\DynamicModels\Drivers\InlineTranslationDriver;
use Ataurbdx\Translator\Modules\DynamicModels\Drivers\InternalTranslationDriver;
use Ataurbdx\Translator\Modules\DynamicModels\Drivers\ExternalTranslationDriver;
use Ataurbdx\Translator\Modules\DynamicModels\Drivers\HybridTranslationDriver;
use Ataurbdx\Translator\Modules\DynamicModels\Models\TranslatorDynamic;
use Illuminate\Database\Eloquent\Builder;

trait HasTranslator
{
    /**
     * Boot the trait and register model lifecycle events
     */
    protected static function bootHasTranslator(): void
    {
        static::deleted(function ($model) {
            $driver = $model->getTranslatorDriver();
            $driver->delete($model);
        });
    }

    /**
     * Resolve the appropriate translation driver for this model
     */
    public function getTranslatorDriver()
    {
        $type = $this->translatorType ?? 'internal';

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
    public function translatorDynamics()
    {
        return $this->morphMany(TranslatorDynamic::class, 'translatable');
    }

    /**
     * Get translated value for a field
     */
    public function translate(string $field, ?string $locale = null, mixed $default = null): mixed
    {
        return $this->getTranslatorDriver()->get($this, $field, $locale, $default);
    }

    /**
     * Set a translation for a field
     */
    public function setTranslation(string $field, string|array $localeOrValues, mixed $value = null): self
    {
        $this->getTranslatorDriver()->set($this, $field, $localeOrValues, $value);
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
        $type = $this->translatorType ?? 'internal';

        if ($type === 'internal') {
            return $query->with('translatorDynamics');
        }

        return $query;
    }
}
