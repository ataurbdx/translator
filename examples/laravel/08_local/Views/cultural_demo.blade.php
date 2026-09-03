<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>Type 8: Cultural & Regional Formatter Demo</title>
    <style>
        body { font-family: sans-serif; padding: 40px; background: #f8fafc; }
        .box { background: #fff; padding: 24px; border-radius: 8px; max-width: 650px; margin-bottom: 24px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e2e8f0; }
        .val { font-weight: 600; color: #0284c7; }
    </style>
</head>
<body>

<div class="box">
    <h2>Type 8: Cultural & Regional Localization (<code>translator_locales</code>)</h2>
    <p>3-Tier Fallback: <strong>Cache &rarr; Database &rarr; JSON File &rarr; Built-in Code</strong> (Works even if DB is down!)</p>
    <p>Active Locale: <strong>{{ app()->getLocale() }}</strong></p>

    <div style="background: #f1f5f9; padding: 16px; border-radius: 6px; margin: 16px 0;">
        <h4>All Ways to Print Cultural Data in Blade:</h4>

        <div class="row">
            <span><strong>1. Bengali Numerals (2026):</strong><br><code>translate('2026', type: 'digits')</code></span>
            <span class="val">{{ translate('2026', type: 'digits', locale: 'bn') }}</span>
        </div>

        <div class="row">
            <span><strong>2. South Asian Lakh/Crore (12,50,000):</strong><br><code>translate(1250000, type: 'number')</code></span>
            <span class="val">৳{{ translate(1250000, type: 'number', locale: 'bn') }}</span>
        </div>

        <div class="row">
            <span><strong>3. Western Thousands (1,250,000):</strong><br><code>translate(1250000, type: 'number', locale: 'en')</code></span>
            <span class="val">${{ translate(1250000, type: 'number', locale: 'en') }}</span>
        </div>

        <div class="row">
            <span><strong>4. Localized Bengali Date:</strong><br><code>translate(now(), type: 'date')</code></span>
            <span class="val">{{ translate(now(), type: 'date', locale: 'bn') }}</span>
        </div>

        <div class="row" style="border-bottom: none;">
            <span><strong>5. Invoice Number-to-Words (Cheque Printing):</strong><br><code>translate(1500, type: 'words', currency: 'BDT')</code></span>
            <span class="val" style="color: #059669;">{{ translate(1500, type: 'words', currency: 'BDT', locale: 'bn') }}</span>
        </div>
    </div>
</div>

</body>
</html>
