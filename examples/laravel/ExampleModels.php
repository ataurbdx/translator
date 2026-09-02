<?php

namespace Ataurbdx\TranslatorEngine\Examples\Laravel;

use Illuminate\Database\Eloquent\Model;
use Ataurbdx\TranslatorEngine\Core\Traits\HasTranslatorEngine;

/**
 * Type 1: inline (In-table JSON column)
 */
class ExampleTag extends Model
{
    use HasTranslatorEngine;

    protected $table = 'example_tags';
    protected $guarded = ['id'];
    protected $translatorEngineType = 'inline';
    protected array $translatable = ['name'];
}

/**
 * Type 2: internal (Shared morph table: translator_engine_dynamics)
 */
class ExampleCategory extends Model
{
    use HasTranslatorEngine;

    protected $table = 'example_categories';
    protected $guarded = ['id'];
    protected $translatorEngineType = 'internal';
    protected array $translatable = ['name', 'description'];
}

/**
 * Type 3: external (Dedicated table: translator_engine_listings)
 */
class ExampleListing extends Model
{
    use HasTranslatorEngine;

    protected $table = 'example_listings';
    protected $guarded = ['id'];
    protected $translatorEngineType = 'external';
    protected string $translatorEngineTable = 'translator_engine_listings';
    protected array $translatable = ['title', 'description', 'address'];
}

/**
 * Type 4: hybrid (Grouped domain table: translator_engine_worlds)
 */
class ExampleCity extends Model
{
    use HasTranslatorEngine;

    protected $table = 'example_cities';
    protected $guarded = ['id'];
    protected $translatorEngineType = 'hybrid';
    protected string $translatorEngineTable = 'translator_engine_worlds';
    protected string $entityType = 'city';
    protected array $translatable = ['name', 'native'];
}
