# Type 8: TranslationLocal (`local`)

Cultural and regional formatting engine supporting localized numerals (Bengali `০-৯`, Arabic `٠-٩`), South Asian Lakh/Crore number grouping, localized dates, and financial number-to-words for invoices and cheques.

---

## ⚡ 3-Tier Zero-Failure Architecture
The `local` driver never crashes your website even if the database is offline:
1. **Tier 1 (RAM / Redis Cache)**: Sub-millisecond warm hits.
2. **Tier 2 (Database - `translator_locales`)**: Dynamic rules stored per locale.
3. **Tier 3 (Fallback Files - `resources/lang/locales/*.json`)**: Generated via `php artisan translator:export-locales`.
4. **Tier 4 (Hardcoded Code Rules)**: Built-in algorithms guaranteeing zero blanks.

---

## 🛠️ Step-by-Step Implementation

### 1. Install Cultural Locales Table
```bash
php artisan translator:install --type=local
```

### 2. Export Fallback JSON Files
```bash
php artisan translator:export-locales --locale=bn
```

### 3. Usage & Printing in Blade
```blade
{{-- 1. Localized Digits --}}
{{ translate('2026', type: 'digits') }}   {{-- ২০২৬ --}}

{{-- 2. South Asian Lakh / Crore grouping --}}
{{ translate(1250000, type: 'number') }}  {{-- ১২,৫০,০০০ --}}

{{-- 3. Localized Date --}}
{{ translate(now(), type: 'date') }}

{{-- 4. Financial Number-To-Words for Cheque & Invoices --}}
{{ translate($invoice->total, type: 'words', currency: 'BDT') }}
{{-- Returns: এক হাজার পাঁচশত টাকা মাত্র --}}
```
