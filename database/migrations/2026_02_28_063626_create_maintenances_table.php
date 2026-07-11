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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token')->nullable();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('mechanical_inspection_id')->nullable()->constrained('mechanical_inspections')->nullOnDelete();
            $table->foreignId('p2h_id')->nullable()->constrained('p2hs')->nullOnDelete();
            $table->foreignId('client_vendor_id')->nullable()->constrained('client_vendors')->nullOnDelete();
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('date')->nullable();
            $table->string('number', 30)->nullable();
            $table->text('description')->nullable();
            $table->string('maintenance_no', 30)->nullable();
            $table->string('mechanic', 30)->nullable();
            $table->decimal('cost_total', 16, 2)->nullable();
            $table->decimal('hour_meter', 16, 2)->nullable();
            $table->decimal('km_hm', 16, 2)->nullable();
            $table->timestamp('start')->nullable();
            $table->timestamp('finish')->nullable();
            $table->time('work_duration')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->string('sync_status', 2)->nullable();
            $table->timestamp('sync_at')->nullable();
            $table->text('remarks')->nullable();
            $table->text('type')->nullable();
            $table->string('input_method', 20)->nullable();
            $table->text('status')->nullable(); //Status nya > Open, Close
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
