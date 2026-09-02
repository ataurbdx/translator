<?php

namespace Ataurbdx\TranslatorEngine\Modules\CulturalLocale\Models;

use Illuminate\Database\Eloquent\Model;

class TranslatorEngineLocale extends Model
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
        return config('translator_engine.tables.locales', 'translator_engine_locales');
    }
}
