<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Generated via: php artisan translator:make:external listings
     * Dedicated translation table with composite indexing for millions of rows.
     */
    public function up(): void
    {
        Schema::create('translator_listings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listing_id')->index();
            $table->string('locale', 10)->index();

            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->text('address')->nullable();

            $table->timestamps();

            $table->unique(['listing_id', 'locale'], 'listing_locale_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translator_listings');
    }
};
