<?php

namespace Ataurbdx\Translator;

use Illuminate\Support\ServiceProvider;
use Ataurbdx\Translator\Core\Translator;
use Ataurbdx\Translator\Console\InstallCommand;
use Ataurbdx\Translator\Console\MakeExternalCommand;
use Ataurbdx\Translator\Console\MakeHybridCommand;
use Ataurbdx\Translator\Console\MakeInlineCommand;
use Ataurbdx\Translator\Console\AiSyncCommand;
use Ataurbdx\Translator\Console\ExportLocalesCommand;
use Ataurbdx\Translator\Modules\HeadlessApi\Middleware\TranslatorLocaleMiddleware;
use Illuminate\Routing\Router;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Register package services in the container
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
                __DIR__ . '/Modules/Languages/Migrations/create_translator_languages_table.php.stub' => $this->getMigrationFileName('create_translator_languages_table', 0),
                __DIR__ . '/Modules/Settings/Migrations/create_translator_settings_table.php.stub' => $this->getMigrationFileName('create_translator_settings_table', 1),
                __DIR__ . '/Modules/DynamicModels/Migrations/create_translator_dynamics_table.php.stub' => $this->getMigrationFileName('create_translator_dynamics_table', 2),
                __DIR__ . '/Modules/StaticUI/Migrations/create_translator_statics_table.php.stub' => $this->getMigrationFileName('create_translator_statics_table', 3),
                __DIR__ . '/Modules/CulturalLocale/Migrations/create_translator_locales_table.php.stub' => $this->getMigrationFileName('create_translator_locales_table', 4),
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

        // 2. Register Headless API Routes if enabled
        if (config('translator.api.enabled', true)) {
            $this->loadRoutesFrom(__DIR__ . '/Modules/HeadlessApi/Routes/api.php');
        }

        // 3. Register Global Locale Middleware
        /** @var Router $router */
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', TranslatorLocaleMiddleware::class);
    }

    /**
     * Generate dynamic migration file name with current timestamp (if not already published)
     */
    protected function getMigrationFileName(string $migrationFileName, int $offsetSeconds = 0): string
    {
        $timestamp = date('Y_m_d_His', time() + $offsetSeconds);

        $existing = glob(database_path('migrations/*_' . $migrationFileName . '.php'));

        return !empty($existing) ? $existing[0] : database_path("migrations/{$timestamp}_{$migrationFileName}.php");
    }
}
