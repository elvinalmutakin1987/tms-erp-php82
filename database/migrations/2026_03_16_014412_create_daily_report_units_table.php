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
        Schema::create('daily_report_units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('daily_report_detail_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('item', 30)->nullable();
            $table->string('uom', 30)->nullable();
            $table->decimal('value', 16, 2)->nullable();
            $table->timestamps();
            $table->foreign('daily_report_detail_id')->references('id')->on('daily_report_details')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_units');
    }
};
