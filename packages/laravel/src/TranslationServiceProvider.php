<?php

namespace Ataurbdx\Translator;

use Ataurbdx\Translator\Console\InstallCommand;
use Ataurbdx\Translator\Console\MakeExternalCommand;
use Ataurbdx\Translator\Console\MakeHybridCommand;
use Ataurbdx\Translator\Console\MakeInlineCommand;
use Ataurbdx\Translator\Console\AiSyncCommand;
use Ataurbdx\Translator\Console\ExportLocalesCommand;
use Ataurbdx\Translator\Core\Translator;
use Ataurbdx\Translator\Modules\HeadlessApi\Middleware\TranslatorLocaleMiddleware;
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
            __DIR__ . '/../config/translator.php',
            'translator'
        );

        // Bind main singleton
        $this->app->singleton('translator', function ($app) {
            return new Translator();
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
                __DIR__ . '/../config/translator.php' => config_path('translator.php'),
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
                ExportLocalesCommand::class,
            ]);
        }

        // 2. Load Core Migrations directly
        $this->loadMigrationsFrom(__DIR__ . '/Modules/Languages/Migrations');
        $this->loadMigrationsFrom(__DIR__ . '/Modules/Settings/Migrations');
        $this->loadMigrationsFrom(__DIR__ . '/Modules/DynamicModels/Migrations');
        $this->loadMigrationsFrom(__DIR__ . '/Modules/StaticUI/Migrations');
        $this->loadMigrationsFrom(__DIR__ . '/Modules/CulturalLocale/Migrations');

        // 3. Register Headless API Routes if enabled
        if (config('translator.api.enabled', true)) {
            $this->loadRoutesFrom(__DIR__ . '/Modules/HeadlessApi/Routes/api.php');
        }

        // 4. Register Global Locale Middleware
        /** @var Router $router */
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', TranslatorLocaleMiddleware::class);
        $router->pushMiddlewareToGroup('api', TranslatorLocaleMiddleware::class);
    }
}
