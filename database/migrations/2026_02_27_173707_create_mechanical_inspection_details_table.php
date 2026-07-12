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
        Schema::create('mechanical_inspection_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token')->nullable();
            $table->foreignId('mechanical_inspection_id')->nullable()->constrained('mechanical_inspections')->nullOnDelete();
            $table->text('inspection_group')->nullable();
            $table->text('inspection_item')->nullable();
            $table->string('check', 20)->nullable();
            $table->text('remarks')->nullable();
            $table->string('inspected_by', 20)->nullable();
            $table->string('sync_status', 2)->nullable();
            $table->timestamp('sync_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mechanical_inspection_details');
    }
};
