# Translator Monorepo (`ataurbdx/translator`)

The **Master Universal Translation & Cultural Localization Engine** for Laravel, React/Next.js/MERN, and Flutter.

---

## 📁 Monorepo Architecture

This repository is organized strictly as a **True Monorepo**, separating each platform's package inside `packages/`:

```text
package-translator/
│
├── packages/
│   │
│   ├── laravel/             <-- 🐘 LARAVEL / PHP ENGINE & PACKAGE
│   │   ├── composer.json    (ataurbdx/translator)
│   │   ├── config/          (translator.php)
│   │   └── src/             (Core, Modules, Migrations, Drivers, Console)
│   │
│   ├── js/                  <-- ⚛️ REACT / NEXT.JS / MERN CLIENT
│   │   ├── package.json     (@ataurbdx/translator)
│   │   └── src/             (TranslatorClient, useTranslator hook)
│   │
│   └── flutter/             <-- 📱 FLUTTER / DART MOBILE CLIENT
│       ├── pubspec.yaml     (translator_flutter)
│       └── lib/             (Translator with SharedPreferences cache)
│
├── examples/                <-- Sample Models & Testing Walkthrough
└── about/                   <-- COMMANDS.txt, ARCHITECTURE.md, DOCUMENTATION.md & EXAMPLES.md
```

---

## 🚀 Quickstart for Each Platform

### 1. Laravel:
```bash
composer require ataurbdx/translator
php artisan translator:install
```

### 2. React / Next.js / MERN:
```bash
npm install @ataurbdx/translator
```

### 3. Flutter:
In `pubspec.yaml`:
```yaml
dependencies:
  translator_flutter: ^1.0.0
```

---

## 📖 Complete Documentation
* [Core Commands](about/COMMANDS.txt)
* [Architecture Blueprint](about/ARCHITECTURE.md)
* [Publishing & Installation Guide](about/DOCUMENTATION.md)
* [API & Usage Examples for all 10 Types](about/EXAMPLES.md)
* [Local Testing Walkthrough](examples/TESTING_WALKTHROUGH.md)
