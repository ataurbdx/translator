# How to Link & Test `TranslatorEngine` in Any Laravel Project (Monorepo)

Since this repository is organized as a true **Monorepo**, the Laravel package lives cleanly inside `packages/laravel/`.

---

### Step 1: Add Local Path Repository to your Laravel `composer.json`

Open the `composer.json` of your Laravel project (e.g. in `asset-sheba`), and add this under `"repositories"`:

```json
"repositories": [
    {
        "type": "path",
        "url": "D:/Installation/laragon/www/translation-package/packages/laravel"
    }
]
```

---

### Step 2: Require the Package Locally

In your terminal inside your Laravel project, run:

```bash
composer require ataurbdx/translator-engine:*
```

Composer will immediately create a symlink to `packages/laravel/`. Any edits you make will reflect in real-time inside your Laravel project!

---

### Step 3: Run the TranslatorEngine Installer

Inside your Laravel project:

```bash
php artisan translator-engine:install
```

This will:
1. Publish `config/translator_engine.php`.
2. Publish and run the core migrations (`translator_engine_settings`, `translator_engine_statics`, `translator_engine_dynamics`, `translator_engine_locales`).

---

### Step 4: For React / Next.js / MERN:
Inside your JavaScript or Next.js app:
```bash
npm install "file:../../translation-package/packages/js"
```

### Step 5: For Flutter:
Inside your Flutter app's `pubspec.yaml`:
```yaml
dependencies:
  translator_engine_flutter:
    path: ../../translation-package/packages/flutter
```
