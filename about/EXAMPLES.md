# Master Translation Engine (`TranslatorEngine`)
## Complete API Reference & Examples for All 10 Translation Types

This guide provides exhaustive code examples showing **both ways to call each of the 10 types**:
1. **Via Direct Engine Call**: `TranslatorEngine::type(...)` or Shorthand `TranslatorEngine::[type](...)`
2. **Via Eloquent Models / Blade Helpers**: Natural `$model->field` and `translate(...)` syntax.

---

### Master Summary: How All 10 Types Are Called

> **Universal "Zero-Blank" Guarantee**: Every single translation type automatically falls back to its original default value (e.g. `$category->getRawOriginal('title')` or `translator_statics.default`) if the requested language is missing. It never returns blank or broken text.

| # | Type | Direct Engine Call | Model / View Call |
|---|---|---|---|
| **1** | `inline` | `TranslatorEngine::inline(Tag::class)->get($tagId, 'name', 'bn')` | `$tag->name` or `$tag->translate('name', 'bn')` |
| **2** | `internal` | `TranslatorEngine::internal(Category::class)->get($id, 'name', 'bn')` | `$category->name` or `$category->translate('name', 'bn')` |
| **3** | `external` | `TranslatorEngine::external(Listing::class)->get($id, 'title', 'bn')` | `$listing->title` or `$listing->translate('title', 'bn')` |
| **4** | `hybrid` | `TranslatorEngine::hybrid(City::class)->get($id, 'name', 'bn')` | `$city->name` or `$city->translate('name', 'bn')` |
| **5** | `static` | `TranslatorEngine::static()->get('button.add_to_cart', 'bn')` | `{{ translate('button.add_to_cart') }}` |
| **6** | `json` | `TranslatorEngine::json()->get('Add to Cart', 'bn')` | `{{ __('Add to Cart') }}` |
| **7** | `file` | `TranslatorEngine::file()->get('auth.failed', 'bn')` | `{{ __('auth.failed') }}` |
| **8** | `local` | `TranslatorEngine::local()->digits('2026', 'bn')` | `{{ translator_digits('2026') }}` |
| **9** | `ai` | `TranslatorEngine::ai()->translate('Hello', 'en', 'bn')` | `TranslatorEngine::ai()->syncModel($listing, ['title'])` |
| **10** | `api` | `TranslatorEngine::api()->export('bn')` | Route: `GET /api/v1/translator-engine/...` |

---

## 1. Type 1: `TranslationInline` (`inline`)
Translations are stored directly inside the model table's JSON column.

### Way A: Direct Engine Call (Without Model Instance)
```php
use Ataurbdx\TranslatorEngine\Facades\TranslatorEngine;

// Long syntax with type():
$value = TranslatorEngine::type('inline', Tag::class)->get($tagId, 'name', 'bn');

// Fluent shorthand:
$value = TranslatorEngine::inline(Tag::class)->get($tagId, 'name', 'bn');

// Setting a translation directly:
TranslatorEngine::inline(Tag::class)->set($tagId, 'name', 'bn', 'নতুন ট্যাগ');
```

### Way B: Via Eloquent Model
```php
class Tag extends Model
{
    use HasTranslatorEngine;

    protected $translatorEngineType = 'inline';
    protected array $translatable = ['name'];
}

// In Controller or Blade:
$tag = Tag::find(1);
echo $tag->name;                      // Auto-resolves based on app()->getLocale()
echo $tag->translate('name', 'bn');   // Explicit locale
$tag->setTranslation('name', 'bn', 'নতুন')->save();
```

---

## 2. Type 2: `TranslationInternal` (`internal`)
Shared polymorphic table (`translator_engine_dynamics`) for multiple small/medium models.

### Way A: Direct Engine Call
```php
// Long syntax:
$name = TranslatorEngine::type('internal', Category::class)->get($categoryId, 'name', 'bn');

// Fluent shorthand:
$name = TranslatorEngine::internal(Category::class)->get($categoryId, 'name', 'bn');

// Set translation:
TranslatorEngine::internal(Category::class)->set($categoryId, 'name', 'bn', 'ইলেকট্রনিক্স');
```

### Way B: Via Eloquent Model
```php
class Category extends Model
{
    use HasTranslatorEngine;

    protected $translatorEngineType = 'internal';
    protected array $translatable = ['name', 'description'];
}

$cat = Category::find(1);
echo $cat->name;
echo $cat->translate('name', 'bn');
```

---

