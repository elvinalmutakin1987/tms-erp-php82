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
        Schema::create('p2hs', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token')->nullable();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('number', 30)->nullable();
            $table->string('p2h_no', 30)->nullable();
            $table->date('date')->nullable();
            $table->string('driver', 30)->nullable();
            $table->string('shift', 20)->nullable();
            $table->decimal('km_start', 16, 2)->nullable();
            $table->decimal('km_finish', 16, 2)->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->string('status', 30)->nullable();
            $table->string('input_method', 20)->nullable();
            $table->string('sync_status', 2)->nullable();
            $table->timestamp('sync_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p2h');
    }
};
