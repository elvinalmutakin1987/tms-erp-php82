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
        Schema::create('maintenance_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token')->nullable();
            /**
             * Ini yang lama
             */
            // $table->unsignedBigInteger('maintenance_id')->nullable();
            // $table->unsignedBigInteger('maintenance_item_id')->nullable();
            // $table->unsignedBigInteger('mro_item_id')->nullable();
            /**
             * ----------------
             */
            $table->foreignId('maintenance_id')->nullable()->constrained('maintenances')->nullOnDelete();
            $table->foreignId('maintenance_item_id')->nullable()->constrained('maintenance_items')->nullOnDelete();
            $table->foreignId('mro_item_id')->nullable()->constrained('mro_items')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('action', 30)->nullable(); //Repair / Replace / Washing / Add / Flushing
            $table->string('sync_status', 2)->nullable();
            $table->decimal('cost', 16, 2)->nullable();
            $table->timestamp('sync_at')->nullable();
            $table->timestamps();
            /**
             * Ini yang lama
             */
            // $table->foreign('maintenance_item_id')->references('id')->on('maintenance_items')->onDelete('cascade');
            // $table->foreign('mro_item_id')->references('id')->on('mro_items')->onDelete('cascade');
            // $table->foreign('maintenance_id')->references('id')->on('maintenances')->onDelete('cascade');
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
        Schema::dropIfExists('maintenance_details');
    }
};
