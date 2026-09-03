# Master Universal Translator (`ataurbdx/translator`)
## Architecture, Strategy & Implementation Blueprint

---

### Executive Vision
The **Master Universal Translator** is a high-performance, universal translation and localization ecosystem for Laravel and multi-platform applications (MERN, Next.js, Vue, mobile apps). 

Instead of forcing developers into a single rigid translation pattern, this engine natively supports **10 Comprehensive Translation Types**, covering every possible dynamic database model scenario, static UI strings, cultural formatting, AI automation, traditional file formats, and cross-platform headless delivery.

---

## The 10 Master Translation Types (Quick Index)

The engine establishes **10 distinct, beautifully categorized translation types**:

| Category | # | Type Name | Key Identifier | Description & Primary Use Case | Storage Mechanism |
|---|---|---|---|---|---|
| **Dynamic Models** | **1** | `TranslationInline` | `inline` | **In-Table JSON Column**: Translations live inside the model's own table column (e.g. `name` = `{"en":"Apple","bn":"আপেল"}`). Zero extra tables, zero joins. | Model's own table column (`json` data type) |
| **Dynamic Models** | **2** | `TranslationInternal` | `internal` | **Shared Polymorphic Table**: Single shared table for multiple small-to-medium models (`Category`, `Department`, `FaqCategory`). Keeps database migrations clean. | Single common table (`translator_dynamics`) via `morphTo` |
| **Dynamic Models** | **3** | `TranslationExternal` | `external` | **Dedicated Table Per Model**: Dedicated translation table for high-traffic, high-volume models (`Listing`, `Product`, `Post`). Maximum performance and composite indexing. | Individual table per model (`translator_listings`) |
| **Dynamic Models** | **4** | `TranslationHybrid` | `hybrid` | **Grouped Domain Tables with Morph/Type**: A dedicated domain table shared across a related family of models (e.g. World cluster: `Country`, `Division`, `City`, `SubCity` sharing `translator_worlds`). | Dedicated domain table partitioned by `entity_type` + `entity_id` |
| **Static UI Strings** | **5** | `TranslationStatic` | `static` | **Database-Driven UI Keys**: UI buttons, labels, menus, alerts. Stored in DB, managed via Admin UI, cached in Redis/Memory. Called via `{{ translate('welcome') }}`. Zero file export required. | Database table (`translator_statics`) + Redis/Memory Multi-Tier Cache |
| **Static UI Strings** | **6** | `TranslationJson` | `json` | **Flat JSON File-Based**: Native Laravel JSON files (`lang/en.json`, `lang/bn.json`). Hand-written or exported from `TranslationStatic`. Called via `__('welcome')`. | Flat JSON files on disk (`lang/*.json`) |
| **Static UI Strings** | **7** | `TranslationFile` | `file` | **PHP Array Translation Files**: Traditional Laravel grouped language files (`lang/en/auth.php`, `lang/en/validation.php`) returning PHP associative arrays. Complete legacy & core package compatibility. | PHP array files (`lang/{locale}/*.php`) |
| **Cultural Formatting** | **8** | `TranslationLocal` | `local` | **Cultural & Regional Formatter**: Localized digits (`১২৩`), calendar dates & months, South Asian grouping (`১২,৩৪,৫৬৭`), and invoice number-to-words (`এক হাজার পাঁচশত টাকা মাত্র`). | Database-driven table (`translator_locales`) cached in Redis/Memory + fallback file |
| **Automation & AI** | **9** | `TranslationAI` | `ai` | **AI On-Demand / Background Sync**: Automatic translation of missing static or dynamic text using Google Gemini, OpenAI, Claude, or DeepL. | Queue jobs / AI Service Drivers writing into `translator_statics` / `translator_dynamics` |
| **Cross-Platform** | **10** | `TranslationAPI` | `api` | **Headless / REST / Cross-Platform**: Real-time REST endpoints for React, Next.js, Vue, mobile apps (Flutter, React Native) with ETag caching and batch fetching. | REST API Controller `/api/v1/translator/...` |

---

## The Universal "Zero-Blank" Fallback Guarantee

Across **every single one of the 10 translation types**, a default value is **strictly guaranteed**:
* **For Eloquent Models (`internal`, `external`, `hybrid`, `inline`)**:
  The default is always the model's own original database column value (`$this->getRawOriginal($field)`). If a translation in the selected language does not exist, it instantly returns the default base attribute. Your UI will never show blank text.
* **For Static UI Strings (`static`)**:
  The default is stored directly in the `name` column of `translator_statics`. If a key has no translation for the active locale, it falls back to `name`.
* **For JSON & PHP Files (`json`, `file`)**:
  Falls back to the default English key or standard fallback locale (`config('app.fallback_locale')`).
* **For Cultural Local Formatter (`local`)**:
  Falls back to standard Western digits and standard Gregorian calendar if the locale's cultural rules are undefined.

---

## 1. Deep Dive: Dynamic Model Translations (Types 1 – 4)

