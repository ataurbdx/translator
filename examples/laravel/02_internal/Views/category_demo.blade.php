<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>Type 2: Internal Polymorphic Translations Demo</title>
    <style>
        body { font-family: sans-serif; padding: 40px; background: #f8fafc; }
        .box { background: #fff; padding: 24px; border-radius: 8px; max-width: 700px; margin-bottom: 24px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #cbd5e1; padding: 10px; text-align: left; }
        th { background: #f1f5f9; }
    </style>
</head>
<body>

<div class="box">
    <h2>Type 2: Internal Polymorphic Table (<code>translator_dynamics</code>)</h2>
    <p>Active Locale: <strong>{{ app()->getLocale() }}</strong></p>

    {{-- Form to Add Category --}}
    <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <div>
            <label>Slug:</label><br>
            <input type="text" name="slug" required style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <div>
            <label>Name (English):</label><br>
            <input type="text" name="name[en]" required style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <div>
            <label>Name (Bengali):</label><br>
            <input type="text" name="name[bn]" style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <div>
            <label>Name (Spanish):</label><br>
            <input type="text" name="name[es]" style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <div>
            <label>Description (English):</label><br>
            <textarea name="description[en]" style="width: 100%; padding: 8px; margin: 4px 0 10px;"></textarea>
        </div>
        <div>
            <label>Description (Bengali):</label><br>
            <textarea name="description[bn]" style="width: 100%; padding: 8px; margin: 4px 0 10px;"></textarea>
        </div>
        <button type="submit" style="padding: 8px 18px; background: #059669; color: #fff; border: none; border-radius: 4px;">Save Category</button>
    </form>
</div>

<div class="box">
    <h3>Existing Categories (All Ways to Print):</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Auto Locale <code>$cat->name</code></th>
                <th>Explicit Spanish <code>$cat->translate('name', 'es')</code></th>
                <th>Description</th>
                <th>Polymorphic Count</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $cat)
                <tr>
                    <td>{{ $cat->id }}</td>
                    {{-- 1. Auto-resolves by app locale --}}
                    <td><strong style="color: #2563eb;">{{ $cat->name }}</strong></td>
                    {{-- 2. Explicit locale call --}}
                    <td>{{ $cat->translate('name', 'es') }}</td>
                    {{-- 3. Translatable description with fallback --}}
                    <td>{{ $cat->description }}</td>
                    {{-- 4. Count of polymorphic translations relation --}}
                    <td><code>{{ $cat->translations->count() }} field(s)</code></td>
                </tr>
            @empty
                <tr><td colspan="5">No categories found. Add one above!</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

</body>
</html>
