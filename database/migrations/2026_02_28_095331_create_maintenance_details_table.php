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
        Schema::create('maintenance_details', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token')->nullable();
            $table->foreignId('maintenance_id')->nullable()->constrained('maintenances')->nullOnDelete();
            $table->foreignId('maintenance_item_id')->nullable()->constrained('maintenance_items')->nullOnDelete();
            $table->foreignId('mro_item_id')->nullable()->constrained('mro_items')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('action', 30)->nullable(); //Repair / Replace / Washing / Add / Flushing
            $table->string('sync_status', 2)->nullable();
            $table->decimal('cost', 16, 2)->nullable();
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
        Schema::dropIfExists('maintenance_details');
    }
};
