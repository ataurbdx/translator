<?php

namespace Ataurbdx\Translator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Ataurbdx\Translator\Modules\DynamicModels\Drivers\InlineTranslationDriver inline(?\Illuminate\Database\Eloquent\Model $model = null)
 * @method static \Ataurbdx\Translator\Modules\DynamicModels\Drivers\InternalTranslationDriver internal(?\Illuminate\Database\Eloquent\Model $model = null)
 * @method static \Ataurbdx\Translator\Modules\DynamicModels\Drivers\ExternalTranslationDriver external(?\Illuminate\Database\Eloquent\Model $model = null)
 * @method static \Ataurbdx\Translator\Modules\DynamicModels\Drivers\HybridTranslationDriver hybrid(?\Illuminate\Database\Eloquent\Model $model = null)
 * @method static \Ataurbdx\Translator\Modules\StaticUI\Drivers\StaticTranslationDriver static()
 * @method static \Ataurbdx\Translator\Modules\StaticUI\Drivers\JsonTranslationDriver json()
 * @method static \Ataurbdx\Translator\Modules\StaticUI\Drivers\FileTranslationDriver file()
 * @method static \Ataurbdx\Translator\Modules\CulturalLocale\Drivers\LocalTranslationDriver local()
 * @method static \Ataurbdx\Translator\Modules\AutomationAI\Drivers\AiTranslationDriver ai()
 * @method static mixed type(string $type, mixed $target = null)
 *
 * @see \Ataurbdx\Translator\Core\Translator
 */
class Translator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'translator';
    }
}
