<?php

namespace Ataurbdx\TranslatorEngine\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'translator-engine:install 
                            {--all : Install and run all migrations without prompting}';

    protected $description = 'Install and configure TranslatorEngine package';

    public function handle(): int
    {
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║             TranslatorEngine Installer                     ║');
        $this->info('║       The Master Universal Translation & Localizer           ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // 1. Publish Configuration
        $this->comment('1. Publishing config file...');
        $this->callSilent('vendor:publish', [
            '--provider' => 'Ataurbdx\TranslatorEngine\TranslationServiceProvider',
            '--tag'      => 'config',
        ]);
        $this->info('✔ Config published to config/translator_engine.php');

        // 2. Publish Migrations
        $this->comment('2. Publishing core migrations...');
        $this->callSilent('vendor:publish', [
            '--provider' => 'Ataurbdx\TranslatorEngine\TranslationServiceProvider',
            '--tag'      => 'migrations',
        ]);
        $this->info('✔ Core migrations published.');

        // 3. Ask to run migrations
        $runMigrations = $this->option('all') || $this->confirm('Do you want to run the core migrations now?', true);

        if ($runMigrations) {
            $this->call('migrate');
            $this->info('✔ Migrations completed successfully.');
        }

        $this->newLine();
        $this->info('🎉 TranslatorEngine successfully installed!');
        $this->line('You can now add <comment>use HasTranslatorEngine;</comment> to your Eloquent models.');
        $this->line('Call <comment>{{ te("button.add_to_cart") }}</comment> or <comment>{{ te_digits("2026") }}</comment> in Blade views.');

        return Command::SUCCESS;
    }
}
