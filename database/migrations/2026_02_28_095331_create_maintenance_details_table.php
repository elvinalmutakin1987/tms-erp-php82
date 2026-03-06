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
            $table->unsignedBigInteger('maintenance_id')->nullable();
            $table->unsignedBigInteger('maintenance_item_id')->nullable();
            $table->unsignedBigInteger('mro_item_id')->nullable();
            $table->text('notes')->nullable();
            $table->string('action', 30)->nullable(); //Repair / Replace / Washing / Add / Flushing
            $table->string('sync_status', 2)->nullable();
            $table->decimal('cost', 16, 2)->nullable();
            $table->timestamp('sync_at')->nullable();
            $table->timestamps();
            $table->foreign('maintenance_item_id')->references('id')->on('maintenance_items')->onDelete('cascade');
            $table->foreign('mro_item_id')->references('id')->on('mro_items')->onDelete('cascade');
            $table->foreign('maintenance_id')->references('id')->on('maintenances')->onDelete('cascade');
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
