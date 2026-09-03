# 00. Core: Dynamic Languages, Language Switcher & Zero-Blank Fallback

This module demonstrates the universal foundation of **`Translator`**:
1. **Dynamic Languages Table (`translator_languages`)**: Not limited to Bangla/English/Arabic — add Spanish, French, Hindi, German, Japanese or any language at runtime via DB/admin.
2. **Language Switcher Dropdown**: Auto-populates all active languages with flags, native names, and active states.
3. **Session & Cookie Middleware**: Persists the user's chosen language across requests.
4. **Universal Zero-Blank Fallback Chain**: Requested Locale &rarr; Default Locale &rarr; Original DB Attribute &rarr; Default Text. Never returns blank!

---

## 1. Database Setup

Ensure Core tables are installed:
```bash
php artisan translator:install --type=core
```

Seed initial global languages:
```bash
php artisan db:seed --class="App\Seeders\LanguageSeeder"
```

---

## 2. Dynamic Language Switcher Setup

### Step A: Register Route in `routes/web.php`
```php
use App\Http\Controllers\LanguageSwitcherController;

Route::get('/language/{locale}', [LanguageSwitcherController::class, 'switch'])->name('language.switch');
```

### Step B: Register Middleware in `app/Http/Kernel.php` (or `bootstrap/app.php` in Laravel 11+)
In the `web` middleware group:
```php
\App\Http\Middleware\SetLocaleMiddleware::class,
```

### Step C: Include Dropdown in your Blade Navbar / Header
```blade
@include('components.language_switcher_dropdown')
```

---

## 3. Dynamically Adding Any Language Anywhere

You can add any new language in your admin controller:
```php
use Ataurbdx\Translator\Modules\Languages\Models\TranslatorLanguage;

TranslatorLanguage::create([
    'code'            => 'es',
    'name'            => 'Spanish',
    'native'          => 'Español',
    'direction'       => 'ltr',
    'currency'        => 'EUR',
    'currency_symbol' => '€',
    'flag'            => '🇪🇸',
    'is_default'      => false,
    'status'          => true,
]);
```
Immediately, the Language Switcher dropdown, API endpoints, and all 10 translation drivers will recognize Spanish!
