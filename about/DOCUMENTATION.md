# Translator — Publishing, Packagist Webhook & Tagging Guide

A complete, step-by-step handbook on how to publish the `ataurbdx/translator` package to Packagist, configure GitHub Webhooks with your API Token for automatic instant syncing, release new version tags, and install it in any Laravel application.

---

## Table of Contents

1. [How Package Distribution Works](#1-how-package-distribution-works)
2. [Phase 1 — Initial Setup: Packagist & GitHub Webhook](#2-phase-1--initial-setup-packagist--github-webhook-one-time)
   - [Step 1: Create a Packagist Account](#step-1-create-a-packagist-account)
   - [Step 2: Obtain your Packagist API Token](#step-2-obtain-your-packagist-api-token)
   - [Step 3: Submit the Repository to Packagist](#step-3-submit-the-repository-to-packagist)
   - [Step 4: Configure GitHub Webhook for Auto-Sync](#step-4-configure-github-webhook-for-auto-sync)
   - [Step 5: Verify Webhook Connection](#step-5-verify-webhook-connection)
3. [Phase 2 — Releasing a New Version (Git Tagging)](#3-phase-2--releasing-a-new-version-git-tagging)
   - [Semantic Versioning Rules](#semantic-versioning-rules)
   - [Tagging & Pushing Workflow](#tagging--pushing-workflow)
4. [Phase 3 — Installing & Using in a Laravel Project](#4-phase-3--installing--using-in-a-laravel-project)
5. [Upgrading the Package](#5-upgrading-the-package)
6. [Uninstalling the Package](#6-uninstalling-the-package)
7. [Quick Reference Cheatsheet](#7-quick-reference-cheatsheet)

---

## 1. How Package Distribution Works

```
Your Local Code  ──(git push)──>  GitHub Repository  ──(Webhook Ping)──>  Packagist.org
                                                                                │
                                                                           (auto-indexed)
                                                                                │
                                                                                ▼
User Terminal:  composer require ataurbdx/translator  <─────────────────────────┘
```

* **GitHub Repository**: Stores your source code, branches, and release tags (`https://github.com/ataurbdx/translator.git`).
* **Packagist.org**: The official PHP package repository where Composer looks up packages.
* **GitHub Webhook**: A webhook trigger that alerts Packagist the instant you push a new commit or version tag, automatically indexing the release in seconds without manual intervention.

---

## 2. Phase 1 — Initial Setup: Packagist & GitHub Webhook (One-Time)

Follow these 5 steps to register and link your package with GitHub and Packagist.

### Step 1: Create a Packagist Account
1. Visit [https://packagist.org](https://packagist.org/).
2. Click **Log in** or **Register** (we recommend clicking **"Log in with GitHub"** for instant authentication).
3. Note your **Packagist Username** (e.g., `ataurbdx`).

---

### Step 2: Obtain your Packagist API Token
Packagist uses an API Token as the secret key in your GitHub webhook to verify that incoming pings come from you.

1. In Packagist, click on your username at the top-right corner to open your **Profile**.
2. Click **"Show API Token"** (or find the **API Token** section).
3. Copy the token string (e.g. `a1b2c3d4e5f6...`). Keep this handy for Step 4.

---

### Step 3: Submit the Repository to Packagist
1. In the top navigation bar of Packagist, click **Submit** (or visit [https://packagist.org/packages/submit](https://packagist.org/packages/submit)).
2. In the **Repository URL** field, enter:
   ```text
   https://github.com/ataurbdx/translator.git
   ```
3. Click **Check**. Packagist will inspect your `composer.json` file.
4. Once verified, click **Submit**.
5. Your package will now be live at:
   ```text
   https://packagist.org/packages/ataurbdx/translator
   ```
   *(Note: You may see a notice warning that auto-update is not configured yet. That is what Step 4 resolves!)*

---

### Step 4: Configure GitHub Webhook for Auto-Sync
This connects your GitHub repository directly to Packagist so every `git push` or tag release updates Packagist immediately.

1. Open your GitHub repository in your browser:
   [https://github.com/ataurbdx/translator](https://github.com/ataurbdx/translator)
2. Go to **Settings** (tab at the top of the repo).
3. In the left sidebar, click **Webhooks**.
4. Click the **Add webhook** button on the top-right.
5. Fill in the webhook form as follows:

| Field | Value / Setting |
|---|---|
| **Payload URL** | `https://packagist.org/api/github?username=YOUR_PACKAGIST_USERNAME`<br>*(Example: `https://packagist.org/api/github?username=ataurbdx`)* |
| **Content type** | Select `application/json` |
| **Secret** | Paste your **Packagist API Token** (copied in Step 2) |
| **SSL verification** | Select **Enable SSL verification** |
| **Which events to trigger** | Select **"Just the push event"** |
| **Active** | Ensure the **Active** checkbox is **Checked** ✅ |

6. Click the green **Add webhook** button.

---

### Step 5: Verify Webhook Connection
1. After adding the webhook, GitHub will immediately send a test ping to Packagist.
2. Refresh the **Webhooks** list on GitHub.
3. You should see a green checkmark icon (`✔`) next to `https://packagist.org/api/github?username=...`.
4. If you click on the webhook and inspect **"Recent Deliveries"**, you will see a `200 OK` response from Packagist.
5. Visit your package page on Packagist ([https://packagist.org/packages/ataurbdx/translator](https://packagist.org/packages/ataurbdx/translator)) and notice the warning notice is gone. Auto-sync is now active!

---

## 3. Phase 2 — Releasing a New Version (Git Tagging)

Whenever you make improvements, bug fixes, or add features, release a new version using Git tags. Composer uses Git tags as package versions.

### Semantic Versioning Rules
Follow the [Semantic Versioning (SemVer)](https://semver.org/) format: `vMAJOR.MINOR.PATCH`

| Release Type | Tag Example | When to use |
|---|---|---|
| **Patch / Fix** | `v1.0.1` | Bug fixes, typos, internal optimizations (nothing breaks) |
| **Minor / Feature** | `v1.1.0` | New translation driver, new command, backward-compatible |
| **Major / Breaking** | `v2.0.0` | Major overhaul, breaking schema changes |

---

### Tagging & Pushing Workflow

#### Step 1: Stage and commit your changes
```bash
git add .
git commit -m "feat: simplified translator engine to translator"
```

#### Step 2: Push your branch to GitHub
```bash
git push origin main
```

#### Step 3: Create a version tag
```bash
# Lightweight tag:
git tag v1.0.0

# Or annotated tag with a descriptive release message (recommended):
git tag -a v1.0.0 -m "Release v1.0.0: Master universal translator for Laravel, Flutter, and React"
```

#### Step 4: Push the tag to GitHub
```bash
# Push a specific tag:
git push origin v1.0.0

# Or push all local tags at once:
git push origin --tags
```

#### Step 5: Automatic indexing
* Because your GitHub Webhook is active, Packagist receives the tag event immediately.
* Within **10–30 seconds**, `v1.0.0` is published and available worldwide on Packagist!
* You can check the live release at [https://packagist.org/packages/ataurbdx/translator](https://packagist.org/packages/ataurbdx/translator).

> **Manual Update Fallback**: If GitHub's webhook ever experiences delays, you can always visit your Packagist page and click the green **"Update"** button to force an instant refresh.

---

## 4. Phase 3 — Installing & Using in a Laravel Project

Once published on Packagist, any Laravel developer in the world can install and use your package with standard Composer commands:

### Step 1: Require via Composer
```bash
composer require ataurbdx/translator
```
Laravel's package auto-discovery will automatically register `Ataurbdx\Translator\TranslationServiceProvider` and the `Translator` facade.

### Step 2: Run the Installer
```bash
php artisan translator:install
```
This single command:
1. Publishes configuration to `config/translator.php`.
2. Publishes and executes all core migrations:
   - `translator_languages` (active languages, flags, defaults)
   - `translator_settings` (AI keys, API settings, cache rules)
   - `translator_dynamics` (polymorphic database translations)
   - `translator_statics` (database UI labels, buttons, menus)
   - `translator_locales` (cultural formatting rules: digits, calendar, money-to-words)

### Step 3: Start Translating in Code

```php
// In Blade templates or PHP controllers:
translate('button.add_to_cart', 'bn');

// Convert digits to Bengali (2026 -> ২০২৬):
translate('2026', type: 'digits', locale: 'bn');

// Format numbers with South Asian grouping (1250000 -> ১২,৫০,০০০):
translate(1250000, type: 'number', locale: 'bn');

// Financial number-to-words:
translate(1500, type: 'words', currency: 'BDT', locale: 'bn');
// Output: এক হাজার পাঁচশত টাকা মাত্র

// In Eloquent models:
use Ataurbdx\Translator\Core\Traits\HasTranslator;

class Category extends Model
{
    use HasTranslator;

    protected $translatorType = 'internal';
    protected array $translatable = ['name', 'description'];
}

$category = Category::find(1);
echo $category->name; // Auto-resolves based on active app locale!
```

---

## 5. Upgrading the Package

When you release a new version tag (e.g. `v1.0.1`), users upgrade by running:

```bash
composer update ataurbdx/translator
php artisan migrate
```

---

## 6. Uninstalling the Package

If a user ever wants to remove the package:

```bash
# 1. Rollback migrations (optional)
php artisan migrate:rollback --path=vendor/ataurbdx/translator/packages/laravel/src/Modules/StaticUI/Migrations
php artisan migrate:rollback --path=vendor/ataurbdx/translator/packages/laravel/src/Modules/CulturalLocale/Migrations

# 2. Remove package
composer remove ataurbdx/translator

# 3. Delete published config
del config\translator.php
```

---

## 7. Quick Reference Cheatsheet

```bash
# ──────────────────────────────────────────────
# 1. COMMIT CHANGES
# ──────────────────────────────────────────────
git add .
git commit -m "feat: your descriptive update"

# ──────────────────────────────────────────────
# 2. PUSH CODE TO GITHUB
# ──────────────────────────────────────────────
git push origin main

# ──────────────────────────────────────────────
# 3. TAG A NEW RELEASE
# ──────────────────────────────────────────────
git tag v1.0.0
git push origin v1.0.0
# (Packagist auto-updates in 15 seconds via webhook!)

# ──────────────────────────────────────────────
# 4. INSTALL IN ANY LARAVEL PROJECT
# ──────────────────────────────────────────────
composer require ataurbdx/translator
php artisan translator:install

# ──────────────────────────────────────────────
# 5. CLEAR & REBUILD CONFIG CACHE
# ──────────────────────────────────────────────
php artisan config:clear
php artisan config:cache
```

---

## Links

- **GitHub Repository**: [https://github.com/ataurbdx/translator](https://github.com/ataurbdx/translator)
- **Packagist Package**: [https://packagist.org/packages/ataurbdx/translator](https://packagist.org/packages/ataurbdx/translator)
- **Packagist Submit**: [https://packagist.org/packages/submit](https://packagist.org/packages/submit)
- **Semantic Versioning Specification**: [https://semver.org](https://semver.org)
