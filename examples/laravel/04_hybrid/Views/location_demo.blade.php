<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>Type 4: Grouped Domain Table (Hybrid) Demo</title>
    <style>
        body { font-family: sans-serif; padding: 40px; background: #f8fafc; }
        .box { background: #fff; padding: 24px; border-radius: 8px; max-width: 750px; margin-bottom: 24px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .country-card { background: #f1f5f9; padding: 16px; border-radius: 6px; margin-bottom: 12px; }
        .city-tag { display: inline-block; background: #e0e7ff; color: #3730a3; padding: 4px 10px; border-radius: 4px; font-size: 13px; margin: 4px 4px 0 0; }
    </style>
</head>
<body>

<div class="box">
    <h2>Type 4: Grouped Domain Table (<code>translator_worlds</code>)</h2>
    <p>Notice: Both <code>Country</code> and <code>City</code> models share <strong>1 single domain table</strong> instead of creating 10 separate tables!</p>
    <p>Active Locale: <strong>{{ app()->getLocale() }}</strong></p>

    {{-- Form to Add Country & City --}}
    <form action="{{ route('locations.store') }}" method="POST">
        @csrf
        <div>
            <label>Country ISO2 (e.g. BD, US, ES):</label><br>
            <input type="text" name="iso2" required maxlength="2" style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <div>
            <label>Country Name (English):</label><br>
            <input type="text" name="country_name[en]" required style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <div>
            <label>Country Name (Bengali):</label><br>
            <input type="text" name="country_name[bn]" style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <div>
            <label>Country Name (Spanish):</label><br>
            <input type="text" name="country_name[es]" style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        
        <hr style="margin: 16px 0; border: none; border-top: 1px dashed #cbd5e1;">

        <div>
            <label>City Name (English):</label><br>
            <input type="text" name="city_name[en]" placeholder="e.g. Dhaka, Madrid" style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>
        <div>
            <label>City Name (Bengali):</label><br>
            <input type="text" name="city_name[bn]" placeholder="e.g. ঢাকা" style="width: 100%; padding: 8px; margin: 4px 0 10px;">
        </div>

        <button type="submit" style="padding: 8px 18px; background: #4f46e5; color: #fff; border: none; border-radius: 4px;">Save Location Cluster</button>
    </form>
</div>

<div class="box">
    <h3>Existing Countries & Cities (All Ways to Print):</h3>
    @forelse($countries as $country)
        <div class="country-card">
            <h4>
                {{-- 1. Country auto-translated --}}
                <span style="color: #4338ca;">{{ $country->name }}</span> ({{ $country->iso2 }})
                {{-- 2. Explicit translation --}}
                <small style="font-size: 13px; color: #64748b; margin-left: 10px;">
                    Bengali: {{ $country->translate('name', 'bn') }} | Spanish: {{ $country->translate('name', 'es') }}
                </small>
            </h4>
            
            <div style="margin-top: 8px;">
                <strong>Cities:</strong>
                @forelse($country->cities as $city)
                    {{-- 3. City auto-translated from same translator_worlds table --}}
                    <span class="city-tag">{{ $city->name }} ({{ $city->translate('name', 'bn') }})</span>
                @empty
                    <span style="color: #94a3b8; font-size: 13px;">No cities added yet.</span>
                @endforelse
            </div>
        </div>
    @empty
        <p>No countries added yet.</p>
    @endforelse
</div>

</body>
</html>
