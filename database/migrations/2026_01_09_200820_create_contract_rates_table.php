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
        Schema::create('contract_rates', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token');
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->unsignedBigInteger('service_item_id')->nullable();
            $table->string('item_no')->nullable();
            $table->string('service_item')->nullable();
            $table->decimal('rate', 16, 2)->nullable();
            $table->timestamps();
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
            $table->foreign('service_item_id')->references('id')->on('service_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_rates');
    }
};
