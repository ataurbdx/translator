<?php

namespace Ataurbdx\Translator\Modules\HeadlessApi\Controllers;

use Ataurbdx\Translator\Facades\Translator;
use Ataurbdx\Translator\Modules\CulturalLocale\Models\TranslatorLocale;
use Ataurbdx\Translator\Modules\Languages\Models\TranslatorLanguage;
use Ataurbdx\Translator\Modules\StaticUI\Models\TranslatorStatic;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class TranslatorApiController extends Controller
{
    /**
     * GET /api/v1/translator/static?locale=bn&group=common
     * Returns static UI translation dictionary for Flutter / Next.js / React
     */
    public function getStatic(Request $request)
    {
        $locale = $request->query('locale', config('translator.default_locale', 'en'));
        $group = $request->query('group');

        $cacheKey = "translator_api_static_{$locale}_" . ($group ?? 'all');
        $ttl = config('translator.cache.ttl', 86400);

        $translations = Cache::remember($cacheKey, $ttl, function () use ($locale, $group) {
            $query = TranslatorStatic::query();
            if ($group) {
                $query->where('group', $group);
            }

            $records = $query->get();
            $data = [];

            foreach ($records as $record) {
                $data[$record->key] = $record->value[$locale] ?? $record->name ?? $record->key;
            }

            return $data;
        });

        $etag = md5(json_encode($translations));
        if ($request->header('If-None-Match') === $etag) {
            return response()->json(null, 304);
        }

        return response()->json([
            'success'      => true,
            'locale'       => $locale,
            'translations' => $translations,
        ])->header('ETag', $etag);
    }

    /**
     * GET /api/v1/translator/locales
     * Returns all active languages with flags and cultural configurations
     */
    public function getLocales(Request $request)
    {
        $languages = TranslatorLanguage::active()->get();
        $locales = TranslatorLocale::where('is_active', true)->get();

        return response()->json([
            'success'   => true,
            'default'   => config('translator.default_locale', 'en'),
            'languages' => $languages,
            'locales'   => $locales,
        ]);
    }

    /**
     * POST /api/v1/translator/batch
     * Translate an array of keys/strings on-the-fly
     */
    public function batchTranslate(Request $request)
    {
        $locale = $request->input('locale', config('translator.default_locale', 'en'));
        $keys = $request->input('keys', []);

        $results = [];
        foreach ($keys as $key) {
            $results[$key] = Translator::static()->get($key, $locale);
        }

        return response()->json([
            'success'      => true,
            'locale'       => $locale,
            'translations' => $results,
        ]);
    }
}
