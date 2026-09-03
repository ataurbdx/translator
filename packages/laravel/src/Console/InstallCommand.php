<?php

namespace Ataurbdx\Translator\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'translator:install 
                            {--type= : Translation type to install: core, internal, static, local, all}
                            {--all : Install all tables without prompting}';

    protected $description = 'Install and configure Translator package with on-demand tables';

    public function handle(): int
    {
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║                  Translator Installer                        ║');
        $this->info('║       The Master Universal Translation & Localizer           ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // 1. Publish Configuration File
        $this->comment('1. Publishing config file...');
        $this->callSilent('vendor:publish', [
            '--provider' => 'Ataurbdx\Translator\TranslationServiceProvider',
            '--tag'      => 'config',
        ]);
        $this->info('✔ Config published to config/translator.php');

        // 2. Determine Which Types to Install
        $types = $this->resolveTypes();

        $this->newLine();
        $this->comment('2. Publishing selected migrations: [' . implode(', ', $types) . ']...');

        // Always publish core (languages + settings)
        $this->callSilent('vendor:publish', [
            '--provider' => 'Ataurbdx\Translator\TranslationServiceProvider',
            '--tag'      => 'migrations-core',
        ]);
        $this->line('  • translator_languages & translator_settings (Core)');

        if (in_array('internal', $types)) {
            $this->callSilent('vendor:publish', [
                '--provider' => 'Ataurbdx\Translator\TranslationServiceProvider',
                '--tag'      => 'migrations-internal',
            ]);
            $this->line('  • translator_dynamics (Polymorphic Model Translations)');
        }

        if (in_array('static', $types)) {
            $this->callSilent('vendor:publish', [
                '--provider' => 'Ataurbdx\Translator\TranslationServiceProvider',
                '--tag'      => 'migrations-static',
            ]);
            $this->line('  • translator_statics (Static UI Strings, Buttons & Menus)');
        }

        if (in_array('local', $types)) {
            $this->callSilent('vendor:publish', [
                '--provider' => 'Ataurbdx\Translator\TranslationServiceProvider',
                '--tag'      => 'migrations-local',
            ]);
            $this->line('  • translator_locales (Cultural Rules, Bengali Digits, Calendar)');
        }

        $this->info('✔ Selected migrations published with real-time timestamps.');

        // 3. Prompt to Run Migrations
        $this->newLine();
        $runMigrations = $this->option('all') || $this->confirm('Do you want to run these migrations now?', true);

        if ($runMigrations) {
            $this->call('migrate');
            $this->info('✔ Migrations executed successfully.');
        }

        $this->newLine();
        $this->info('🎉 Translator setup completed!');
        $this->line('• In Models: <comment>use HasTranslator;</comment>');
        $this->line('• In Blade:  <comment>{{ translate("button.save") }}</comment> or <comment>{{ translate("2026", type: "digits") }}</comment>');
        $this->line('• To add more types later: <comment>php artisan translator:install --type=static</comment>');

        return Command::SUCCESS;
    }

    /**
     * Resolve user selection for translation types
     */
    protected function resolveTypes(): array
    {
        if ($this->option('all')) {
            return ['core', 'internal', 'static', 'local'];
        }

        $typeOption = $this->option('type');
        if (!empty($typeOption)) {
            $inputTypes = array_map('trim', explode(',', strtolower($typeOption)));

            if (in_array('all', $inputTypes)) {
                return ['core', 'internal', 'static', 'local'];
            }

            $types = ['core'];
            foreach ($inputTypes as $t) {
                if (in_array($t, ['internal', 'static', 'local'])) {
                    $types[] = $t;
                }
            }
            return array_unique($types);
        }

        // Interactive Prompt
        $choice = $this->choice(
            'Which translation features do you want to install?',
            [
                'all'      => 'Full Suite (All tables: Languages, Settings, Dynamics, Statics, Locales)',
                'core'     => 'Core Only (Languages + Settings — for Inline JSON, File, JSON or AI)',
                'internal' => 'Internal Dynamic Models (Core + translator_dynamics table)',
                'static'   => 'Static UI Translations (Core + translator_statics table)',
                'local'    => 'Cultural Locales & Numbers (Core + translator_locales table)',
                'custom'   => 'Custom Selection (Choose which features to enable)',
            ],
            'all'
        );

        if ($choice === 'all') {
            return ['core', 'internal', 'static', 'local'];
        }

        if ($choice === 'core') {
            return ['core'];
        }

        if ($choice === 'internal') {
            return ['core', 'internal'];
        }

        if ($choice === 'static') {
            return ['core', 'static'];
        }

        if ($choice === 'local') {
            return ['core', 'local'];
        }

        // Custom Multi-Select
        $selected = $this->choice(
            'Select the features to install (separate multiple numbers with commas):',
            [
                'internal' => 'translator_dynamics (Polymorphic Eloquent models)',
                'static'   => 'translator_statics (UI buttons, menus, labels)',
                'local'    => 'translator_locales (Cultural rules, Bengali digits, currency)',
            ],
            null,
            null,
            true
        );

        return array_unique(array_merge(['core'], (array) $selected));
    }
}
