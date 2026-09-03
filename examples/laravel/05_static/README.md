# Type 5: TranslationStatic (`static`)

Stores UI buttons, headers, navbar labels, alerts, and system messages in a database table (**`translator_statics`**) with instant multi-tier memory/Redis caching.

---

## ⚡ Why Use Database-Driven Static Keys?
- **Admin-Editable**: Non-technical team members can change UI text in an Admin Panel without modifying code or touching git.
- **Multi-Tier Cache**: Fast memory and Redis caching ensures database overhead is 0ms on warm hits.
- **Zero File Syncing**: No need to manually deploy `.json` or `.php` language files to multiple servers.

---

## 🛠️ Step-by-Step Implementation

### 1. Install Static UI Table
```bash
php artisan translator:install --type=static
```
Creates `translator_statics` table.

### 2. Setting Keys in Code / Admin Controller
```php
use Ataurbdx\Translator\Facades\Translator;

Translator::static()->set(
    key: 'button.checkout',
    name: 'Proceed to Checkout',
    values: [
        'en' => 'Proceed to Checkout',
        'bn' => 'চেকআউট করুন',
        'es' => 'Pasar por caja',
    ],
    group: 'checkout'
);
```

### 3. Rendering in Blade Views (All Ways)
```blade
{{-- 1. Helper function (auto resolves active locale) --}}
<button>{{ translate('button.checkout') }}</button>

{{-- 2. Helper with custom default fallback --}}
<span>{{ translate('header.welcome', default: 'Welcome!') }}</span>

{{-- 3. Explicit language --}}
<span>{{ translate('button.checkout', 'bn') }}</span>

{{-- 4. Direct Facade call in controllers --}}
$text = Translator::static()->get('button.checkout', 'es');
```
