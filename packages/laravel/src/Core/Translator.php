<?php

namespace Ataurbdx\Translator\Core;

use Ataurbdx\Translator\Modules\DynamicModels\Drivers\InlineTranslationDriver;
use Ataurbdx\Translator\Modules\DynamicModels\Drivers\InternalTranslationDriver;
use Ataurbdx\Translator\Modules\DynamicModels\Drivers\ExternalTranslationDriver;
use Ataurbdx\Translator\Modules\DynamicModels\Drivers\HybridTranslationDriver;
use Ataurbdx\Translator\Modules\StaticUI\Drivers\StaticTranslationDriver;
use Ataurbdx\Translator\Modules\StaticUI\Drivers\JsonTranslationDriver;
use Ataurbdx\Translator\Modules\StaticUI\Drivers\FileTranslationDriver;
use Ataurbdx\Translator\Modules\CulturalLocale\Drivers\LocalTranslationDriver;
use Ataurbdx\Translator\Modules\AutomationAI\Drivers\AiTranslationDriver;
use Illuminate\Database\Eloquent\Model;

class Translator
{
    protected static ?StaticTranslationDriver $staticDriver = null;
    protected static ?JsonTranslationDriver $jsonDriver = null;
    protected static ?FileTranslationDriver $fileDriver = null;
    protected static ?LocalTranslationDriver $localDriver = null;
    protected static ?AiTranslationDriver $aiDriver = null;

    /**
     * Resolve by type name and optional model target
     */
    public function type(string $type, mixed $target = null)
    {
        $resolvedTarget = is_string($target) && class_exists($target) ? new $target : $target;

        return match ($type) {
            'inline'   => $this->inline($resolvedTarget),
            'internal' => $this->internal($resolvedTarget),
            'external' => $this->external($resolvedTarget),
            'hybrid'   => $this->hybrid($resolvedTarget),
            'static'   => $this->static(),
            'json'     => $this->json(),
            'file'     => $this->file(),
            'local'    => $this->local(),
            'ai'       => $this->ai(),
            default    => $this->static(),
        };
    }

    public function inline(?Model $model = null): InlineTranslationDriver
    {
        return new InlineTranslationDriver($model);
    }

    public function internal(?Model $model = null): InternalTranslationDriver
    {
        return new InternalTranslationDriver($model);
    }

    public function external(?Model $model = null): ExternalTranslationDriver
    {
        return new ExternalTranslationDriver($model);
    }

    public function hybrid(?Model $model = null): HybridTranslationDriver
    {
        return new HybridTranslationDriver($model);
    }

    public function static(): StaticTranslationDriver
    {
        if (static::$staticDriver === null) {
            static::$staticDriver = new StaticTranslationDriver();
        }
        return static::$staticDriver;
    }

    public function json(): JsonTranslationDriver
    {
        if (static::$jsonDriver === null) {
            static::$jsonDriver = new JsonTranslationDriver();
        }
        return static::$jsonDriver;
    }

    public function file(): FileTranslationDriver
    {
        if (static::$fileDriver === null) {
            static::$fileDriver = new FileTranslationDriver();
        }
        return static::$fileDriver;
    }

    public function local(): LocalTranslationDriver
    {
        if (static::$localDriver === null) {
            static::$localDriver = new LocalTranslationDriver();
        }
        return static::$localDriver;
    }

    public function ai(): AiTranslationDriver
    {
        if (static::$aiDriver === null) {
            static::$aiDriver = new AiTranslationDriver();
        }
        return static::$aiDriver;
    }
}
