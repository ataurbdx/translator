<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ataurbdx\Translator\Core\Traits\HasTranslator;

class City extends Model
{
    use HasTranslator;

    protected $table = 'example_cities';
    protected $guarded = ['id'];

    // 1. Tell Translator this model belongs to the same grouped hybrid domain table
    protected $translatorType = 'hybrid';

    // 2. Shares the same translator_worlds table with Country!
    protected string $translatorTable = 'translator_worlds';

    // 3. Entity type discriminator in translator_worlds
    protected string $entityType = 'city';

    // 4. Translatable fields
    protected array $translatable = ['name'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
