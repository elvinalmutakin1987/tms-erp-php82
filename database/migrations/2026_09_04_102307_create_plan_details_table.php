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
        Schema::create('plan_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token')->nullable();
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
            $table->string('periode')->nullable();
            $table->string('month')->nullable();
            $table->string('coa')->nullable();
            $table->string('description')->nullable();
            $table->decimal('value', 16, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_details');
    }
};
