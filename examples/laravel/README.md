# Complete Laravel Blueprint: All 10 Translation Types & Core Switcher

Welcome to the comprehensive, production-ready implementation guide for the **Master Universal Translator** (`ataurbdx/translator`).

Each folder in this directory is **100% self-contained**, including migrations, Eloquent models, controllers, routes, Blade templates, seeders, and documentation.

---

## 🗂️ Master Index of Examples

| Folder | Type Name | Key Identifier | Description |
|---|---|---|---|
| [**`00_core_and_switcher/`**](./00_core_and_switcher) | **Core Foundation** | `core` | **Dynamic Languages & Switcher**: Add any language (Spanish, French, German, Hindi, Arabic), Language Switcher dropdown, session middleware, and universal zero-blank fallback. |
| [**`01_inline/`**](./01_inline) | `TranslationInline` | `inline` | **In-Table JSON Column**: Stored inside model table column (`name` = `{"en":"Apple","bn":"আপেল"}`). 0 extra tables, 0 joins. |
| [**`02_internal/`**](./02_internal) | `TranslationInternal` | `internal` | **Shared Polymorphic Table**: Single table (`translator_dynamics`) shared across multiple models (`Category`, `Brand`, `Faq`). |
| [**`03_external/`**](./03_external) | `TranslationExternal` | `external` | **Dedicated Table Per Model**: Standalone table (`translator_listings`) for millions of rows with custom composite indexing. |
| [**`04_hybrid/`**](./04_hybrid) | `TranslationHybrid` | `hybrid` | **Grouped Domain Table**: Related family of models (e.g. World cluster: `Country`, `City`) sharing 1 domain table (`translator_worlds`). |
| [**`05_static/`**](./05_static) | `TranslationStatic` | `static` | **Database-Driven UI Strings**: Buttons, menus, headers stored in DB (`translator_statics`) with memory & Redis caching. |
| [**`06_json/`**](./06_json) | `TranslationJson` | `json` | **Flat JSON Files**: Native Laravel disk files (`lang/*.json`) using natural text sentences as keys. |
| [**`07_file/`**](./07_file) | `TranslationFile` | `file` | **PHP Array Files**: Traditional Laravel nested files (`lang/{locale}/*.php`) for full legacy package compatibility. |
| [**`08_local/`**](./08_local) | `TranslationLocal` | `local` | **Cultural Regional Formatter**: Bengali numerals (`২০২৬`), Lakh/Crore grouping (`১২,৫০,০০০`), localized dates, and cheque words. |
| [**`09_ai/`**](./09_ai) | `TranslationAI` | `ai` | **AI Auto-Translation**: Background and on-demand translation using Google Gemini, OpenAI, Claude, or DeepL. |
| [**`10_api/`**](./10_api) | `TranslationAPI` | `api` | **Headless REST API**: Real-time endpoints with ETag caching for React, Next.js, and Flutter mobile apps. |

---

## 🚀 Quick Setup & Installation

Install the package via Composer:
```bash
composer require ataurbdx/translator
```

Install only what you need (Modular On-Demand):
```bash
# 1. Interactive prompt (select the types you want):
php artisan translator:install

# 2. Core only (Languages & Settings):
php artisan translator:install --type=core

# 3. Specific features:
php artisan translator:install --type=static    # Static UI
php artisan translator:install --type=internal  # Eloquent models
php artisan translator:install --type=local     # Cultural formatting

# 4. Full Suite (all tables):
php artisan translator:install --all
```

---

## 🖨️ Universal Printing / Output Cheatsheet

No matter which translation type you use, rendering translations in Blade is clean and unified:

```blade
{{-- 1. Eloquent Models (Inline, Internal, External, Hybrid) --}}
{{ $post->title }}                            {{-- Auto-resolves based on active locale --}}
{{ $post->translate('title', 'bn') }}         {{-- Explicit locale --}}
{{ $post->title_es }}                         {{-- Suffix shorthand --}}

{{-- 2. Static UI Strings & Keys --}}
{{ translate('button.add_to_cart') }}
{{ translate('welcome', default: 'Welcome!') }}

{{-- 3. Cultural & Regional Formatting --}}
{{ translate('2026', type: 'digits') }}                   {{-- ২০২৬ --}}
{{ translate(1250000, type: 'number') }}                  {{-- ১২,৫০,০০০ --}}
{{ translate(now(), type: 'date') }}                      {{-- ০৯ সেপ্টেম্বর ২০২৬ --}}
{{ translate($invoice->total, type: 'words', currency: 'BDT') }} {{-- এক হাজার পাঁচশত টাকা মাত্র --}}

{{-- 4. Flat JSON & File Language Strings --}}
{{ __('Add to Cart') }}
{{ __('messages.auth.failed') }}
```
