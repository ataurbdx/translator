<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TYPE 2 (Internal): Notice that the main table remains 100% CLEAN!
     * No JSON columns or language columns.
     * All translations are stored in the shared polymorphic table: `translator_dynamics`.
     */
    public function up(): void
    {
        Schema::create('example_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Default / fallback name in English
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('example_categories');
    }
};
