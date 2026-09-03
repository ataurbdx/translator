<?php

namespace Ataurbdx\Translator\Modules\CulturalLocale\Models;

use Illuminate\Database\Eloquent\Model;

class TranslatorLocale extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'digits'       => 'array',
        'months'       => 'array',
        'days'         => 'array',
        'extra_config' => 'array',
        'is_active'    => 'boolean',
        'is_default'   => 'boolean',
    ];

    public function getTable()
    {
        return config('translator.tables.locales', 'translator_locales');
    }
}
