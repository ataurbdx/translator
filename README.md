# TranslatorEngine Monorepo (`ataurbdx/translator-engine`)

The **Master Universal Translation & Cultural Localization Engine** for Laravel, React/Next.js/MERN, and Flutter.

---

## 📁 Monorepo Architecture

This repository is organized strictly as a **True Monorepo**, separating each platform's package inside `packages/`:

```text
translation-package/
│
├── packages/
│   │
│   ├── laravel/             <-- 🐘 LARAVEL / PHP ENGINE & PACKAGE
│   │   ├── composer.json    (ataurbdx/translator-engine)
│   │   ├── config/          (translator_engine.php)
│   │   └── src/             (Core, Modules, Migrations, Drivers, Console)
│   │
│   ├── js/                  <-- ⚛️ REACT / NEXT.JS / MERN CLIENT
│   │   ├── package.json     (@ataurbdx/translator-engine)
│   │   └── src/             (TranslatorEngineClient, useTranslatorEngine hook)
│   │
│   └── flutter/             <-- 📱 FLUTTER / DART MOBILE CLIENT
│       ├── pubspec.yaml     (translator_engine_flutter)
│       └── lib/             (TranslatorEngine with SharedPreferences cache)
│
├── examples/                <-- Sample Models & Testing Walkthrough
└── about/                   <-- ARCHITECTURE.md & EXAMPLES.md
```

---

## 🚀 Quickstart for Each Platform

### 1. Laravel:
```bash
# In local development:
# Add "packages/laravel" to your Laravel composer.json repositories
composer require ataurbdx/translator-engine
php artisan translator-engine:install
```

### 2. React / Next.js / MERN:
```bash
npm install @ataurbdx/translator-engine
```

### 3. Flutter:
In `pubspec.yaml`:
```yaml
dependencies:
  translator_engine_flutter: ^1.0.0
```

---

## 📖 Complete Documentation
* [Architecture Blueprint](about/ARCHITECTURE.md)
* [API & Usage Examples for all 10 Types](about/EXAMPLES.md)
* [Local Testing Walkthrough](examples/TESTING_WALKTHROUGH.md)
