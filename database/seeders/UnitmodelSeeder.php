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
        ];
        DB::table('unit_models')->insert($data);
    }
}
