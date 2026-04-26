<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "request_token" => (string) Str::uuid(),
                "loc_type" => "Unit Location",
                "name" => "Sangatta"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "loc_type" => "Unit Location",
                "name" => "Bengalon"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "loc_type" => "Project Location",
                "name" => "Tanjung Bara"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "loc_type" => "Project Location",
                "name" => "Lubuk Tutung"
            ],
        ];
        DB::table('locations')->insert($data);
    }
}
