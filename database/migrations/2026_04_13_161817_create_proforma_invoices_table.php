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
        Schema::create('proforma_invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token')->unique();
            $table->unsignedBigInteger('client_vendor_id')->nullable();
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('generate_no', 30)->nullable();
            $table->string('proforma_no', 30)->nullable();
            $table->date('date')->nullable();
            $table->string('periode')->nullable();
            $table->date('periode_start')->nullable();
            $table->date('periode_finish')->nullable();
            $table->decimal('target', 16, 2)->nullable(); //Target PA
            $table->decimal('price', 16, 2)->nullable(); //Rate
            $table->integer('act_work_day')->nullable();
            $table->integer('act_work_hour')->nullable();
            $table->decimal('breakdown', 16, 2)->nullable();
            $table->decimal('pa', 16, 2)->nullable();
            $table->decimal('penalty', 16, 2)->nullable();
            $table->decimal('total', 16, 2)->nullable();
            $table->decimal('km_awal', 16, 2)->nullable();
            $table->decimal('km_akhir', 16, 2)->nullable();
            $table->timestamp('approval_at')->nullable();
            $table->timestamp('user_approval_at')->nullable();
            $table->timestamp('custodian_approval_at')->nullable();
            //------------------------------------------------------------
            $table->text('status')->nullable(); //Status nya > Draft, Approval, Open, User Approval, Custodian Approval, Revision, Done, Cancel
            $table->unsignedBigInteger('checked_by')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->string('sync_status', 2)->nullable();
            $table->timestamp('sync_at')->nullable();
            $table->timestamps();
            $table->foreign('client_vendor_id')->references('id')->on('client_vendors')->onDelete('cascade');
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proforma_invoices');
    }
};
