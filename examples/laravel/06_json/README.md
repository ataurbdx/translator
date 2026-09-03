# Type 6: TranslationJson (`json`)

Flat JSON files stored on disk in your Laravel project at `resources/lang/en.json`, `resources/lang/bn.json`, `resources/lang/es.json`.

---

## ⚡ Why Use Flat JSON Files?
- **Natural Text as Keys**: Instead of artificial keys like `messages.welcome`, you write natural sentences: `{{ __('Welcome back, :name!') }}`.
- **Native Laravel Compatibility**: Works seamlessly with Laravel's built-in `__()` helper.
- **Bi-directional DB Sync**: Translator allows you to export database translations from `translator_statics` directly into your `.json` disk files.

---

## 🛠️ Step-by-Step Implementation

### 1. File Location
Place files in `resources/lang/` or `lang/`:
- `resources/lang/en.json`
- `resources/lang/bn.json`
- `resources/lang/es.json`

### 2. Exporting from Database to JSON Files
```php
use Ataurbdx\Translator\Facades\Translator;

// Exports all database static keys for locale 'bn' into lang/bn.json
Translator::json()->exportFromDatabase('bn', resource_path('lang/bn.json'));
```

### 3. Usage & Outputting in Blade
```blade
{{-- 1. Simple text translation --}}
<button>{{ __('Add to Cart') }}</button>

{{-- 2. String with parameter replacements --}}
<p>{{ __('Welcome back, :name!', ['name' => $user->name]) }}</p>
```
