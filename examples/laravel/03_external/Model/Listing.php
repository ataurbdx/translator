<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ataurbdx\Translator\Core\Traits\HasTranslator;

class Listing extends Model
{
    use HasTranslator;

    protected $table = 'example_listings';
    protected $guarded = ['id'];

    // 1. Tell Translator this model has a dedicated translation table
    protected $translatorType = 'external';

    // 2. Specify dedicated translation table name (defaults to translator_{table})
    protected string $translatorTable = 'translator_listings';

    // 3. Translatable columns in translator_listings table
    protected array $translatable = ['title', 'description', 'address'];

    protected $casts = [
        'price' => 'decimal:2',
    ];
}
