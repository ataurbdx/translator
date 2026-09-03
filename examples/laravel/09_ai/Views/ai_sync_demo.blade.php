<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>Type 9: AI On-Demand & Background Sync Demo</title>
    <style>
        body { font-family: sans-serif; padding: 40px; background: #f8fafc; }
        .box { background: #fff; padding: 24px; border-radius: 8px; max-width: 650px; margin-bottom: 24px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="box">
    <h2>Type 9: AI Automation & Sync (Gemini, OpenAI, Claude, DeepL)</h2>
    <p>Default AI Provider: <strong>{{ config('translator.ai.default_provider', 'gemini') }}</strong></p>

    {{-- Live AJAX Form to Test AI --}}
    <form id="aiForm">
        @csrf
        <div>
            <label>Text to Translate (English):</label><br>
            <textarea id="aiInput" style="width: 100%; padding: 8px; margin: 4px 0 10px; height: 70px;">The Master Universal Translation Engine for Laravel and Flutter.</textarea>
        </div>
        <div>
            <label>Target Language:</label><br>
            <select id="targetLocale" style="padding: 8px; margin-bottom: 12px;">
                <option value="bn">Bengali (বাংলা)</option>
                <option value="es">Spanish (Español)</option>
                <option value="ar">Arabic (العربية)</option>
                <option value="fr">French (Français)</option>
            </select>
        </div>
        <button type="button" onclick="runAi()" style="padding: 8px 18px; background: #9333ea; color: #fff; border: none; border-radius: 4px; cursor: pointer;">
            ⚡ Translate via AI
        </button>
    </form>

    <div id="aiResult" style="margin-top: 16px; padding: 14px; background: #f3e8ff; border-radius: 6px; display: none;">
        <strong>AI Translation Output:</strong>
        <p id="outputTxt" style="font-size: 16px; color: #6b21a8; margin: 6px 0 0;"></p>
    </div>
</div>

<script>
function runAi() {
    const text = document.getElementById('aiInput').value;
    const to = document.getElementById('targetLocale').value;
    const btn = event.target;
    btn.innerText = 'Translating...';

    fetch('{{ route('ai.text') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ text: text, from: 'en', to: to })
    })
    .then(res => res.json())
    .then(data => {
        btn.innerText = '⚡ Translate via AI';
        document.getElementById('aiResult').style.display = 'block';
        document.getElementById('outputTxt').innerText = data.translated;
    });
}
</script>

</body>
</html>
