<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('translator_engine.tables.settings', 'translator_engine_settings');

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('key', 100)->unique();
                $table->text('value')->nullable();
                $table->string('type', 30)->default('string'); // string, boolean, json, encrypted
                $table->string('group', 50)->default('general')->index(); // ai, api, cache, general
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        $tableName = config('translator_engine.tables.settings', 'translator_engine_settings');
        Schema::dropIfExists($tableName);
    }
};
