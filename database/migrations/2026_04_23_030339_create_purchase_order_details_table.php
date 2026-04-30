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
        Schema::create('purchase_order_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token');
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('maintenance_item_id')->nullable();
            $table->unsignedBigInteger('mro_item_id')->nullable();
            $table->text('description')->nullable();
            $table->string('type', 30)->nullable(); //Good, Service
            $table->string('uom', 30)->nullable();
            $table->decimal('qty', 16, 2)->nullable();
            $table->decimal('price', 16, 2)->nullable();
            $table->decimal('discount_item', 16, 2)->nullable();
            $table->decimal('tax', 16, 2)->nullable();
            $table->decimal('amount', 16, 2)->nullable();
            $table->string('part_number')->nullable();
            $table->string('desc_vendor')->nullable();
            $table->timestamps();
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('maintenance_item_id')->references('id')->on('maintenance_items')->onDelete('cascade');
            $table->foreign('mro_item_id')->references('id')->on('mro_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_details');
    }
};
