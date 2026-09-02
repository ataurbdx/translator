<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('translator_engine.tables.dynamics', 'translator_engine_dynamics');

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->morphs('translatable'); // translatable_type, translatable_id
                $table->string('name', 100)->index(); // e.g. 'title', 'description'
                $table->json('value'); // e.g. {"en": "Electronics", "bn": "ইলেকট্রনিক্স"}
                $table->timestamps();

                $table->unique(['translatable_type', 'translatable_id', 'name'], 'ted_type_id_name_unique');
            });
        }
    }

    public function down(): void
    {
        $tableName = config('translator_engine.tables.dynamics', 'translator_engine_dynamics');
        Schema::dropIfExists($tableName);
    }
};
