<?php

namespace Ataurbdx\Translator\Console;

use Ataurbdx\Translator\Modules\CulturalLocale\Models\TranslatorLocale;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportLocalesCommand extends Command
{
    protected $signature = 'translator:export-locales
                            {--locale= : Export a specific locale only (e.g. --locale=bn)}
                            {--force   : Overwrite existing exported files}';

    protected $description = 'Export cultural locale rules from database to resources/lang/locales/{code}.json files';

    public function handle(): int
    {
        $outputDir = resource_path('lang/locales');

        // Ensure output directory exists
        if (!File::isDirectory($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
            $this->line("📁 Created directory: {$outputDir}");
        }

        // Build query
        $query = TranslatorLocale::query();

        if ($locale = $this->option('locale')) {
            $query->where('code', $locale);
        }

        $locales = $query->get();

        if ($locales->isEmpty()) {
            $this->warn('⚠️  No locale records found in database. Have you seeded translator_locales?');
            return Command::FAILURE;
        }

        $exported = 0;

        foreach ($locales as $record) {
            $filePath = "{$outputDir}/{$record->code}.json";

            if (File::exists($filePath) && !$this->option('force')) {
                $this->line("⏭️  Skipping <comment>{$record->code}</comment> (file already exists, use --force to overwrite)");
                continue;
            }

            $data = [
                'code'            => $record->code,
                'name'            => $record->name,
                'native_name'     => $record->native_name,
                'direction'       => $record->direction,
                'decimal_sep'     => $record->decimal_sep,
                'thousand_sep'    => $record->thousand_sep,
                'group_style'     => $record->group_style,
                'currency_code'   => $record->currency_code,
                'currency_symbol' => $record->currency_symbol,
                'currency_suffix' => $record->currency_word_suffix,
                'digits'          => $record->digits ?? [],
                'months'          => $record->months ?? [],
                'days'            => $record->days ?? [],
            ];

            File::put($filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $this->info("✅ Exported <comment>{$record->code}</comment> → {$filePath}");
            $exported++;
        }

        $this->newLine();
        $this->info("🎉 Exported {$exported} locale(s) to <comment>{$outputDir}</comment>");
        $this->line('These files serve as fallback when the database is unavailable.');

        return Command::SUCCESS;
    }
}
