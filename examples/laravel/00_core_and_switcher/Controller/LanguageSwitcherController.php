<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Ataurbdx\Translator\Modules\Languages\Models\TranslatorLanguage;

class LanguageSwitcherController extends Controller
{
    /**
     * Switch user session locale dynamically.
     * Supports ANY language available in translator_languages table!
     */
    public function switch(string $locale): RedirectResponse
    {
        // 1. Verify that the requested language exists and is active
        $language = TranslatorLanguage::where('code', $locale)
            ->where('status', true)
            ->first();

        if ($language) {
            // 2. Persist in session
            session(['locale' => $locale]);

            // 3. Set immediate application locale
            app()->setLocale($locale);
        }

        // 4. Redirect back to previous page
        return redirect()->back();
    }
}
