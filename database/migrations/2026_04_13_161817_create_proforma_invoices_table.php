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
            $table->uuid('request_token')->nullable();
            /**
             * Ini yang lama
             */
            // $table->unsignedBigInteger('client_vendor_id')->nullable();
            // $table->unsignedBigInteger('contract_id')->nullable();
            // $table->unsignedBigInteger('unit_id')->nullable();
            // $table->unsignedBigInteger('checked_by')->nullable();
            /**
             * ----------------
             */
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('client_vendor_id')->nullable()->constrained('client_vendors')->nullOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained('contracts')->nullOnDelete();
            $table->foreignId('contract_rate_id')->nullable()->constrained('contract_rates')->nullOnDelete();
            $table->foreignId('contract_fmf_id')->nullable()->constrained('contract_fmfs')->nullOnDelete();
            $table->foreignId('unit_target_id')->nullable()->constrained('unit_targets')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            /**
             * ----------------
             */
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
            $table->string('type')->nullable();
            $table->string('input_method', 20)->nullable();
            $table->string('cancel_notes')->nullable();
            $table->date('cut_off_date')->nullable(); //Tanggal Cut off
            $table->date('consolidation_date')->nullable(); //Konsolidasi Data TMS & CMD
            $table->date('progress_claim_date')->nullable(); //Kirim Progress Klaim Approval
            $table->date('ops_received_date')->nullable(); //Data diterima dr ops
            $table->date('prof_inv_app_date')->nullable(); //Proforma Inv approved
            $table->date('cic_request_date')->nullable(); //Minta cic
            $table->date('cic_created_date')->nullable(); //Pembuatan CIC
            $table->date('inv_date')->nullable(); //Terima CIC
            $table->date('inv_create_date')->nullable(); //Tgl INV
            $table->date('cic_send_date')->nullable(); //Kirim CIC ke KPC
            $table->date('cic_ready_to_pick_date')->nullable(); //Informasi CIC bisa diambil
            $table->date('cic_pick_up_date')->nullable(); //CIC diambil TMS
            $table->date('inv_send_date')->nullable(); //Inv Terima CIC
            //------------------------------------------------------------
            $table->text('status')->nullable(); //Status nya > Draft, Approval, Open, User Approval, Custodian Approval, Revision, Done, Cancel
            $table->timestamp('checked_at')->nullable();
            $table->string('sync_status', 2)->nullable();
            $table->timestamp('sync_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            /**
             * Ini yang lama
             */
            // $table->foreign('client_vendor_id')->references('id')->on('client_vendors')->onDelete('cascade');
            // $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
            // $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            /*/ $table->foreign('checked_by')->references('id')->on('users')->onDelete('cascade');
             * ----------------
             */
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
