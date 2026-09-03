<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ataurbdx\Translator\Core\Traits\HasTranslator;

class Tag extends Model
{
    use HasTranslator;

    protected $table = 'example_tags';
    protected $guarded = ['id'];

    // 1. Tell Translator this model uses In-Table JSON
    protected $translatorType = 'inline';

    // 2. Translatable fields
    protected array $translatable = ['name'];

    // 3. Cast the JSON column to array
    protected $casts = [
        'name' => 'array',
        'is_active' => 'boolean',
    ];
}
