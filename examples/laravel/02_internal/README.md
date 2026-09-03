# Type 2: TranslationInternal (`internal`)

Translations are stored in a single, shared polymorphic table (**`translator_dynamics`**) using `translatable_type` and `translatable_id`.

---

## ⚡ Why Use Type 2?
- **Zero Schema Changes**: Your `categories` or `tags` table has no extra columns or language fields.
- **Single Shared Table**: 50 different models (Category, Brand, Department, Faq, Unit) share **1 single table**.
- **No Manual Relations**: You never write `hasOne(CategoryTranslation::class)`. `HasTranslator` wires up the morph relation automatically!
- **N+1 Solved**: Use `Category::withTranslations()->get()` to eager load all translations in 1 query.

---

## 🛠️ Step-by-Step Implementation

### 1. Install Internal Dynamic Table
```bash
php artisan translator:install --type=internal
```
Creates `translator_dynamics` table.

### 2. Model Setup
```php
use Ataurbdx\Translator\Core\Traits\HasTranslator;

class Category extends Model
{
    use HasTranslator;

    protected $translatorType = 'internal'; // defaults to 'internal' even if omitted
    protected array $translatable = ['name', 'description'];
}
```

### 3. Usage & Printing
```php
// Eager load translations (Prevents N+1 Query):
$categories = Category::withTranslations()->get();

// Auto-translated field
echo $category->name;

// Explicit language
echo $category->translate('name', 'bn');

// Access relationship directly:
$translations = $category->translations; // or $category->translatorDynamics
```
