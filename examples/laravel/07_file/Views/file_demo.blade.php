<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>Type 7: PHP Array File Translations Demo</title>
    <style>
        body { font-family: sans-serif; padding: 40px; background: #f8fafc; }
        .box { background: #fff; padding: 24px; border-radius: 8px; max-width: 650px; margin-bottom: 24px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="box">
    <h2>Type 7: Traditional PHP Array Language Files (<code>resources/lang/{locale}/*.php</code>)</h2>
    <p>Organized in nested group files &bull; Traditional Laravel structure &bull; Full compatibility with legacy packages</p>
    <p>Active Locale: <strong>{{ app()->getLocale() }}</strong></p>

    <div style="background: #f1f5f9; padding: 16px; border-radius: 6px; margin: 16px 0;">
        <h4>Rendering Examples via Laravel <code>__('messages.key')</code>:</h4>

        {{-- 1. Simple nested key --}}
        <p>
            <strong>1. Auth Error Message:</strong><br>
            <code>__('messages.auth.failed')</code> &rarr; 
            <span style="color: #dc2626; font-weight: 600;">{{ __('messages.auth.failed') }}</span>
        </p>

        {{-- 2. Replacement parameter --}}
        <p>
            <strong>2. Throttle Warning:</strong><br>
            <code>__('messages.auth.throttle', ['seconds' => 60])</code> &rarr; 
            <span>{{ __('messages.auth.throttle', ['seconds' => 60]) }}</span>
        </p>

        {{-- 3. Invoice header --}}
        <p>
            <strong>3. Invoice Header:</strong><br>
            <code>__('messages.invoice.title')</code> &rarr; 
            <strong>{{ __('messages.invoice.title') }}</strong>
        </p>
    </div>

    <h4>Bi-Directional Engine Support:</h4>
    <p>You can read directly via engine: <code>Translator::file()->get('messages.auth.failed', 'es')</code> or import your whole PHP file into database <code>translator_statics</code>!</p>
</div>

</body>
</html>
