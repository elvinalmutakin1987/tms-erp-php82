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
        Schema::create('client_vendors', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('code', 30)->nullable(); // kode unik untuk vendor
            $table->string('type', 30)->nullable(); // ini isinya Client / Vendor
            $table->string('taxable', 30)->nullable(); // PKP / Non PKP
            $table->string('name', 100)->nullable();
            $table->string('pic', 100)->nullable();
            $table->string('applied_by', 100)->nullable(); // PKP / Non PKP
            $table->text('address')->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 100)->nullable();
            $table->integer('top')->nullable(); //Term of Payment
            $table->string('bank', 50)->nullable();
            $table->string('bank_account', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_vendors');
    }
};
