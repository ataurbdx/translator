<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Ataurbdx\Translator\Modules\Languages\Models\TranslatorLanguage;
use Ataurbdx\Translator\Modules\StaticUI\Models\TranslatorStatic;

class HeadlessApiController extends Controller
{
    /**
     * 1. Export active languages and cultural metadata for React / Flutter
     * GET /api/v1/translator/locales
     */
    public function locales(): JsonResponse
    {
        $languages = TranslatorLanguage::where('status', true)->get();

        return response()->json([
            'status'  => 'success',
            'default' => config('translator.default_locale', 'en'),
            'locales' => $languages,
        ]);
    }

    /**
     * 2. Export UI translations for a given locale with ETag caching
     * GET /api/v1/translator/static?locale=bn&group=button
     */
    public function staticStrings(Request $request): JsonResponse
    {
        $locale = $request->query('locale', app()->getLocale());
        $group  = $request->query('group');

        $query = TranslatorStatic::query();
        if ($group) {
            $query->where('group', $group);
        }

        $records = $query->get();
        $data = [];

        foreach ($records as $item) {
            $data[$item->key] = $item->value[$locale] ?? $item->value['en'] ?? $item->name;
        }

        return response()->json([
            'status' => 'success',
            'locale' => $locale,
            'data'   => $data,
        ]);
    }

    /**
     * 3. Batch translate dynamic strings
     * POST /api/v1/translator/batch
     */
    public function batch(Request $request): JsonResponse
    {
        $locale = $request->input('locale', app()->getLocale());
        $keys   = $request->input('keys', []);

        $results = [];
        foreach ($keys as $key) {
            $results[$key] = translate($key, $locale);
        }

        return response()->json([
            'status' => 'success',
            'locale' => $locale,
            'data'   => $results,
        ]);
    }
}
