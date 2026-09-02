<?php

namespace Ataurbdx\TranslatorEngine\Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class TranslatorEngineSetting extends Model
{
    protected $guarded = ['id'];

    public function getTable()
    {
        return config('translator_engine.tables.settings', 'translator_engine_settings');
    }

    /**
     * Get typed value attribute
     */
    public function getParsedValueAttribute(): mixed
    {
        return match ($this->type) {
            'boolean'   => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'json'      => json_decode($this->value, true) ?? [],
            'encrypted' => $this->value ? Crypt::decryptString($this->value) : null,
            default     => $this->value,
        };
    }

    /**
     * Helper to get setting by key
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->parsed_value : $default;
    }

    /**
     * Helper to set setting by key
     */
    public static function set(string $key, mixed $value, string $type = 'string', string $group = 'general'): self
    {
        $rawValue = match ($type) {
            'boolean'   => $value ? '1' : '0',
            'json'      => is_array($value) ? json_encode($value) : $value,
            'encrypted' => $value ? Crypt::encryptString($value) : null,
            default     => (string) $value,
        };

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $rawValue, 'type' => $type, 'group' => $group]
        );
    }
}
