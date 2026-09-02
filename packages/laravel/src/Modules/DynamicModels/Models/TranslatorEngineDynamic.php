<?php

namespace Ataurbdx\TranslatorEngine\Modules\DynamicModels\Models;

use Illuminate\Database\Eloquent\Model;

class TranslatorEngineDynamic extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'value' => 'array',
    ];

    public function getTable()
    {
        return config('translator_engine.tables.dynamics', 'translator_engine_dynamics');
    }

    /**
     * Polymorphic parent model relation
     */
    public function translatable()
    {
        return $this->morphTo();
    }
}
