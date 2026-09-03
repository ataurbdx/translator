# Type 4: TranslationHybrid (`hybrid`)

Translations for an entire domain of related models are grouped into **one shared domain table** (e.g. `translator_worlds` for `Country`, `State`, `City`, `SubCity`).

---

## ⚡ Why Use Grouped Hybrid Tables?
- **Prevents Table Sprawl**: Instead of creating 5 separate translation tables (`translator_countries`, `translator_states`, `translator_cities`, `translator_districts`, `translator_areas`), they all live cleanly in `translator_worlds`.
- **Domain Isolation**: Keeps high-volume geo data isolated from the generic `translator_dynamics` table.
- **Unified Querying & Caching**: All locations in a single country can be queried or cached together.

---

## 🛠️ Step-by-Step Implementation

### 1. Generate Hybrid Domain Table
```bash
php artisan translator:make:hybrid worlds
```
Creates migration: `database/migrations/..._create_translator_worlds_table.php`.

Run the migration:
```bash
php artisan migrate
```

### 2. Model Setup
In `Country.php`:
```php
use Ataurbdx\Translator\Core\Traits\HasTranslator;

class Country extends Model
{
    use HasTranslator;

    protected $translatorType = 'hybrid';
    protected string $translatorTable = 'translator_worlds';
    protected string $entityType = 'country';
    protected array $translatable = ['name'];
}
```

In `City.php`:
```php
use Ataurbdx\Translator\Core\Traits\HasTranslator;

class City extends Model
{
    use HasTranslator;

    protected $translatorType = 'hybrid';
    protected string $translatorTable = 'translator_worlds'; // Same table!
    protected string $entityType = 'city';
    protected array $translatable = ['name'];
}
```

### 3. Usage & Printing
```php
// Both Country and City auto-resolve seamlessly from translator_worlds:
echo $country->name;
echo $city->name;

// Explicit language request:
echo $city->translate('name', 'bn');
```
