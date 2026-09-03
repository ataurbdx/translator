<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('translator.tables.statics', 'translator_statics');

        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();                    // e.g. 'button.add_to_cart'
                $table->string('name')->unique();                   // e.g. 'Add to Cart' (strictly unique default name)
                $table->json('value')->nullable();                  // e.g. {"en": "Add to Cart", "bn": "কার্টে যোগ করুন"}
                $table->string('group', 100)->default('common')->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        $tableName = config('translator.tables.statics', 'translator_statics');
        Schema::dropIfExists($tableName);
    }
};
