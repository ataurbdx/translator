# Complete Examples for All 3 Platforms

The `examples/` directory is partitioned into 3 separate folders for each platform:

```text
examples/
│
├── laravel/
│   ├── ExampleModels.php                 # Working Eloquent Models for all 4 types (Inline, Internal, External, Hybrid)
│   └── LaravelExampleUsageController.php # Controller demonstrating translate() and translator()
│
├── js/
│   └── ReactExample.tsx                  # React / Next.js / MERN Component with useTranslator hook
│
└── flutter/
    └── flutter_example.dart              # Complete Flutter app with SharedPreferences cache & Bengali numerals
```

---

## 🐘 1. Laravel Examples (`examples/laravel/`)

### A. Eloquent Models:
```php
use Ataurbdx\Translator\Core\Traits\HasTranslator;

class ExampleCategory extends Model
{
    use HasTranslator;

    protected $translatorType = 'internal'; // 'inline', 'internal', 'external', 'hybrid'
    protected array $translatable = ['name', 'description'];
}
```

### B. Usage in Controller / Blade:
```php
// Eloquent auto-translated:
$name = $category->name; 

// Static UI translations:
$btn = translate('button.add_to_cart');

// Cultural formatters:
$digits = translate('2026', type: 'digits', locale: 'bn');     // ২০২৬
$number = translate(1250000, type: 'number', locale: 'bn');    // ১২,৫০,০০০
$words  = translate(1500, type: 'words', currency: 'BDT', locale: 'bn'); // এক হাজার পাঁচশত টাকা মাত্র
```

---

## ⚛️ 2. React / Next.js / MERN Examples (`examples/js/`)

```tsx
import { TranslatorClient } from '../../packages/js/src/index';
import { TranslatorProvider, useTranslator } from '../../packages/js/src/react';

const client = new TranslatorClient({
    baseUrl: 'https://api.yourdomain.com',
    locale: 'bn',
});

function ProductCard() {
    const { t, formatDigits, formatNumber } = useTranslator();

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
import '../../packages/flutter/lib/translator.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Initialize client pointing to Laravel backend
  final translator = Translator.init(
    baseUrl: 'https://api.yourdomain.com',
    defaultLocale: 'bn',
  );
  await translator.load();

  runApp(const MyApp());
}

// In any Widget:
Text(Translator.instance.translate('button.add_to_cart'))
Text(Translator.instance.formatDigits(2026))       // ২০২৬
Text(Translator.instance.formatNumber(1250000))    // ১২,৫০,০০০
```
