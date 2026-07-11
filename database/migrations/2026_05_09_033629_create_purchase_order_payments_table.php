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
        Schema::create('purchase_order_payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('client_vendor_id')->nullable()->constrained('client_vendors')->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('bank')->nullable();
            $table->string('bank_account', 50)->nullable();
            $table->string('bank_sender')->nullable();
            $table->string('bank_account_sender', 50)->nullable();
            $table->string('payment_no', 30)->nullable();
            $table->string('type', 30)->nullable(); //Down Payment, Balance Payment
            $table->string('ref_no', 30)->nullable();
            $table->date('date')->nullable();
            $table->text('notes')->nullable();
            $table->text('status')->nullable();
            $table->decimal('total', 16, 2)->nullable();
            $table->string('sync_status', 2)->nullable();
            $table->timestamp('sync_at')->nullable();
            $table->string('input_method', 20)->nullable();
            $table->text('payment_path')->nullable();
            $table->text('real_name')->nullable();
            $table->string('cancel_notes')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_payments');
    }
};
