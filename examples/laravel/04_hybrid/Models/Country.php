<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ataurbdx\Translator\Core\Traits\HasTranslator;

class Country extends Model
{
    use HasTranslator;

    protected $table = 'example_countries';
    protected $guarded = ['id'];

    // 1. Tell Translator this model belongs to a grouped hybrid domain table
    protected $translatorType = 'hybrid';

    // 2. Shared domain table name
    protected string $translatorTable = 'translator_worlds';

    // 3. Entity type discriminator in translator_worlds
    protected string $entityType = 'country';

    // 4. Translatable fields
    protected array $translatable = ['name'];

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
