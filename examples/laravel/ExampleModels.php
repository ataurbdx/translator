<?php

namespace Ataurbdx\Translator\Examples\Laravel;

use Illuminate\Database\Eloquent\Model;
use Ataurbdx\Translator\Core\Traits\HasTranslator;

// ============================================================
// TYPE 1: inline
// Translation stored as JSON directly inside the model's OWN table column.
// e.g. name = {"en":"Apple","bn":"আপেল","ar":"تفاحة"}
// Performance: 1 Query, 0 Joins, 0 Extra Tables
// ============================================================
class ExampleTag extends Model
{
    use HasTranslator;

    protected $table = 'example_tags';
    protected $guarded = ['id'];
    protected $casts = ['name' => 'array'];   // cast JSON column to array

    protected $translatorType = 'inline';
    protected array $translatable = ['name'];
}

// ============================================================
// TYPE 2: internal
// Translation stored in the shared polymorphic table:
//   translator_dynamics (translatable_type, translatable_id, locale, name, value)
// Best for: small-to-medium models (Category, Department, FaqCategory...)
// Performance: 1 extra query per model, no schema changes needed
// ============================================================
class ExampleCategory extends Model
{
    use HasTranslator;

    protected $table = 'example_categories';
    protected $guarded = ['id'];

    protected $translatorType = 'internal';
    protected array $translatable = ['name', 'description'];
}

// ============================================================
// TYPE 3: external
// Translation stored in a DEDICATED table per model.
//   translator_listings (listing_id, locale, field, value)
// Best for: high-traffic, high-volume models (Listing, Product, Post...)
// Generate migration: php artisan translator:make:external listings
// Performance: Maximum indexing, composite primary keys, built for millions of rows
// ============================================================
class ExampleListing extends Model
{
    use HasTranslator;

    protected $table = 'example_listings';
    protected $guarded = ['id'];

    protected $translatorType = 'external';
    protected string $translatorTable = 'translator_listings';
    protected array $translatable = ['title', 'description', 'address'];
}

// ============================================================
// TYPE 4: hybrid
// Translation stored in a GROUPED DOMAIN table shared across
// multiple related models using entity_type + entity_id.
//   translator_worlds (entity_type, entity_id, locale, field, value)
// Best for: world/geo clusters (Country, Division, City, SubCity sharing one table)
// Generate migration: php artisan translator:make:hybrid worlds
// Performance: Prevents table sprawl, keeps domain data isolated
// ============================================================
class ExampleCountry extends Model
{
    use HasTranslator;

    protected $table = 'example_countries';
    protected $guarded = ['id'];

    protected $translatorType = 'hybrid';
    protected string $translatorTable = 'translator_worlds';
    protected string $entityType = 'country';
    protected array $translatable = ['name', 'native'];
}

class ExampleCity extends Model
{
    use HasTranslator;

    protected $table = 'example_cities';
    protected $guarded = ['id'];

    protected $translatorType = 'hybrid';
    protected string $translatorTable = 'translator_worlds'; // same domain table!
    protected string $entityType = 'city';
    protected array $translatable = ['name', 'native'];
}
