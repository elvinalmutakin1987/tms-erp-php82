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
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('p2h_id')->nullable();
            $table->unsignedBigInteger('mechanical_inspection_id')->nullable();
            $table->date('date')->nullable();
            $table->string('number', 30)->nullable();
            $table->string('maintenance_no', 30)->nullable();
            $table->string('mechanic', 30)->nullable();
            $table->unsignedBigInteger('checked_by')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->string('sync_status', 2)->nullable();
            $table->timestamp('sync_at')->nullable();
            $table->text('remarks')->nullable();
            $table->string('input_method', 20)->nullable();
            $table->text('status', 30)->nullable(); //Status nya > Open, Close
            $table->timestamps();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('checked_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('p2h_id')->references('id')->on('p2hs')->onDelete('cascade');
            $table->foreign('mechanical_inspection_id')->references('id')->on('mechanical_inspections')->onDelete('cascade');
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
