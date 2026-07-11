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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_token')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('unit_brand_id')->nullable()->constrained('unit_brands')->nullOnDelete();
            $table->foreignId('unit_model_id')->nullable()->constrained('unit_models')->nullOnDelete();
            $table->string('type', 50)->nullable(); // ini dibuatkan data dari array aja. ntar di form model dropdown
            //Excavator, Hauler, Dozer, Compactor, Light Vehicle, Water Truck, 
            //Manhaul, Lighting Plant, Light Truck, Crane Truck, Prime Truck, Fuel Truck, Light Gas, LCT
            $table->string('brand', 50)->nullable(); //ini juga pake data array aja. ntar di form model dropdown
            $table->string('model', 100)->nullable(); //ini text bebas
            $table->string('vehicle_no', 10)->nullable();
            $table->string('code_access', 10)->nullable();
            $table->string('plr_no', 10)->nullable();
            $table->string('banlaw_no', 10)->nullable();
            $table->date('buy_date')->nullable();
            $table->string('mechine_no', 50)->nullable();
            $table->string('chassis_no', 50)->nullable();
            $table->string('certificate_no', 50)->nullable(); //BPKB
            $table->string('registration_no', 50)->nullable(); //STNK
            $table->date('exp_crane')->nullable();
            $table->date('exp_fuel_issue')->nullable();
            $table->date('exp_tbst')->nullable();
            $table->date('exp_pass_road_1')->nullable();
            $table->date('exp_stnk')->nullable();
            $table->date('exp_tax')->nullable();
            $table->date('exp_comm')->nullable(); //Commissioning
            $table->string('description')->nullable();
            $table->string('status', 30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
