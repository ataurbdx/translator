<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>Type 3: Dedicated Table Translations Demo</title>
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
    <h2>Type 3: Dedicated Translation Table (<code>translator_listings</code>)</h2>
    <p>Active Locale: <strong>{{ app()->getLocale() }}</strong></p>

    {{-- Form to Add Listing --}}
    <form action="{{ route('listings.store') }}" method="POST">
        @csrf
        <div>
            <label>Price ($):</label><br>
            <input type="number" name="price" value="250000" required style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <div>
            <label>Title (English):</label><br>
            <input type="text" name="title[en]" required style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <div>
            <label>Title (Bengali):</label><br>
            <input type="text" name="title[bn]" style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <div>
            <label>Address (English):</label><br>
            <input type="text" name="address[en]" style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <div>
            <label>Address (Bengali):</label><br>
            <input type="text" name="address[bn]" style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <button type="submit" style="padding: 8px 18px; background: #d97706; color: #fff; border: none; border-radius: 4px;">Save High-Traffic Listing</button>
    </form>
</div>

<div class="box">
    <h3>Existing Listings (All Ways to Print):</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Auto Locale <code>$listing->title</code></th>
                <th>Explicit Bengali <code>$listing->translate('title', 'bn')</code></th>
                <th>Address</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @forelse($listings as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    {{-- 1. Auto-resolves by app locale --}}
                    <td><strong style="color: #b45309;">{{ $item->title }}</strong></td>
                    {{-- 2. Explicit locale call --}}
                    <td>{{ $item->translate('title', 'bn') }}</td>
                    {{-- 3. Translatable address --}}
                    <td>{{ $item->address }}</td>
                    {{-- 4. Number / currency formatting --}}
                    <td>${{ number_format($item->price, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="5">No listings found. Add one above!</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 16px;">
        {{ $listings->links() }}
    </div>
</div>

</body>
</html>
