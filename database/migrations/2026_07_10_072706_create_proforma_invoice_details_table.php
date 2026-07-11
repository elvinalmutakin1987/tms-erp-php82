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
        Schema::create('proforma_invoice_details', function (Blueprint $table) {
            $table->uuid('request_token')->nullable();
            $table->foreignId('proforma_invoice_id')->nullable()->constrained('proforma_invoices')->nullOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained('contracts')->nullOnDelete();
            $table->foreignId('contract_rate_id')->nullable()->constrained('contract_rates')->nullOnDelete();
            $table->foreignId('contract_fmf_id')->nullable()->constrained('contract_fmfs')->nullOnDelete();
            $table->foreignId('unit_target_id')->nullable()->constrained('unit_targets')->nullOnDelete();
            $table->foreignId('service_item_id')->nullable()->constrained('service_items')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('item_no')->nullable();
            $table->string('service_item')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('rate', 16, 2)->nullable();
            $table->string('type')->nullable();
            $table->string('year')->nullable();
            $table->decimal('value', 16, 2)->nullable();
            $table->decimal('target', 16, 2)->nullable();
            $table->decimal('price', 16, 2)->nullable();
            $table->decimal('qty', 16, 2)->nullable();
            $table->decimal('amount', 16, 2)->nullable();
            $table->decimal('ptd_qty', 16, 2)->nullable();
            $table->decimal('ptd_amount', 16, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proforma_invoice_details');
    }
};
