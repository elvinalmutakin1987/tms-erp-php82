<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UnitmodelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "request_token" => (string) Str::uuid(),
                "unit_brand_id" => 1,
                "desc" => "Innova"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "unit_brand_id" => 1,
                "desc" => "Avanza"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "unit_brand_id" => 2,
                "desc" => "Triton"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "unit_brand_id" => 3,
                "desc" => "Actros"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "unit_brand_id" => 4,
                "desc" => "FVZ34T"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "unit_brand_id" => 4,
                "desc" => "NMR71T"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "unit_brand_id" => 4,
                "desc" => "FVZ34N"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "unit_brand_id" => 4,
                "desc" => "NKR71HD"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "unit_brand_id" => 4,
                "desc" => "FVR34L"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "unit_brand_id" => 5,
                "desc" => "Scania"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "unit_brand_id" => 6,
                "desc" => "Fuel Diesel 45.000"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "unit_brand_id" => 7,
                "desc" => "Mutiara Mahakam"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "unit_brand_id" => 8,
                "desc" => "330D2_SZK"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "unit_brand_id" => 8,
                "desc" => "330D2LME"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "unit_brand_id" => 8,
                "desc" => "D6R"
            ],
        ];
        DB::table('unit_models')->insert($data);
    }
}
