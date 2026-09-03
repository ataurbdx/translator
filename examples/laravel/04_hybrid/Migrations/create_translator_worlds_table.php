<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Generated via: php artisan translator:make:hybrid worlds
     * Shared across all models in the world/geo domain (Country, Division, City, SubCity).
     */
    public function up(): void
    {
        Schema::create('translator_worlds', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 50)->index(); // 'country', 'city', etc.
            $table->unsignedBigInteger('entity_id')->index();
            $table->string('locale', 10)->index();
            $table->string('field', 100)->index();
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['entity_type', 'entity_id', 'locale', 'field'], 'worlds_unique_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translator_worlds');
    }
};
