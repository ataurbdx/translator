<?php

namespace Ataurbdx\TranslatorEngine\Core\Contracts;

interface TranslationDriverInterface
{
    /**
     * Get translated value for a field/key in a specific locale.
     */
    public function get(mixed $target, string $field, ?string $locale = null, mixed $default = null): mixed;

    /**
     * Set/save a translation value.
     */
    public function set(mixed $target, string $field, string|array $localeOrValues, mixed $value = null): bool;

    /**
     * Delete translations for a target.
     */
    public function delete(mixed $target, ?string $field = null): bool;
}
