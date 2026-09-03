<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>Type 5: Database-Driven Static UI Translations Demo</title>
    <style>
        body { font-family: sans-serif; padding: 40px; background: #f8fafc; }
        .box { background: #fff; padding: 24px; border-radius: 8px; max-width: 750px; margin-bottom: 24px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #cbd5e1; padding: 10px; text-align: left; }
        th { background: #f1f5f9; }
    </style>
</head>
<body>

<div class="box">
    <h2>Type 5: Database-Driven Static UI Strings (<code>translator_statics</code>)</h2>
    <p>Stored in Database &bull; Editable in Admin Panel &bull; High-Speed Memory/Redis Cache &bull; Zero JSON export needed</p>
    <p>Active Locale: <strong>{{ app()->getLocale() }}</strong></p>

    {{-- Form to Add Static UI Key --}}
    <form action="{{ route('static.store') }}" method="POST">
        @csrf
        <div>
            <label>Key Name (e.g. <code>button.save</code>, <code>menu.cart</code>):</label><br>
            <input type="text" name="key" required placeholder="button.add_to_cart" style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <div>
            <label>Default Name / English Fallback:</label><br>
            <input type="text" name="name" required placeholder="Add to Cart" style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <div>
            <label>English Value:</label><br>
            <input type="text" name="values[en]" value="Add to Cart" style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <div>
            <label>Bengali Value:</label><br>
            <input type="text" name="values[bn]" placeholder="কার্টে যোগ করুন" style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <div>
            <label>Spanish Value:</label><br>
            <input type="text" name="values[es]" placeholder="Añadir a la cesta" style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <div>
            <label>Group:</label><br>
            <input type="text" name="group" value="button" style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <button type="submit" style="padding: 8px 18px; background: #0284c7; color: #fff; border: none; border-radius: 4px;">Save Static Key to DB</button>
    </form>
</div>

<div class="box">
    <h3>Rendering & Printing Static Keys in Blade (All Ways):</h3>

    <div style="margin: 16px 0; padding: 16px; background: #f1f5f9; border-radius: 6px;">
        <h4>Live Output for <code>button.add_to_cart</code>:</h4>
        
        {{-- 1. Helper function translate('key') --}}
        <p><strong>1. Helper <code>translate('button.add_to_cart')</code>:</strong> 
            <button style="padding: 6px 14px; background: #0284c7; color: #fff; border: none; border-radius: 4px;">
                {{ translate('button.add_to_cart', default: 'Add to Cart') }}
            </button>
        </p>

        {{-- 2. Explicit locale parameter --}}
        <p><strong>2. Explicit Bengali:</strong> <code>{{ translate('button.add_to_cart', 'bn') }}</code></p>
        <p><strong>3. Explicit Spanish:</strong> <code>{{ translate('button.add_to_cart', 'es') }}</code></p>

        {{-- 3. Facade call directly --}}
        <p><strong>4. Via Facade:</strong> <code>Translator::static()->get('button.add_to_cart', 'bn')</code> &rarr; {{ \Ataurbdx\Translator\Facades\Translator::static()->get('button.add_to_cart', 'bn') }}</p>
    </div>

    <h4>Stored Static Keys in <code>translator_statics</code>:</h4>
    <table>
        <thead>
            <tr>
                <th>Key</th>
                <th>Default Name</th>
                <th>Group</th>
                <th>Current Output (Locale: {{ app()->getLocale() }})</th>
            </tr>
        </thead>
        <tbody>
            @forelse($statics as $st)
                <tr>
                    <td><code>{{ $st->key }}</code></td>
                    <td>{{ $st->name }}</td>
                    <td><span style="background: #e2e8f0; padding: 2px 6px; border-radius: 4px;">{{ $st->group }}</span></td>
                    <td><strong>{{ translate($st->key) }}</strong></td>
                </tr>
            @empty
                <tr><td colspan="4">No static keys found. Add one above!</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

</body>
</html>
