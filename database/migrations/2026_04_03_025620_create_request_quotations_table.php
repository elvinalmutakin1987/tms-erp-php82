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
            $table->unsignedBigInteger('purchase_requisition_id')->nullable();
            $table->unsignedBigInteger('client_vendor_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->uuid('request_token');
            $table->text('real_name')->nullable();
            $table->text('quotation_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('purchase_requisition_id')->references('id')->on('purchase_requisitions')->onDelete('cascade');
            $table->foreign('client_vendor_id')->references('id')->on('client_vendors')->onDelete('cascade');
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
