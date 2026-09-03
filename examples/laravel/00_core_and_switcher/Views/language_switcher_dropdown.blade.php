{{-- 
  ========================================================================
  DYNAMIC LANGUAGE SWITCHER DROPDOWN (BLADE COMPONENT)
  ========================================================================
  - Automatically fetches all active languages from translator_languages.
  - Highlights currently active language.
  - Supports flags, native names, and RTL/LTR directions.
--}}

@php
    $activeLocale = app()->getLocale();
    $languages = \Ataurbdx\Translator\Modules\Languages\Models\TranslatorLanguage::where('status', true)
        ->orderBy('sort_order')
        ->get();
    $currentLang = $languages->firstWhere('code', $activeLocale) ?? $languages->first();
@endphp

<div class="dropdown language-switcher" style="position: relative; display: inline-block;">
    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 6px 14px; border-radius: 6px; cursor: pointer;">
        <span class="flag-icon">{{ $currentLang?->flag }}</span>
        <span class="lang-name" style="margin-left: 6px; font-weight: 600;">{{ $currentLang?->native ?? strtoupper($activeLocale) }}</span>
    </button>
    
    <ul class="dropdown-menu" aria-labelledby="languageDropdown" style="position: absolute; min-width: 160px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-radius: 8px; list-style: none; padding: 8px 0; margin: 4px 0 0; background: #fff; border: 1px solid #e2e8f0; z-index: 1050;">
        @foreach($languages as $lang)
            <li>
                <a class="dropdown-item {{ $lang->code === $activeLocale ? 'active' : '' }}" 
                   href="{{ route('language.switch', $lang->code) }}" 
                   style="display: flex; align-items: center; padding: 8px 16px; text-decoration: none; color: #1a202c; font-size: 14px;">
                    <span style="font-size: 18px; margin-right: 10px;">{{ $lang->flag }}</span>
                    <span>{{ $lang->native }} ({{ strtoupper($lang->code) }})</span>
                    @if($lang->code === $activeLocale)
                        <span style="margin-left: auto; color: #10b981;">✔</span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</div>
