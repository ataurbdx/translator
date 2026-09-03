<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>Type 6: Flat JSON Translations Demo</title>
    <style>
        body { font-family: sans-serif; padding: 40px; background: #f8fafc; }
        .box { background: #fff; padding: 24px; border-radius: 8px; max-width: 650px; margin-bottom: 24px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="box">
    <h2>Type 6: Flat JSON Language Files (<code>resources/lang/*.json</code>)</h2>
    <p>Uses natural English text as the translation key &bull; Completely native Laravel <code>__()</code> support</p>
    <p>Active Locale: <strong>{{ app()->getLocale() }}</strong></p>

    <div style="background: #f1f5f9; padding: 16px; border-radius: 6px; margin: 16px 0;">
        <h4>Rendering Examples via Native Laravel <code>__()</code>:</h4>

        {{-- 1. Simple text translation --}}
        <p>
            <strong>1. Button Label:</strong><br>
            <code>__('Add to Cart')</code> &rarr; 
            <button style="padding: 6px 14px; background: #16a34a; color: #fff; border: none; border-radius: 4px;">
                {{ __('Add to Cart') }}
            </button>
        </p>

        {{-- 2. String with parameter replacements --}}
        <p>
            <strong>2. Parameter Replacement:</strong><br>
            <code>__('Welcome back, :name!', ['name' => 'Ataur'])</code> &rarr; 
            <strong style="color: #15803d;">{{ __('Welcome back, :name!', ['name' => 'Ataur']) }}</strong>
        </p>

        {{-- 3. Order number replacement --}}
        <p>
            <strong>3. Order Confirmation:</strong><br>
            <code>__('Your order #:order has been placed successfully.', ['order' => '99401'])</code> &rarr; 
            <em>{{ __('Your order #:order has been placed successfully.', ['order' => '99401']) }}</em>
        </p>
    </div>

    <h4>Syncing with Translator:</h4>
    <p>You can export database keys from <code>translator_statics</code> directly into your project's <code>lang/bn.json</code> or <code>lang/es.json</code> with 1 command or controller call!</p>
</div>

</body>
</html>
