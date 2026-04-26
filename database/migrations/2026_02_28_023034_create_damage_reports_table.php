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
        Schema::create('damage_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->date('date')->nullable();
            $table->string('number', 30)->nullable();
            $table->string('damage_no', 30)->nullable();
            $table->string('driver', 30)->nullable();
            $table->string('mechanic', 30)->nullable();
            $table->text('description')->nullable();
            $table->text('action')->nullable();
            $table->string('status', 30)->nullable();
            $table->string('sync_status', 2)->nullable();
            $table->timestamp('sync_at')->nullable();
            $table->timestamps();
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damage_reports');
    }
};
