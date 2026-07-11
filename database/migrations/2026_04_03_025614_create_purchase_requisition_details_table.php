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
            $table->uuid('request_token')->nullable();
            $table->foreignId('purchase_requisition_id')->nullable()->constrained('purchase_requisitions')->nullOnDelete();
            $table->foreignId('maintenance_item_id')->nullable()->constrained('maintenance_items')->nullOnDelete();
            $table->foreignId('mro_item_id')->nullable()->constrained('mro_items')->nullOnDelete();
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
            $table->softDeletes();
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
