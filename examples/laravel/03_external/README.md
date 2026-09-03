# Type 3: TranslationExternal (`external`)

Translations are stored in a dedicated, standalone table created specifically for that model (e.g. `translator_listings` for `Listing`, `translator_products` for `Product`).

---

## ⚡ When to Use Dedicated Tables?
- **Massive Traffic & Millions of Records**: When a single model has millions of rows (Listings, E-commerce Products, News Articles).
- **Composite Indexing**: Having a dedicated table allows custom MySQL composite indexes (`[listing_id, locale]`) for sub-millisecond query performance.
- **Independent Table Partitioning**: Dedicated tables can be sharded or partitioned independently without affecting other models.

---

## 🛠️ Step-by-Step Implementation

### 1. Generate Dedicated Table via Artisan
```bash
php artisan translator:make:external listings
```
Creates migration: `database/migrations/..._create_translator_listings_table.php`.

Run the migration:
```bash
php artisan migrate
```

### 2. Model Setup
```php
use Ataurbdx\Translator\Core\Traits\HasTranslator;

class Listing extends Model
{
    use HasTranslator;

    protected $translatorType = 'external';
    protected string $translatorTable = 'translator_listings';
    protected array $translatable = ['title', 'description', 'address'];
}
```

### 3. Usage & Outputting
```php
// Standard Eloquent auto-translated
echo $listing->title;

// Explicit language request
echo $listing->translate('title', 'bn');

// Saving translations
$listing->setTranslation('title', 'bn', 'বনানী ফ্ল্যাট')->save();
```
