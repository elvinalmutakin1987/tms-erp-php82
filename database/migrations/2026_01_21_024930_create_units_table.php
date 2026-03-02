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
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('unit_brand_id')->nullable();
            $table->unsignedBigInteger('unit_model_id')->nullable();
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
            $table->string('status', 30)->nullable();
            $table->timestamps();
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('unit_brand_id')->references('id')->on('unit_brands')->onDelete('cascade');
            $table->foreign('unit_model_id')->references('id')->on('unit_models')->onDelete('cascade');
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
