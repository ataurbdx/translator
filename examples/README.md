# Complete Examples for All 3 Platforms

The `examples/` directory is partitioned into 3 separate folders for each platform:

```text
examples/
│
├── laravel/
│   ├── ExampleModels.php                 # Working Eloquent Models for all 4 types (Inline, Internal, External, Hybrid)
│   └── LaravelExampleUsageController.php # Controller demonstrating te(), te_digits(), te_number(), te_words()
│
├── js/
│   └── ReactExample.tsx                  # React / Next.js / MERN Component with useTranslatorEngine hook
│
└── flutter/
    └── flutter_example.dart              # Complete Flutter app with SharedPreferences cache & Bengali numerals
```

---

## 🐘 1. Laravel Examples (`examples/laravel/`)

### A. Eloquent Models:
```php
use Ataurbdx\TranslatorEngine\Core\Traits\HasTranslatorEngine;

class ExampleCategory extends Model
{
    use HasTranslatorEngine;

    protected $translatorEngineType = 'internal'; // 'inline', 'internal', 'external', 'hybrid'
    protected array $translatable = ['name', 'description'];
}
```

### B. Usage in Controller / Blade:
```php
// Eloquent auto-translated:
$name = $category->name; 

// Static UI translations:
$btn = te('button.add_to_cart');

// Cultural formatters:
$digits = te_digits('2026', 'bn');     // ২০২৬
$number = te_number(1250000, 0, 'bn'); // ১২,৫০,০০০
$words  = te_words(1500, 'BDT', 'bn'); // এক হাজার পাঁচশত টাকা মাত্র
```

---

## ⚛️ 2. React / Next.js / MERN Examples (`examples/js/`)

```tsx
import { TranslatorEngineClient } from '../../packages/js/src/index';
import { TranslatorEngineProvider, useTranslatorEngine } from '../../packages/js/src/react';

const client = new TranslatorEngineClient({
    baseUrl: 'https://api.yourdomain.com',
    locale: 'bn',
});

function ProductCard() {
    const { t, formatDigits, formatNumber } = useTranslatorEngine();

    return (
        <div>
            <h2>{t('product.title', 'Default Title')}</h2>
            <p>Year: {formatDigits(2026)}</p>         {/* ২০২৬ */}
            <p>Price: ৳{formatNumber(1250000)}</p>   {/* ১২,৫০,০০০ */}
            <button>{t('button.save')}</button>
        </div>
    );
}
```

---

## 📱 3. Flutter Examples (`examples/flutter/`)

```dart
import 'package:flutter/material.dart';
import '../../packages/flutter/lib/translator_engine.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Initialize client pointing to Laravel backend
  final engine = TranslatorEngine.init(
    baseUrl: 'https://api.yourdomain.com',
    defaultLocale: 'bn',
  );
  await engine.load();

  runApp(const MyApp());
}

// In any Widget:
Text(TranslatorEngine.instance.translate('button.add_to_cart'))
Text(TranslatorEngine.instance.formatDigits(2026))       // ২০২৬
Text(TranslatorEngine.instance.formatNumber(1250000))    // ১২,৫০,০০০
```
