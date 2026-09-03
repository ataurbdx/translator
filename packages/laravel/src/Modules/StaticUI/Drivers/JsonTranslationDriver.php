<?php

namespace Ataurbdx\Translator\Modules\StaticUI\Drivers;

use Ataurbdx\Translator\Modules\StaticUI\Models\TranslatorStatic;
use Illuminate\Support\Facades\File;

class JsonTranslationDriver
{
    /**
     * Get a string directly from a flat JSON file on disk
     */
    public function get(string $key, ?string $locale = null, mixed $default = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $path = resource_path("lang/{$locale}.json");

        if (File::exists($path)) {
            $data = json_decode(File::get($path), true);
            if (is_array($data) && isset($data[$key])) {
                return $data[$key];
            }
        }

        return $default !== null ? $default : $key;
    }

    /**
     * Export database static keys to lang/{locale}.json using `default` as JSON key
     */
    public function exportFromDatabase(string $locale, ?string $path = null): int
    {
        $targetPath = $path ?? resource_path("lang/{$locale}.json");
        $dir = dirname($targetPath);

        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $records = TranslatorStatic::all();
        $output = [];

        foreach ($records as $record) {
            $jsonKey = !empty($record->name) ? $record->name : $record->key;
            $translatedVal = $record->value[$locale] ?? $record->name ?? $record->key;
            $output[$jsonKey] = $translatedVal;
        }

        File::put($targetPath, json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return count($output);
    }

    /**
     * Import a JSON file into the database
     */
    public function importToDatabase(string $filePath, string $locale, string $group = 'imported'): int
    {
        if (!File::exists($filePath)) {
            return 0;
        }

        $data = json_decode(File::get($filePath), true);
        if (!is_array($data)) {
            return 0;
        }

        $count = 0;
        foreach ($data as $key => $val) {
            $record = TranslatorStatic::firstOrNew(['key' => $key]);
            if (!$record->exists) {
                $record->name = $key;
                $record->group = $group;
            }

            $currentValues = $record->value ?? [];
            $currentValues[$locale] = $val;
            $record->value = $currentValues;
            $record->save();
            $count++;
        }

        return $count;
    }
}
