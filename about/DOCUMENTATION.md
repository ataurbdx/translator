# TranslatorEngine — Publishing, Tagging & Installation Guide

---

## Table of Contents

1. [How It Works](#how-it-works)
2. [Release a New Version (Git Tag)](#release-a-new-version-git-tag)
3. [Install in a Laravel Project](#install-in-a-laravel-project)
4. [All Available Artisan Commands](#all-available-artisan-commands)
5. [Upgrade the Package](#upgrade-the-package)
6. [Uninstall the Package](#uninstall-the-package)
7. [Quick Reference Cheatsheet](#quick-reference-cheatsheet)

---

## How It Works

```
Your Code  →  Git Push  →  GitHub  →  Packagist (auto-indexed via webhook)
                                              ↓
                                   composer require ataurbdx/translator-engine
```

- The package is hosted on GitHub: https://github.com/ataurbdx/translator-engine
- It is indexed on Packagist: https://packagist.org/packages/ataurbdx/translator-engine
- GitHub and Packagist are connected via a **webhook** — every `git push` auto-updates Packagist.
- Composer reads from Packagist by default, so users just run `composer require ataurbdx/translator-engine`.

---

## Release a New Version (Git Tag)

Every release **must have a Git tag**. Composer uses tags as version numbers.
Without a tag, Composer cannot find a stable version to install.

### Step 1 — Make your changes and commit

```bash
git add .
git commit -m "feat: your change description"
```

### Step 2 — Create a version tag

Follow [Semantic Versioning](https://semver.org/): `vMAJOR.MINOR.PATCH`

| Change Type | Example | When to use |
|-------------|---------|-------------|
| Bug fix | `v1.0.1` | Small fixes, nothing breaks |
| New feature | `v1.1.0` | New feature added, backward compatible |
| Breaking change | `v2.0.0` | Existing API changed, not backward compatible |

```bash
# Create the tag
git tag v1.0.1

# Or with a message (recommended)
git tag -a v1.0.1 -m "Fix: corrected digit translation fallback"
```

### Step 3 — Push the tag to GitHub

```bash
# Push the tag
git push origin v1.0.1

# Push all tags at once (if you have multiple)
git push origin --tags
```

### Step 4 — Packagist auto-updates (via webhook)

After `git push`, the GitHub webhook notifies Packagist automatically.
Wait 10–30 seconds, then the new version will be available on Packagist.

You can verify at: https://packagist.org/packages/ataurbdx/translator-engine

### Step 5 — Force manual update (if webhook fails)

Go to https://packagist.org/packages/ataurbdx/translator-engine and click the green **"Update"** button.

---

## Install in a Laravel Project

### Minimum requirement

- PHP `^8.1`
- Laravel `^9.0 | ^10.0 | ^11.0 | ^12.0`

### Step 1 — Require the package

```bash
composer require ataurbdx/translator-engine
```

That's it. Laravel will auto-discover the service provider.

### Step 2 — Run the installer

```bash
php artisan translator-engine:install
```

This single command will:
- ✅ Publish the config file to `config/translator_engine.php`
- ✅ Run all migrations (creates required database tables)
- ✅ Cache the config for performance

### Done! Start using it:

```php
// Translate text
translate('welcome_message', 'bn');

// Convert digits
translate('2025', 'bn', 'digit');

// Convert month name
translate('January', 'bn', 'month');

// Get flag emoji
translate('BD', 'bn', 'flag');
```

---

## Verify the Installation

### 1. Check the package is installed via Composer
```bash
composer show ataurbdx/translator-engine
```
✅ Shows version + description → installed  
❌ Error → not installed

### 2. Check the 5 tables exist in your database
```bash
php artisan migrate:status
```
You should see all 5 rows with status **Ran**:
```
translator_engine_languages  ✅ Ran
translator_engine_settings   ✅ Ran
translator_engine_dynamics   ✅ Ran
translator_engine_statics    ✅ Ran
translator_engine_locales    ✅ Ran
```

### 3. Test the helper in Tinker (live test)
```bash
php artisan tinker
```
```php
translate('hello', 'bn');
// Returns: 'hello' (key as fallback) → package is working ✅
// Returns: undefined function error → NOT installed ❌
```

### 4. Check config file was published
```bash
# Windows
dir config\translator_engine.php

# Linux / Mac
ls config/translator_engine.php
```
File should exist → published correctly ✅

---

## All Available Artisan Commands

### `translator-engine:install`

The all-in-one setup command. Run once after installing the package.

```bash
php artisan translator-engine:install
```

What it does:
- Publishes `config/translator_engine.php`
- Publishes all migrations to `database/migrations/`
- Runs `php artisan migrate` which creates all 5 core tables:
  - `translator_engine_languages` — available languages (en, bn, ar, ...)
  - `translator_engine_settings`  — AI keys, API config, cache settings
  - `translator_engine_dynamics`  — internal/hybrid polymorphic translations
  - `translator_engine_statics`   — static UI strings (buttons, labels, menus)
  - `translator_engine_locales`   — cultural rules (digits, months, calendar)
- Asks for confirmation before running migrations (can skip with `--all` flag)

---

### `vendor:publish` — Publish only the config

If you want to re-publish the config file only:

```bash
php artisan vendor:publish --provider="Ataurbdx\TranslatorEngine\TranslatorEngineServiceProvider" --tag="config"
```

---

### `vendor:publish` — Publish only the migrations

If you want to copy the migrations into your app's `database/migrations/` folder:

```bash
php artisan vendor:publish --provider="Ataurbdx\TranslatorEngine\TranslatorEngineServiceProvider" --tag="migrations"
```

---

### `migrate` — Run the migrations manually

If you prefer running migrations manually instead of using the installer:

```bash
php artisan migrate --path=vendor/ataurbdx/translator-engine/packages/laravel/src/Modules/StaticUI/Migrations

php artisan migrate --path=vendor/ataurbdx/translator-engine/packages/laravel/src/Modules/CulturalLocale/Migrations
```

---

### `config:cache` — Cache config after changes

After editing `config/translator_engine.php`, refresh the config cache:

```bash
php artisan config:cache
```

To clear the cache without rebuilding:

```bash
php artisan config:clear
```

---

### `migrate:rollback` — Undo the migrations

If you need to roll back the package's migrations:

```bash
php artisan migrate:rollback --path=vendor/ataurbdx/translator-engine/packages/laravel/src/Modules/StaticUI/Migrations

php artisan migrate:rollback --path=vendor/ataurbdx/translator-engine/packages/laravel/src/Modules/CulturalLocale/Migrations
```

---

### `translator-engine:export-locales` — Export cultural locale rules to JSON files

Exports the `local` type cultural rules from the database to fallback JSON files.
The driver uses these files when the database is unavailable (3-tier fallback: Cache → DB → File → Hardcoded).

```bash
# Export ALL locales from DB to resources/lang/locales/
php artisan translator-engine:export-locales

# Export a specific locale only
php artisan translator-engine:export-locales --locale=bn

# Overwrite existing exported files
php artisan translator-engine:export-locales --force
```

Output files are created at:
```
resources/
└── lang/
    └── locales/
        ├── bn.json   ← Bangla cultural rules (digits, months, calendar...)
        ├── ar.json   ← Arabic cultural rules
        └── hi.json   ← Hindi cultural rules
```

> **When to run:** After seeding `translator_engine_locales`, run this command to create
> fallback files. This ensures the `local` type works even when the database is down.

---

### `translator-engine:make:external` — Generate a dedicated translation table for a model

```bash
php artisan translator-engine:make:external listings
# Creates: database/migrations/..._create_translator_engine_listings_table.php
```

### `translator-engine:make:hybrid` — Generate a shared domain translation table

```bash
php artisan translator-engine:make:hybrid worlds
# Creates: database/migrations/..._create_translator_engine_worlds_table.php
# Shared by: Country, Division, City, SubCity — all in one table
```

### `translator-engine:ai-sync` — Auto-translate missing keys via AI

```bash
php artisan translator-engine:ai-sync --from=en --to=bn --group=menu
```

---

## Upgrade the Package

When a new version is released:

```bash
composer update ataurbdx/translator-engine
```

If the new version has new migrations, run them:

```bash
php artisan migrate
```

---

## Uninstall the Package

### Step 1 — Roll back migrations (optional but recommended)

```bash
php artisan migrate:rollback --path=vendor/ataurbdx/translator-engine/packages/laravel/src/Modules/StaticUI/Migrations

php artisan migrate:rollback --path=vendor/ataurbdx/translator-engine/packages/laravel/src/Modules/CulturalLocale/Migrations
```

### Step 2 — Remove the package

```bash
composer remove ataurbdx/translator-engine
```

### Step 3 — Remove published files (optional)

```bash
# Delete published config
del config\translator_engine.php
```

---

## Quick Reference Cheatsheet

```bash
# ──────────────────────────────────────────────
# PUBLISHING A NEW VERSION
# ──────────────────────────────────────────────

git add .
git commit -m "your commit message"
git tag v1.0.1
git push origin v1.0.1

# ──────────────────────────────────────────────
# INSTALLING IN A LARAVEL PROJECT
# ──────────────────────────────────────────────

composer require ataurbdx/translator-engine
php artisan translator-engine:install

# ──────────────────────────────────────────────
# UPGRADING
# ──────────────────────────────────────────────

composer update ataurbdx/translator-engine
php artisan migrate

# ──────────────────────────────────────────────
# UNINSTALLING
# ──────────────────────────────────────────────

composer remove ataurbdx/translator-engine

# ──────────────────────────────────────────────
# FORCE PACKAGIST TO REFRESH (if webhook fails)
# ──────────────────────────────────────────────

# Go to: https://packagist.org/packages/ataurbdx/translator-engine
# Click the green "Update" button

# ──────────────────────────────────────────────
# CLEAR & REBUILD CONFIG CACHE
# ──────────────────────────────────────────────

php artisan config:clear
php artisan config:cache
```

---

## Version History

| Version | Tag | Description |
|---------|-----|-------------|
| 1.0.0 | `v1.0.0` | Initial stable release |

> Add new rows here every time you release a new version.

---

## Links

- **GitHub**: https://github.com/ataurbdx/translator-engine
- **Packagist**: https://packagist.org/packages/ataurbdx/translator-engine
- **Semantic Versioning**: https://semver.org
