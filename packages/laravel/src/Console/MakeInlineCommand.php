<?php

namespace Ataurbdx\TranslatorEngine\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeInlineCommand extends Command
{
    protected $signature = 'translator-engine:make:inline 
                            {table : The table name to modify} 
                            {column : The column to convert to JSON}';

    protected $description = 'Generate a migration to convert an existing column into a translation JSON column';

    public function handle(): int
    {
        $table = trim($this->argument('table'));
        $column = trim($this->argument('column'));

        $this->info("Creating migration to convert `{$column}` in `{$table}` to JSON...");

        $timestamp = date('Y_m_d_His');
        $fileName = "{$timestamp}_convert_{$column}_to_json_in_{$table}_table.php";
        $filePath = database_path("migrations/{$fileName}");

        $stub = "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('{$table}', function (Blueprint \$table) {
            \$table->json('{$column}')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('{$table}', function (Blueprint \$table) {
            \$table->string('{$column}')->nullable()->change();
        });
    }
};
";

        File::put($filePath, $stub);
        $this->info("✔ Inline JSON migration created: database/migrations/{$fileName}");

        return Command::SUCCESS;
    }
}