All four dynamic model types are accessed seamlessly through the single master trait:
`use Ataurbdx\Translator\Core\Traits\HasTranslator;`

### Type 1: `TranslationInline` (`inline`)
* **Concept**: Stored directly inside the model's existing table column as JSON.
* **Model Configuration**:
  ```php
  class Tag extends Model
  {
      use HasTranslator;

      protected $translatorType = 'inline';
      protected array $translatable = ['name'];
  }
  ```
* **Performance**: 1 Query, 0 Joins, 0 Extra Tables.

### Type 2: `TranslationInternal` (`internal`)
* **Concept**: Polymorphic key-value storage in a single centralized shared table.
* **Table**: `translator_dynamics` (`translatable_type`, `translatable_id`, `name`, `value` JSON).
* **Auto-Created**: Installed automatically via `php artisan translator:install`.
* **Model Configuration**:
  ```php
  class Category extends Model
  {
      use HasTranslator;

      protected $translatorType = 'internal';
      protected array $translatable = ['name', 'description'];
  }
  ```
* **Performance**: Keeps database schema clean without creating 50 individual tables.

### Type 3: `TranslationExternal` (`external`)
* **Concept**: Dedicated table exclusively for one high-traffic model.
* **Table**: `translator_{table}` (e.g., `translator_listings`).
* **Generation Command**:
  ```bash
  php artisan translator:make:external listings
  ```
  *Reads `listings` columns, lets you pick translatable fields, and generates `create_translator_listings_table` migration automatically!*
* **Model Configuration**:
  ```php
  class Listing extends Model
  {
      use HasTranslator;

      protected $translatorType = 'external';
      protected array $translatable = ['title', 'description', 'address'];
  }
  ```
* **Performance**: Maximum indexing power, composite primary keys, built for millions of rows.

### Type 4: `TranslationHybrid` (`hybrid`)
* **Concept**: Grouped domain table sharing translations among multiple related models using dynamic `entity_type`.
* **Table**: `translator_{domain}` (e.g., `translator_worlds`, `translator_catalog`).
* **Generation Command**:
  ```bash
  php artisan translator:make:hybrid worlds
  ```
  *Super simple command! You do NOT need to hardcode entities in advance. Any entity type (`country`, `division`, `city`, `sub_city`, etc.) can be added on the fly.*
* **Schema Generated**:
  * `id`
  * `entity_type` (e.g. `'country'`, `'city'`)
  * `entity_id` (foreign ID)
  * `locale` (e.g. `'en'`, `'bn'`)
  * `field` (e.g. `'name'`, `'native'`)
  * `value` (translated text)
* **Model Configuration**:
  ```php
  class City extends Model
  {
      use HasTranslator;

      protected $translatorType = 'hybrid';
      protected string $translatorTable = 'translator_worlds';
      protected string $entityType = 'city';
      protected array $translatable = ['name', 'native'];
  }
  ```
* **Performance**: Prevents table sprawl for domain clusters while keeping heavy domain data isolated.

---

## 2. Deep Dive: UI & Application Strings (Types 5 – 7)

### Type 5: `TranslationStatic` (`static`)
* **Concept**: Database-driven UI keys (`translator_statics`) + Redis/Memory caching.
* **Schema Design**:
  * `key`: Structured dot-notation (e.g. `button.add_to_cart`, `menu.dashboard`).
  * `name`: Natural fallback string (e.g. `Add to Cart`).
  * `value`: Multilingual JSON (e.g. `{"en": "Add to Cart", "bn": "কার্টে যোগ করুন"}`).
  * `group`: Category grouping (e.g. `button`, `auth`, `common`).
* **Direct Database Printing (Via Key)**:
  ```blade
  {{ translate('button.add_to_cart') }}
  {{-- Or fluent helper: --}}
  {{ translator('button.add_to_cart') }}
  ```
  - Resolves `bn` ➔ `"কার্টে যোগ করুন"`
  - If missing in `bn`, automatically falls back to `name` ➔ `"Add to Cart"`!
* **Export to JSON File Behavior**:
  - Uses `name` as the JSON file key: `"Add to Cart": "কার্টে যোগ করুন"`.
  - Enables developers to write natural English with native Laravel helper: `{{ __('Add to Cart') }}`.
* **Zero-Export Live Mode**: Real-time live updates via Admin UI without touching files.

### Type 6: `TranslationJson` (`json`)
* **Concept**: Flat JSON language files located in `lang/{locale}.json`.
* **Usage**:
  ```blade
  {{ __('welcome') }}
  ```

### Type 7: `TranslationFile` (`file`)
* **Concept**: Grouped PHP array language files in `lang/{locale}/{group}.php`.
* **Usage**:
  ```blade
  {{ __('auth.failed') }}
  {{ __('validation.required') }}
  ```
* **Use Case**: 100% compatibility with Laravel default packages and legacy files.

---

