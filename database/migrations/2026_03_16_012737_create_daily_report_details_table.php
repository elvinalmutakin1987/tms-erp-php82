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
        Schema::create('daily_report_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token');
            $table->unsignedBigInteger('daily_report_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('item', 30)->nullable();
            $table->string('uom_1', 30)->nullable();
            $table->decimal('value_1', 16, 2)->nullable();
            $table->string('uom_2', 30)->nullable();
            $table->decimal('value_2', 16, 2)->nullable();
            $table->timestamps();
            $table->foreign('daily_report_id')->references('id')->on('daily_reports')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_details');
    }
};
