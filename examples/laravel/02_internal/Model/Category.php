<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ataurbdx\Translator\Core\Traits\HasTranslator;

class Category extends Model
{
    use HasTranslator;

    protected $table = 'example_categories';
    protected $guarded = ['id'];

    // 1. Tell Translator this model uses the shared polymorphic table (translator_dynamics)
    protected $translatorType = 'internal';

    // 2. Translatable fields
    protected array $translatable = ['name', 'description'];
}