## 3. Deep Dive: Cultural Formatting (Type 8: `TranslationLocal`)

Cultural formatting rules are stored in the database (`translator_locales`) and cached in memory.

### Available Helper Functions:
```blade
{{-- 1. Localized Digits --}}
{{ translate('2026', type: 'digits', locale: 'bn') }}           {{-- Returns: ২০২৬ --}}

{{-- 2. Localized Numbers & South Asian Grouping --}}
{{ translate(1234567, type: 'number', locale: 'bn') }}          {{-- Returns: ১২,৩৪,৫৬৭ --}}

{{-- 3. Localized Dates & Months --}}
{{ translate($order->created_at, type: 'date', locale: 'bn') }}  {{-- Returns: ০৯ ফেব্রুয়ারি ২০২৬ --}}

{{-- 4. Financial Number-To-Words --}}
{{ translate(1500, type: 'words', currency: 'BDT', locale: 'bn') }} {{-- Returns: এক হাজার পাঁচশত টাকা মাত্র --}}
```

---

## 4. Deep Dive: Central Settings & Automation (Types 9 & 10)

### Central Settings Table: `translator_settings`
* **Purpose**: Allows web administrators to manage API keys, AI model selection, and cache configurations directly from the Web UI or `.env` without modifying server files.
* **Schema**:
  * `id`
  * `key`: (e.g. `'ai_provider'`, `'gemini_api_key'`, `'api_enabled'`, `'cache_driver'`)
  * `value`: Text (encrypted or plain)
  * `type`: `'string'`, `'boolean'`, `'json'`, `'encrypted'`
  * `group`: `'ai'`, `'api'`, `'cache'`, `'general'`

### Type 9: `TranslationAI` (`ai`)
* **Drivers**: Google Gemini, OpenAI GPT-4o-mini, Claude, DeepL.
* **Capabilities**:
  * Auto-translate missing static keys on-demand in the background.
  * Artisan sync command:
    ```bash
    php artisan translator:ai-sync --from=en --to=bn --group=menu
    ```

### Type 10: `TranslationAPI` (`api`)
* **Headless REST Endpoints**:
  * `GET /api/v1/translator/static?locale=bn`
  * `GET /api/v1/translator/locales`
  * `POST /api/v1/translator/batch`
* **Features**: ETag caching, HTTP 304, ultra-low latency for Next.js, React, React Native, and Flutter.

---

## 5. Self-Contained Modular Architecture

The entire codebase is structured into **5 Self-Contained Modules** for maximum portability across Laravel, Node.js, and Flutter:

```text
packages/laravel/
├── composer.json
├── config/
│   └── translator.php
├── src/
│   ├── TranslationServiceProvider.php
│   │
│   ├── Core/
│   │   ├── Translator.php                     # The Master Hub
│   │   ├── Contracts/                         # Engine Interfaces
│   │   ├── Facades/Translator.php             # The Master Facade
│   │   ├── Traits/HasTranslator.php           # Unified Model Trait
│   │   └── Helpers/helpers.php                # translate(), translator()
│   │
│   ├── Modules/
│   │   ├── DynamicModels/                     # Module 1 (Types 1 - 4)
│   │   │   ├── Drivers/                       (Inline, Internal, External, Hybrid)
│   │   │   ├── Models/                        (TranslatorDynamic)
│   │   │   └── Migrations/                    (create_translator_dynamics_table)
│   │   │
│   │   ├── StaticUI/                          # Module 2 (Types 5 - 7)
│   │   │   ├── Drivers/                       (Static, Json, File)
│   │   │   ├── Models/                        (TranslatorStatic)
│   │   │   └── Migrations/                    (create_translator_statics_table)
│   │   │
│   │   ├── CulturalLocale/                    # Module 3 (Type 8)
│   │   │   ├── Drivers/                       (LocalDriver)
│   │   │   ├── Models/                        (TranslatorLocale)
│   │   │   └── Migrations/                    (create_translator_locales_table)
│   │   │
│   │   ├── AutomationAI/                      # Module 4 (Type 9)
│   │   │   ├── Drivers/                       (AiDriver)
│   │   │   └── Providers/                     (Gemini, OpenAI, DeepL)
│   │   │
│   │   └── HeadlessApi/                       # Module 5 (Type 10)
│   │       ├── Controllers/                   (TranslatorApiController)
│   │       ├── Middleware/                    (TranslatorLocaleMiddleware)
│   │       └── Routes/                        (api.php)
│   │
│   └── Console/
│       ├── InstallCommand.php                 # php artisan translator:install
│       ├── MakeExternalCommand.php            # php artisan translator:make:external {table}
│       ├── MakeHybridCommand.php              # php artisan translator:make:hybrid {domain}
│       ├── MakeInlineCommand.php              # php artisan translator:make:inline {table} {column}
│       ├── AiSyncCommand.php                  # php artisan translator:ai-sync
│       └── ExportLocalesCommand.php           # php artisan translator:export-locales
```