## 3. Type 3: `TranslationExternal` (`external`)
Dedicated translation table per model (`translator_engine_listings`) for high-traffic models.

### Way A: Direct Engine Call
```php
// Long syntax:
$title = TranslatorEngine::type('external', Listing::class)->get($listingId, 'title', 'bn');

// Fluent shorthand:
$title = TranslatorEngine::external(Listing::class)->get($listingId, 'title', 'bn');

// Set translation:
TranslatorEngine::external(Listing::class)->set($listingId, 'title', 'bn', 'বনানী বিলাসবহুল ফ্ল্যাট');
```

### Way B: Via Eloquent Model
```php
class Listing extends Model
{
    use HasTranslatorEngine;

    protected $translatorEngineType = 'external';
    protected string $translatorEngineTable = 'translator_engine_listings'; // Auto-inferred
    protected array $translatable = ['title', 'description', 'address'];
}

$listing = Listing::find(1);
echo $listing->title;
echo $listing->translate('title', 'bn');

// Eager load translations (Prevents N+1 Query):
$listings = Listing::withTranslations()->paginate(20);
```

---

## 4. Type 4: `TranslationHybrid` (`hybrid`)
Grouped domain table sharing translations among multiple related models (`translator_engine_worlds`).

### Way A: Direct Engine Call
```php
// Long syntax:
$name = TranslatorEngine::type('hybrid', City::class)->get($cityId, 'name', 'bn');

// Fluent shorthand:
$name = TranslatorEngine::hybrid(City::class)->get($cityId, 'name', 'bn');

// Specifying entity_type directly with raw table name:
$name = TranslatorEngine::hybrid('translator_engine_worlds', 'city')->get($cityId, 'name', 'bn');
```

### Way B: Via Eloquent Model
```php
class City extends Model
{
    use HasTranslatorEngine;

    protected $translatorEngineType = 'hybrid';
    protected string $translatorEngineTable = 'translator_engine_worlds';
    protected string $entityType = 'city'; // Used by Country, State, City, SubCity
    protected array $translatable = ['name', 'native'];
}

$city = City::find(1);
echo $city->name;
echo $city->translate('name', 'bn');
```

---

## 5. Type 5: `TranslationStatic` (`static`)
Database-driven UI keys (`translator_statics`) + Redis/Memory caching.

### Database Record Structure:
* `key`: `button.add_to_cart`
* `default`: `Add to Cart`
* `value`: `{"en": "Add to Cart", "bn": "কার্টে যোগ করুন"}`

### Way A: Direct Engine Call
```php
// Query by structured key:
$text = TranslatorEngine::static()->get('button.add_to_cart', 'bn'); // Returns 'কার্টে যোগ করুন'

// Setting or updating a static key in database with default fallback:
TranslatorEngine::static()->set(
    key: 'button.add_to_cart',
    default: 'Add to Cart',
    values: [
        'en' => 'Add to Cart',
        'bn' => 'কার্টে যোগ করুন'
    ],
    group: 'button'
);
```

### Way B: In Blade Views (Calling via Key)
```blade
{{-- Calling by key from Database: --}}
<button>{{ translate('button.add_to_cart') }}</button>
{{-- If locale is 'bn' -> outputs: 'কার্টে যোগ করুন' --}}
{{-- If missing in 'bn' -> auto fallback to default: 'Add to Cart' --}}
```

---

## 6. Type 6: `TranslationJson` (`json`)
Flat JSON files on disk (`lang/en.json`, `lang/bn.json`).

### Export from Database to JSON using `default` as JSON key:
When exporting, the engine takes the `default` (`"Add to Cart"`) as the JSON key:

```php
// Command or Engine call to export:
TranslatorEngine::json()->exportFromDatabase('bn', resource_path('lang/bn.json'));
```

Generates `resources/lang/bn.json`:
```json
{
    "Add to Cart": "কার্টে যোগ করুন"
}
```

### In Blade Views (Calling via Natural Default Text):
```blade
{{-- Native Laravel helper reading directly from bn.json --}}
<button>{{ __('Add to Cart') }}</button>
{{-- If locale is 'bn' -> outputs: 'কার্টে যোগ করুন' --}}
{{-- If locale is 'en' or missing -> outputs the string itself: 'Add to Cart' --}}
```
```

---

## 7. Type 7: `TranslationFile` (`file`)
PHP array files in subfolders (`lang/en/auth.php`, `lang/bn/auth.php`).

### Way A: Direct Engine Call
```php
// Read string from specific group PHP file:
$val = TranslatorEngine::type('file')->get('auth.failed', 'bn');
$val = TranslatorEngine::file()->get('auth.failed', 'bn');

