<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>Type 1: Inline JSON Translations Demo</title>
    <style>
        body { font-family: sans-serif; padding: 40px; background: #f8fafc; }
        .box { background: #fff; padding: 24px; border-radius: 8px; max-width: 650px; margin-bottom: 24px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #cbd5e1; padding: 10px; text-align: left; }
        th { background: #f1f5f9; }
    </style>
</head>
<body>

<div class="box">
    <h2>Type 1: Inline JSON Translations (Zero Joins, Fast Reads)</h2>
    <p>Active Locale: <strong>{{ app()->getLocale() }}</strong></p>

    {{-- Form to Add Tag --}}
    <form action="{{ route('tags.store') }}" method="POST">
        @csrf
        <div>
            <label>Slug:</label><br>
            <input type="text" name="slug" required style="width: 100%; padding: 8px; margin: 4px 0 12px;">
        </div>
        <div>
            <label>Name (English):</label><br>
            <input type="text" name="name[en]" required style="width: 100%; padding: 8px; margin: 4px 0 12px;">
        </div>
        <div>
            <label>Name (Bengali):</label><br>
            <input type="text" name="name[bn]" style="width: 100%; padding: 8px; margin: 4px 0 12px;">
        </div>
        <div>
            <label>Name (Spanish):</label><br>
            <input type="text" name="name[es]" style="width: 100%; padding: 8px; margin: 4px 0 12px;">
        </div>
        <button type="submit" style="padding: 8px 18px; background: #2563eb; color: #fff; border: none; border-radius: 4px;">Save Tag</button>
    </form>
</div>

<div class="box">
    <h3>Existing Tags Output (All Ways to Print):</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Auto Locale <code>$tag->name</code></th>
                <th>Explicit Bengali <code>$tag->translate('name', 'bn')</code></th>
                <th>Raw JSON <code>$tag->getRawOriginal('name')</code></th>
            </tr>
        </thead>
        <tbody>
            @forelse($tags as $tag)
                <tr>
                    <td>{{ $tag->id }}</td>
                    {{-- 1. Auto resolves to active locale --}}
                    <td><strong style="color: #059669;">{{ $tag->name }}</strong></td>
                    {{-- 2. Explicit locale request --}}
                    <td>{{ $tag->translate('name', 'bn') }}</td>
                    {{-- 3. Raw database storage --}}
                    <td><code>{{ json_encode($tag->getRawOriginal('name')) }}</code></td>
                </tr>
            @empty
                <tr><td colspan="4">No tags found. Add one above!</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

</body>
</html>
