<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            "location_id" => 1,
            "company_id" => null,
            "unit_brand_id" => 1,
            "unit_model_id" => 1,
            "vehicle_no" => "TMS01",
            "registration_no" => "KT1123RR",
            "type" => "Vehicle"
        ];
        DB::table('units')->insert($data);
    }
}
