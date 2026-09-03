# Type 1: TranslationInline (`inline`)

Translations are stored directly inside the model table's JSON column (`name` = `{"en":"Electronics","bn":"ইলেকট্রনিক্স"}`).

---

## ⚡ Key Characteristics & Performance
- **Zero Extra Tables**: No translation tables needed.
- **Zero Database Joins**: Everything comes in 1 single select query.
- **Best For**: Small, frequently read lookup models (Tags, Badges, Statuses, Units).

---

## 🛠️ Step-by-Step Implementation

### 1. Migration
Add a `json` column:
```php
$table->json('name');
```
*(Or convert an existing column using `php artisan translator:make:inline tags name`)*

### 2. Model Setup
```php
use Ataurbdx\Translator\Core\Traits\HasTranslator;

class Tag extends Model
{
    use HasTranslator;

    protected $translatorType = 'inline';
    protected array $translatable = ['name'];

    protected $casts = [
        'name' => 'array',
    ];
}
```

### 3. Printing / Outputting in Blade & Controllers
```php
// Way A: Auto-resolves by app locale
echo $tag->name;

// Way B: Explicit locale
echo $tag->translate('name', 'bn');

// Way C: Direct engine call without model instance
Translator::inline(Tag::class)->get($tagId, 'name', 'es');
```
