<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unit_models', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token')->nullable();
            /**
             * Ini yang lama
             */
            // $table->unsignedBigInteger('unit_brand_id')->nullable();
            /**
             * ----------------
             */
            $table->foreignId('unit_brand_id')->nullable()->constrained('unit_brands')->nullOnDelete();
            $table->string('desc', 100)->nullable();
            $table->timestamps();
            /**
             * Ini yang lama
             */
            // $table->foreign('unit_brand_id')->references('id')->on('unit_brands')->onDelete('cascade');
            /**
             * ----------------
             */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_models');
    }
};
