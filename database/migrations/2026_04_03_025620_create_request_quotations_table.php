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
        Schema::create('request_quotations', function (Blueprint $table) {
            $table->id();
            /**
             * Ini yang lama
             */
            // $table->unsignedBigInteger('purchase_requisition_id')->nullable();
            // $table->unsignedBigInteger('client_vendor_id')->nullable();
            // $table->unsignedBigInteger('user_id')->nullable();
            /**
             * ----------------
             */
            $table->foreignId('purchase_requisition_id')->nullable()->constrained('purchase_requisitions')->nullOnDelete();
            $table->foreignId('client_vendor_id')->nullable()->constrained('client_vendors')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('request_token')->nullable();
            $table->text('real_name')->nullable();
            $table->text('quotation_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            /**
             * Ini yang lama
             */
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('purchase_requisition_id')->references('id')->on('purchase_requisitions')->onDelete('cascade');
            // $table->foreign('client_vendor_id')->references('id')->on('client_vendors')->onDelete('cascade');
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
        Schema::dropIfExists('request_quotations');
    }
};
