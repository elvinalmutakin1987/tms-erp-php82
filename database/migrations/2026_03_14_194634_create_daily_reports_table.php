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
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('client_vendor_id')->nullable();
            $table->string('number', 30)->nullable();
            $table->string('report_no', 30)->nullable();
            $table->string('type', 30)->nullable();
            $table->date('date')->nullable();
            $table->string('shift', 20)->nullable();
            $table->decimal('km_start', 16, 2)->nullable();
            $table->decimal('km_finish', 16, 2)->nullable();
            $table->decimal('km_total', 16, 2)->nullable();
            $table->string('operator', 30)->nullable();
            $table->string('helper', 30)->nullable();
            $table->decimal('load', 16, 2)->nullable();
            $table->timestamp('down_time')->nullable();
            $table->timestamp('ready_for_use')->nullable();
            $table->decimal('refule_hm', 16, 2)->nullable();
            $table->decimal('refule_liter', 16, 2)->nullable();
            $table->string('refule_type', 20)->nullable();
            $table->decimal('supply_fuel', 16, 2)->nullable();
            $table->decimal('supply_water', 16, 2)->nullable();
            $table->time('operation')->nullable();
            $table->time('stand_by')->nullable();
            $table->decimal('hour_meter', 16, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->time('duration_trip')->nullable();
            $table->string('input_method', 20)->nullable();
            $table->text('status')->nullable(); //Status nya > Open, Close
            $table->unsignedBigInteger('checked_by')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->string('sync_status', 2)->nullable();
            $table->timestamp('sync_at')->nullable();
            $table->timestamps();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('client_vendor_id')->references('id')->on('client_vendors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
