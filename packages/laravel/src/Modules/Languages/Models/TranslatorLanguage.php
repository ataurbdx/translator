<?php

namespace Ataurbdx\Translator\Modules\Languages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class TranslatorLanguage extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'is_default' => 'boolean',
        'status'     => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getTable()
    {
        return config('translator.tables.languages', 'translator_languages');
    }

    /**
     * Scope for active languages
     */
    public function scopeActive($query)
    {
        return $query->where('status', true)->orderBy('sort_order', 'asc');
    }

    /**
     * Get all active languages with caching
     */
    public static function getActive()
    {
        $cachePrefix = config('translator.cache.prefix', 'translator_');
        return Cache::remember("{$cachePrefix}active_languages", 86400, function () {
            return static::active()->get();
        });
    }

    /**
     * Get default language
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Render flag gracefully whether it is a CSS class, SVG tag, HTML <img>, image URL, or Emoji
     */
    public function renderFlag(?string $extraClasses = ''): string
    {
        if (empty($this->flag)) {
            return '';
        }

        $flag = trim($this->flag);

        // 1. Raw SVG markup
        if (str_starts_with($flag, '<svg')) {
            return $flag;
        }

        // 2. HTML img tag
        if (str_starts_with($flag, '<img')) {
            return $flag;
        }

        // 3. Image URL (http://, https://, or /images/...)
        if (preg_match('/^(https?:\/\/|\/)/', $flag) || preg_match('/\.(svg|png|jpg|webp)$/i', $flag)) {
            return sprintf('<img src="%s" alt="%s" class="%s" />', e($flag), e($this->name), e($extraClasses));
        }

        // 4. CSS Class (Bootstrap / Tailwind / Flag-icons e.g. 'fi fi-us', 'flag-icon flag-icon-bd')
        return sprintf('<i class="%s %s"></i>', e($flag), e($extraClasses));
    }
}