// Export Database keys to grouped PHP array files:
TranslatorEngine::file()->exportGroupToPhp('auth', 'bn');

// Import auth.php into Database:
TranslatorEngine::file()->importGroupFromPhp('auth', 'bn');
```

### Way B: In Blade / Laravel Native
```blade
{{-- Laravel native helper reading from auth.php --}}
<p>{{ __('auth.failed') }}</p>
```

---

## 8. Type 8: `TranslationLocal` (`local`)
Cultural & regional formatting: digits, South Asian grouping, dates, and number-to-words.

### Way A: Direct Engine Call
```php
// 1. Localized Digits:
$digits = TranslatorEngine::local()->digits('2026', 'bn'); // '২০২৬'

// 2. Localized Numbers (South Asian Lakh/Crore grouping):
$num = TranslatorEngine::local()->number(1234567, 0, 'bn'); // '১২,৩৪,৫৬৭'
$numEn = TranslatorEngine::local()->number(1234567, 2, 'en'); // '1,234,567.00'

// 3. Localized Dates:
$date = TranslatorEngine::local()->date(now(), false, 'bn'); // '০৯ সেপ্টেম্বর ২০২৬'
$dateTime = TranslatorEngine::local()->date(now(), true, 'bn'); // with time

// 4. Financial Number-To-Words (Cheques, Invoices):
$words = TranslatorEngine::local()->words(1500, 'BDT', 'bn'); // 'এক হাজার পাঁচশত টাকা মাত্র'
$wordsEn = TranslatorEngine::local()->words(1500, 'USD', 'en'); // 'One Thousand Five Hundred USD Only'
```

### Way B: In Blade Views / Helpers
```blade
{{ translate_digits('2026') }}
{{ translate_number(1250000) }}
{{ translate_date($order->created_at) }}
{{ translate_words($invoice->grand_total) }}
```

---

## 9. Type 9: `TranslationAI` (`ai`)
AI-powered automatic translation using Google Gemini, OpenAI, Claude, or DeepL.

### Way A: Direct Engine Call
```php
// 1. Translate a raw text string on demand:
$translated = TranslatorEngine::ai()->translate('Welcome to our application', 'en', 'bn');

// 2. Translate an associative array:
$translations = TranslatorEngine::ai()->translateBatch([
    'title' => 'Luxury Apartment',
    'location' => 'Dhaka, Bangladesh'
], 'en', 'bn');

// 3. Auto-translate missing fields of an Eloquent Model:
TranslatorEngine::ai()->translateModel($listing, ['title', 'description'], 'bn');

// 4. Auto-translate all untranslated keys in translator_statics table:
TranslatorEngine::ai()->translateMissingStatic('en', 'bn');
```

### Way B: Via Artisan CLI
```bash
# Translates all missing static UI keys from English to Bengali using Gemini:
php artisan translator:ai-sync --from=en --to=bn

# Translates specific group:
php artisan translator:ai-sync --from=en --to=bn --group=menu
```

---

## 10. Type 10: `TranslationAPI` (`api`)
Headless REST endpoints for MERN (React), Next.js, Vue, Nuxt, and Mobile Apps.

### Way A: Direct Engine Call (In Controller / Service)
```php
// Export all translations as clean API payload for frontend client:
$payload = TranslatorEngine::api()->getTranslationsPayload('bn', 'common');

// Return formatted JSON response with ETag & Cache headers:
return TranslatorEngine::api()->response('bn', 'auth');
```

### Way B: Direct HTTP Endpoints (MERN / React / Flutter Consumption)
```http
// 1. Get Static UI translations for a locale:
GET /api/v1/translator/static?locale=bn&group=auth
Response:
{
    "status": "success",
    "locale": "bn",
    "data": {
        "welcome": "স্বাগতম",
        "login": "লগইন করুন"
    }
}

// 2. Get active locales and cultural configuration:
GET /api/v1/translator/locales
Response:
{
    "locales": ["en", "bn"],
    "default": "en",
    "meta": {
        "bn": { "direction": "ltr", "currency": "BDT", "digits": ["০","১",...] }
    }
}

// 3. Batch translate dynamic strings:
POST /api/v1/translator/batch
Body:
{
    "locale": "bn",
    "keys": ["Add to Cart", "Checkout", "Order Details"]
}
```
