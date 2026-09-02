<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('translator_engine.tables.languages', 'translator_engine_languages');

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);                    // e.g. 'English', 'Bangla', 'Hindi'
                $table->string('code', 10)->unique();           // e.g. 'en', 'bn', 'hn'
                $table->text('flag')->nullable();               // Full flexibility: CSS class, SVG tag, <img> HTML, image URL, or emoji
                $table->boolean('is_default')->default(false);  // 0 or 1
                $table->boolean('status')->default(true);       // 1 = active, 0 = inactive
                $table->integer('sort_order')->default(0);      // display order in UI switchers
                $table->timestamps();
                $table->softDeletes();                          // deleted_at
            });
        }
    }

    public function down(): void
    {
        $tableName = config('translator_engine.tables.languages', 'translator_engine_languages');
        Schema::dropIfExists($tableName);
    }
};
