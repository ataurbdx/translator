# Type 7: TranslationFile (`file`)

Grouped PHP array files stored in locale subdirectories: `resources/lang/en/messages.php`, `resources/lang/bn/messages.php`, `resources/lang/es/messages.php`.

---

## ⚡ When to Use PHP Array Files?
- **Core Laravel Packages**: Many third-party packages (Breeze, Jetstream, Fortify, Nova) publish language files in PHP array format.
- **Large Structured Domains**: Breaking translations into files like `auth.php`, `validation.php`, `passwords.php`, `pagination.php`.
- **Database Importer**: Translator includes built-in tools to import your legacy PHP files into database `translator_statics`.

---

## 🛠️ Step-by-Step Implementation

### 1. File Structure
```text
resources/lang/
├── en/
│   └── messages.php
├── bn/
│   └── messages.php
└── es/
    └── messages.php
```

### 2. Reading via Direct Engine Call
```php
use Ataurbdx\Translator\Facades\Translator;

$text = Translator::file()->get('messages.auth.failed', 'bn');
```

### 3. Usage & Printing in Blade
```blade
<p>{{ __('messages.auth.failed') }}</p>
<p>{{ trans('messages.auth.throttle', ['seconds' => 60]) }}</p>
```
