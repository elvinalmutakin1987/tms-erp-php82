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
        Schema::create('purchase_requisitions', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('maintenance_id')->nullable();
            $table->string('number', 30)->nullable();
            $table->string('requisition_no', 30)->nullable();
            $table->string('type', 30)->nullable(); //General / equipment
            $table->date('date')->nullable();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->text('status')->nullable(); //Status nya > Open, Close
            $table->decimal('total', 16, 2)->nullable();
            $table->decimal('discount', 16, 2)->nullable();
            $table->decimal('tax', 16, 2)->nullable();
            $table->decimal('grand_total', 16, 2)->nullable();
            $table->unsignedBigInteger('checked_by')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->string('sync_status', 2)->nullable();
            $table->timestamp('sync_at')->nullable();
            $table->string('input_method', 20)->nullable();
            $table->string('department', 30)->nullable();
            $table->string('urgency', 3)->nullable(); //P1, P2, P3, P4
            $table->string('cancel_notes')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('maintenance_id')->references('id')->on('maintenances')->onDelete('cascade');
            $table->foreign('checked_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requisitions');
    }
};
