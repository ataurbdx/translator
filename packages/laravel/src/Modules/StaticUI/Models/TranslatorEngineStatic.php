<?php

namespace Ataurbdx\TranslatorEngine\Modules\StaticUI\Models;

use Illuminate\Database\Eloquent\Model;

class TranslatorEngineStatic extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'value' => 'array',
    ];

    public function getTable()
    {
        return config('translator_engine.tables.statics', 'translator_engine_statics');
    }

    /**
     * Build hierarchical tree from flat dot-notation keys
     */
    public static function buildTree($translations)
    {
        $tree = [];
        foreach ($translations as $translation) {
            $parts = explode('.', $translation->key);
            $current = &$tree;
            $builtPath = '';

            foreach ($parts as $index => $part) {
                $builtPath = $builtPath ? $builtPath . '.' . $part : $part;

                if (!isset($current[$part])) {
                    $current[$part] = [
                        'label'       => $part,
                        'path'        => $builtPath,
                        'translation' => null,
                        'children'    => [],
                    ];
                }

                if ($index === count($parts) - 1) {
                    $current[$part]['translation'] = $translation;
                }

                $current = &$current[$part]['children'];
            }
        }
        return $tree;
    }
}
