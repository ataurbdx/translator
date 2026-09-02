<?php

namespace Ataurbdx\TranslatorEngine\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Ataurbdx\TranslatorEngine\Modules\DynamicModels\Drivers\InlineTranslationDriver inline(?\Illuminate\Database\Eloquent\Model $model = null)
 * @method static \Ataurbdx\TranslatorEngine\Modules\DynamicModels\Drivers\InternalTranslationDriver internal(?\Illuminate\Database\Eloquent\Model $model = null)
 * @method static \Ataurbdx\TranslatorEngine\Modules\DynamicModels\Drivers\ExternalTranslationDriver external(?\Illuminate\Database\Eloquent\Model $model = null)
 * @method static \Ataurbdx\TranslatorEngine\Modules\DynamicModels\Drivers\HybridTranslationDriver hybrid(?\Illuminate\Database\Eloquent\Model $model = null)
 * @method static \Ataurbdx\TranslatorEngine\Modules\StaticUI\Drivers\StaticTranslationDriver static()
 * @method static \Ataurbdx\TranslatorEngine\Modules\StaticUI\Drivers\JsonTranslationDriver json()
 * @method static \Ataurbdx\TranslatorEngine\Modules\StaticUI\Drivers\FileTranslationDriver file()
 * @method static \Ataurbdx\TranslatorEngine\Modules\CulturalLocale\Drivers\LocalTranslationDriver local()
 * @method static \Ataurbdx\TranslatorEngine\Modules\AutomationAI\Drivers\AiTranslationDriver ai()
 * @method static mixed type(string $type, mixed $target = null)
 *
 * @see \Ataurbdx\TranslatorEngine\Core\TranslatorEngine
 */
class TranslatorEngine extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'translator-engine';
    }
}
