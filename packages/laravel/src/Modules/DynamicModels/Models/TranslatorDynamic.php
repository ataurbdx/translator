<?php

namespace Ataurbdx\Translator\Modules\DynamicModels\Models;

use Illuminate\Database\Eloquent\Model;

class TranslatorDynamic extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'value' => 'array',
    ];

    public function getTable()
    {
        return config('translator.tables.dynamics', 'translator_dynamics');
    }

    /**
     * Polymorphic parent model relation
     */
    public function translatable()
    {
        return $this->morphTo();
    }
}
