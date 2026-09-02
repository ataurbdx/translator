<?php

namespace Ataurbdx\TranslatorEngine;

use Ataurbdx\TranslatorEngine\Console\InstallCommand;
use Ataurbdx\TranslatorEngine\Console\MakeExternalCommand;
use Ataurbdx\TranslatorEngine\Console\MakeHybridCommand;
use Ataurbdx\TranslatorEngine\Console\MakeInlineCommand;
use Ataurbdx\TranslatorEngine\Console\AiSyncCommand;
use Ataurbdx\TranslatorEngine\Core\TranslatorEngine;
use Ataurbdx\TranslatorEngine\Modules\HeadlessApi\Middleware\TranslatorEngineLocaleMiddleware;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container
     */
    public function register(): void
    {
        // Merge package configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/translator_engine.php',
            'translator_engine'
        );

        // Bind main singleton
        $this->app->singleton('translator-engine', function ($app) {
            return new TranslatorEngine();
        });
    }

    /**
     * Bootstrap package services
     */
    public function boot(): void
    {
        // 1. Register Publishable Assets
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/translator_engine.php' => config_path('translator_engine.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/Modules/Languages/Migrations/' => database_path('migrations'),
                __DIR__ . '/Modules/Settings/Migrations/' => database_path('migrations'),
                __DIR__ . '/Modules/DynamicModels/Migrations/' => database_path('migrations'),
                __DIR__ . '/Modules/StaticUI/Migrations/' => database_path('migrations'),
                __DIR__ . '/Modules/CulturalLocale/Migrations/' => database_path('migrations'),
            ], 'migrations');

            // Register artisan commands
            $this->commands([
                InstallCommand::class,
                MakeExternalCommand::class,
                MakeHybridCommand::class,
                MakeInlineCommand::class,
                AiSyncCommand::class,
            ]);
        }

        // 2. Load Core Migrations directly
        $this->loadMigrationsFrom(__DIR__ . '/Modules/Languages/Migrations');
        $this->loadMigrationsFrom(__DIR__ . '/Modules/Settings/Migrations');
        $this->loadMigrationsFrom(__DIR__ . '/Modules/DynamicModels/Migrations');
        $this->loadMigrationsFrom(__DIR__ . '/Modules/StaticUI/Migrations');
        $this->loadMigrationsFrom(__DIR__ . '/Modules/CulturalLocale/Migrations');

        // 3. Register Headless API Routes if enabled
        if (config('translator_engine.api.enabled', true)) {
            $this->loadRoutesFrom(__DIR__ . '/Modules/HeadlessApi/Routes/api.php');
        }

        // 4. Register Global Locale Middleware
        /** @var Router $router */
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', TranslatorEngineLocaleMiddleware::class);
        $router->pushMiddlewareToGroup('api', TranslatorEngineLocaleMiddleware::class);
    }
}
