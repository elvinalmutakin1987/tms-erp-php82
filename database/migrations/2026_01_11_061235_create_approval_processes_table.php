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
        Schema::create('approval_processes', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token')->nullable();
            /**
             * Ini yang lama
             */
            // $table->unsignedBigInteger('approval_flow_id')->nullable();
            // $table->unsignedBigInteger('approval_step_id')->nullable();
            // $table->unsignedBigInteger('approvable_id')->nullable();
            /**
             * ----------------
             */
            $table->foreignId('approval_flow_id')->nullable()->constrained('approval_flows')->nullOnDelete();
            $table->foreignId('approval_step_id')->nullable()->constrained('approval_steps')->nullOnDelete();
            $table->unsignedBigInteger('approvable_id')->nullable();
            $table->string('action', 30)->nullable();
            $table->string('comment')->nullable();
            $table->timestamps();
            /**
             * Ini yang lama
             */
            // $table->foreign('approval_flow_id')->references('id')->on('approval_flows')->onDelete('cascade');
            // $table->foreign('approval_step_id')->references('id')->on('approval_steps')->onDelete('cascade');
            /**
             * ----------------
             */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_processes');
    }
};
