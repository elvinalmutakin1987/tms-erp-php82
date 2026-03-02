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
        Schema::create('mechanical_inspection_damages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mechanical_inspection_id')->nullable();
            $table->text('damage')->nullable();
            $table->text('notes')->nullable();
            $table->string('sync_status', 2)->nullable();
            $table->timestamp('sync_at')->nullable();
            $table->foreign('mechanical_inspection_id')->references('id')->on('mechanical_inspections')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mechanical_inspection_damages');
    }
};
