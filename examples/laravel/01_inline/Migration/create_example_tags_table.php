<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TYPE 1 (Inline): Stores translations as JSON in the model's own table column.
     * Zero extra tables, zero database joins. Maximum read speed.
     */
    public function up(): void
    {
        Schema::create('example_tags', function (Blueprint $table) {
            $table->id();
            $table->json('name'); // {"en": "Electronics", "bn": "ইলেকট্রনিক্স", "es": "Electrónica"}
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('example_tags');
    }
};
