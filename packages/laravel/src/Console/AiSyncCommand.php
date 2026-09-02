<?php

namespace Ataurbdx\TranslatorEngine\Console;

use Ataurbdx\TranslatorEngine\Facades\TranslatorEngine;
use Ataurbdx\TranslatorEngine\Modules\StaticUI\Models\TranslatorEngineStatic;
use Illuminate\Console\Command;

class AiSyncCommand extends Command
{
    protected $signature = 'translator-engine:ai-sync 
                            {--to=bn : Target locale} 
                            {--from=en : Source locale} 
                            {--group= : Translate a specific group only}';

    protected $description = 'Automatically translate missing static UI keys using configured AI driver';

    public function handle(): int
    {
        $to = $this->option('to');
        $from = $this->option('from');
        $group = $this->option('group');

        $this->info("Scanning untranslated keys from '{$from}' to '{$to}' using AI...");

        $query = TranslatorEngineStatic::query();
        if ($group) {
            $query->where('group', $group);
        }

        $records = $query->get();
        $count = 0;

        foreach ($records as $record) {
            $currentVal = $record->value[$to] ?? null;

            if (empty($currentVal)) {
                $sourceText = $record->value[$from] ?? $record->name ?? $record->key;
                $this->line("Translating: <comment>{$record->key}</comment> -> '{$sourceText}'");

                $translated = TranslatorEngine::ai()->translate($sourceText, $to, $from);

                if (!empty($translated) && $translated !== $sourceText) {
                    $values = $record->value ?? [];
                    $values[$to] = $translated;
                    $record->value = $values;
                    $record->save();

                    TranslatorEngine::static()->forget($record->key);
                    $this->info("✔ Saved: '{$translated}'");
                    $count++;
                }
            }
        }

        $this->newLine();
        $this->info("🎉 AI Sync completed. {$count} keys translated.");

        return Command::SUCCESS;
    }
}
