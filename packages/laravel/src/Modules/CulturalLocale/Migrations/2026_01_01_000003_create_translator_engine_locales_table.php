<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('translator_engine.tables.locales', 'translator_engine_locales');

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('code', 10)->unique();       // e.g. 'bn', 'en', 'ar'
                $table->string('name', 100);                // 'Bengali'
                $table->string('native_name', 100);         // 'বাংলা'
                $table->string('direction', 5)->default('ltr'); // 'ltr' or 'rtl'
                
                // Formatting rules
                $table->string('decimal_sep', 5)->default('.');
                $table->string('thousand_sep', 5)->default(',');
                $table->string('group_style', 20)->default('standard'); // 'standard' or 'south_asian'
                $table->string('currency_code', 10)->default('BDT');
                $table->string('currency_symbol', 10)->default('৳');
                $table->string('currency_word_suffix', 50)->nullable(); // 'টাকা মাত্র'

                // JSON rule blocks
                $table->json('digits')->nullable();        // ["০","১",...]
                $table->json('months')->nullable();        // {"1": "জানুয়ারি", ...}
                $table->json('days')->nullable();          // {"sat": "শনিবার", ...}
                $table->json('extra_config')->nullable();

                $table->boolean('is_active')->default(true);
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        $tableName = config('translator_engine.tables.locales', 'translator_engine_locales');
        Schema::dropIfExists($tableName);
    }
};
