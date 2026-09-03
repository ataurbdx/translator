<?php

namespace Ataurbdx\Translator\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'translator:install 
                            {--all : Install and run all migrations without prompting}';

    protected $description = 'Install and configure Translator package';

    public function handle(): int
    {
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║                  Translator Installer                        ║');
        $this->info('║       The Master Universal Translation & Localizer           ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // 1. Publish Configuration
        $this->comment('1. Publishing config file...');
        $this->callSilent('vendor:publish', [
            '--provider' => 'Ataurbdx\Translator\TranslationServiceProvider',
            '--tag'      => 'config',
        ]);
        $this->info('✔ Config published to config/translator.php');

        // 2. Publish Migrations
        $this->comment('2. Publishing core migrations...');
        $this->callSilent('vendor:publish', [
            '--provider' => 'Ataurbdx\Translator\TranslationServiceProvider',
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
        $this->info('🎉 Translator successfully installed!');
        $this->line('You can now add <comment>use HasTranslator;</comment> to your Eloquent models.');
        $this->line('Call <comment>{{ translate("button.add_to_cart") }}</comment> or <comment>{{ translate("2026", type: "digits") }}</comment> in Blade views.');

        return Command::SUCCESS;
    }
}
