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
        Schema::create('purchase_requisition_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token');
            $table->unsignedBigInteger('purchase_requisition_id')->nullable();
            $table->unsignedBigInteger('maintenance_item_id')->nullable();
            $table->unsignedBigInteger('mro_item_id')->nullable();
            $table->text('description')->nullable();
            $table->string('uom', 30)->nullable();
            $table->decimal('qty', 16, 2)->nullable();
            $table->decimal('price', 16, 2)->nullable();
            $table->decimal('discount_item', 16, 2)->nullable();
            $table->decimal('tax', 16, 2)->nullable();
            $table->decimal('amount', 16, 2)->nullable();
            $table->date('received_at')->nullable();
            $table->string('received_by', 30)->nullable();
            $table->string('received_note')->nullable();
            $table->timestamps();
            $table->foreign('purchase_requisition_id')->references('id')->on('purchase_requisitions')->onDelete('cascade');
            $table->foreign('maintenance_item_id')->references('id')->on('maintenance_items')->onDelete('cascade');
            $table->foreign('mro_item_id')->references('id')->on('mro_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requisition_details');
    }
};
