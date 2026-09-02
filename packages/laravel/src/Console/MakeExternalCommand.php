<?php

namespace Ataurbdx\TranslatorEngine\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class MakeExternalCommand extends Command
{
    protected $signature = 'translator-engine:make:external {table : Base table name (e.g. listings)}';

    protected $description = 'Generate a dedicated translation table migration for a high-traffic model';

    public function handle(): int
    {
        $baseTable = trim($this->argument('table'));
        $prefix = config('translator_engine.tables.prefix', 'translator_engine_');
        $targetTable = $prefix . $baseTable;

        $this->info("Creating dedicated translation migration for: {$baseTable} -> {$targetTable}");

        // Read columns from database if table exists
        $columns = [];
        if (Schema::hasTable($baseTable)) {
            $allCols = Schema::getColumnListing($baseTable);
            $exclude = ['id', 'created_at', 'updated_at', 'deleted_at', 'status', 'is_active'];
            $candidates = array_diff($allCols, $exclude);

            $selected = $this->choice(
                'Select translatable columns (separate multiple by commas, or press Enter for defaults):',
                array_values($candidates),
                null,
                null,
                true
            );
            $columns = (array) $selected;
        }

        if (empty($columns)) {
            $input = $this->ask('Enter translatable column names separated by commas (e.g. title, description, address):', 'title, description');
            $columns = array_map('trim', explode(',', $input));
        }

        $singular = rtrim($baseTable, 's');
        $foreignKey = $singular . '_id';

        $columnDefinitions = '';
        foreach ($columns as $col) {
            $columnDefinitions .= "            \$table->text('{$col}')->nullable();\n";
        }

        $timestamp = date('Y_m_d_His');
        $className = 'Create' . str_replace(' ', '', ucwords(str_replace('_', ' ', $targetTable))) . 'Table';
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
            \$table->unsignedBigInteger('{$foreignKey}')->index();
            \$table->string('locale', 10)->index();

{$columnDefinitions}
            \$table->timestamps();

            \$table->unique(['{$foreignKey}', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$targetTable}');
    }
};
";

        File::put($filePath, $stub);
        $this->info("✔ Migration created: database/migrations/{$fileName}");

        return Command::SUCCESS;
    }
}
