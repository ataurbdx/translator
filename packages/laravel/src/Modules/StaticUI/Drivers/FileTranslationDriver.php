<?php

namespace Ataurbdx\Translator\Modules\StaticUI\Drivers;

use Ataurbdx\Translator\Modules\StaticUI\Models\TranslatorStatic;
use Illuminate\Support\Facades\File;

class FileTranslationDriver
{
    /**
     * Get a string from a standard PHP array lang file (e.g. auth.failed)
     */
    public function get(string $key, ?string $locale = null, mixed $default = null): string
    {
        $locale = $locale ?? app()->getLocale();
        return __($key, [], $locale) ?: ($default !== null ? $default : $key);
    }

    /**
     * Export a group from database to lang/{locale}/{group}.php
     */
    public function exportGroupToPhp(string $group, string $locale, ?string $dirPath = null): int
    {
        $targetDir = $dirPath ?? resource_path("lang/{$locale}");
        if (!File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $records = TranslatorStatic::where('group', $group)->get();
        $array = [];

        foreach ($records as $record) {
            // strip group prefix if present (e.g. 'auth.failed' -> 'failed')
            $keyName = str_starts_with($record->key, "{$group}.") 
                ? substr($record->key, strlen("{$group}.")) 
                : $record->key;

            $val = $record->value[$locale] ?? $record->name ?? $record->key;
            $array[$keyName] = $val;
        }

        $filePath = "{$targetDir}/{$group}.php";
        $content = "<?php\n\nreturn " . var_export($array, true) . ";\n";
        File::put($filePath, $content);

        return count($array);
    }
}
