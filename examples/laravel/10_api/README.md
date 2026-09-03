# Type 10: TranslationAPI (`api`)

Headless REST API endpoints for modern frontend frameworks (React, Next.js, Vue, Nuxt) and mobile apps (Flutter, React Native).

---

## ⚡ Endpoints Overview

| Method | Endpoint | Purpose |
|---|---|---|
| `GET` | `/api/v1/translator/locales` | Returns all active languages, direction, symbols, and flags. |
| `GET` | `/api/v1/translator/static?locale=bn&group=auth` | Fetches UI translation dictionary with HTTP ETag caching. |
| `POST` | `/api/v1/translator/batch` | Batch translates an array of dynamic keys. |

---

## 📱 Consuming in Frontend Clients

### 1. React / Next.js Client
```tsx
import { TranslatorClient } from '@ataurbdx/translator';

const client = new TranslatorClient({
    baseUrl: 'https://api.yourdomain.com',
    locale: 'bn',
});

// React Hook
const { t, formatDigits, formatNumber } = useTranslator();
return <button>{t('button.save')}</button>;
```

### 2. Flutter Mobile Client
```dart
import 'package:translator_flutter/translator.dart';

final translator = Translator.init(
    baseUrl: 'https://api.yourdomain.com',
    defaultLocale: 'bn',
);
await translator.load();

// In any Widget:
Text(Translator.instance.translate('button.save'));
Text(Translator.instance.formatDigits(2026)); // ২০২৬
```
