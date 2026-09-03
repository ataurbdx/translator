<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(app()->getLocale(), ['ar', 'fa', 'ur']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>Translator Core & Dynamic Language Switcher Demo</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; padding: 40px; background: #f8fafc; color: #1e293b; }
        .card { background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; background: #e0f2fe; color: #0369a1; }
    </style>
</head>
<body>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Language Switcher Demo</h2>
        @include('00_core_and_switcher.Views.language_switcher_dropdown')
    </div>

    <p><strong>Active Application Locale:</strong> <span class="badge">{{ app()->getLocale() }}</span></p>
    <p><strong>Fallback Locale:</strong> <span class="badge">{{ config('translator.fallback_locale', 'en') }}</span></p>

    <hr style="margin: 20px 0; border: none; border-top: 1px solid #e2e8f0;">

    <h3>Zero-Blank Fallback Rule in Action:</h3>
    <p>
        If a translation key exists in Spanish, French, or Bengali, it displays that language.<br>
        If missing, it automatically falls back to <code>English</code>.<br>
        If missing in English too, it displays the key/original default — <strong>never blank or broken!</strong>
    </p>

    <div style="background: #f1f5f9; padding: 16px; border-radius: 8px; margin-top: 16px;">
        <strong>Translated UI String:</strong><br>
        <code>translate('welcome_message')</code> &rarr; 
        <span style="font-weight: 600; color: #2563eb;">{{ translate('welcome_message', default: 'Welcome to our platform!') }}</span>
    </div>
</div>

</body>
</html>
