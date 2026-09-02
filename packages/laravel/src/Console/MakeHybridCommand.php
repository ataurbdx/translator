<?php

namespace Ataurbdx\TranslatorEngine\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeHybridCommand extends Command
{
    protected $signature = 'translator-engine:make:hybrid {domain : Domain cluster name (e.g. worlds, catalog)}';

    protected $description = 'Generate a grouped domain translation table migration';

    public function handle(): int
    {
        $domain = trim($this->argument('domain'));
        $prefix = config('translator_engine.tables.prefix', 'translator_engine_');
        $targetTable = $prefix . $domain;

        $this->info("Creating grouped hybrid domain migration: {$targetTable}");

        $timestamp = date('Y_m_d_His');
        $fileName = "{$timestamp}_create_{$targetTable}_table.php";
        $filePath = database_path("migrations/{$fileName}");

        $stub = "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$targetTable}', function (Blueprint \$table) {
            \$table->id();
            \$table->string('entity_type', 50)->index(); // e.g. 'country', 'city', 'department'
            \$table->unsignedBigInteger('entity_id')->index();
            \$table->string('locale', 10)->index();
            \$table->string('field', 100)->index();       // e.g. 'name', 'native'
            \$table->text('value')->nullable();
            \$table->timestamps();

            \$table->unique(['entity_type', 'entity_id', 'locale', 'field'], '{$domain}_unique_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$targetTable}');
    }
};
";

        File::put($filePath, $stub);
        $this->info("✔ Hybrid migration created: database/migrations/{$fileName}");
        $this->line("Any entity model in this domain can now use: <comment>protected \$translatorEngineTable = '{$targetTable}';</comment>");

        return Command::SUCCESS;
    }
}
