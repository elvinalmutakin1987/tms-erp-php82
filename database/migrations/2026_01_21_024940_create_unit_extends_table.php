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
        Schema::create('unit_extends', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token')->unique();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('ext_type')->nullable(); //ngambil dari type unit
            $table->date('submit_date')->nullable();
            $table->date('done_date')->nullable();
            $table->string('status', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_extends');
    }
};
