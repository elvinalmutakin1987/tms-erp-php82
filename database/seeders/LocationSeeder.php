<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ["loc_type" => "Unit Location", "name" => "Sangatta"],
            ["loc_type" => "Unit Location", "name" => "Bengalon"],
            ["loc_type" => "Project Location", "name" => "Tanjung Bara"],
            ["loc_type" => "Project Location", "name" => "Lubuk Tutung"],
        ];
        DB::table('locations')->insert($data);
    }
}
