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
        Schema::create('unit_rates', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token')->nullable();
            /**
             * Ini yang lama
             */
            // $table->unsignedBigInteger('contract_id')->nullable();
            // $table->unsignedBigInteger('unit_id')->nullable();
            /**
             * ----------------
             */
            $table->foreignId('contract_id')->nullable()->constrained('contracts')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->decimal('rate', 16, 2)->nullable();
            $table->decimal('target', 16, 2)->nullable();
            $table->timestamps();
            $table->unique(['contract_id', 'unit_id']);
            /**
             * Ini yang lama
             */
            // $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
            // $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
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
        Schema::dropIfExists('unit_rates');
    }
};
