# How to Link & Test `Translator` in Any Laravel Project (Monorepo)

Since this repository is organized as a true **Monorepo**, the Laravel package lives cleanly inside `packages/laravel/`.

---

### Step 1: Add Local Path Repository to your Laravel `composer.json`

Open the `composer.json` of your Laravel project, and add this under `"repositories"`:

```json
"repositories": [
    {
        "type": "path",
        "url": "D:/Installation/laragon/www/package-translator/packages/laravel"
    }
]
```

---

### Step 2: Require the Package Locally

In your terminal inside your Laravel project, run:

```bash
composer require ataurbdx/translator:*
```

Composer will immediately create a symlink to `packages/laravel/`. Any edits you make will reflect in real-time inside your Laravel project!

---

### Step 3: Run the Translator Installer

Inside your Laravel project:

```bash
php artisan translator:install
```

This will:
1. Publish `config/translator.php`.
2. Publish and run the core migrations (`translator_languages`, `translator_settings`, `translator_statics`, `translator_dynamics`, `translator_locales`).

---

### Step 4: For React / Next.js / MERN:
Inside your JavaScript or Next.js app:
```bash
npm install "file:../../package-translator/packages/js"
```

### Step 5: For Flutter:
Inside your Flutter app's `pubspec.yaml`:
```yaml
dependencies:
  translator_flutter:
    path: ../../package-translator/packages/flutter
```
