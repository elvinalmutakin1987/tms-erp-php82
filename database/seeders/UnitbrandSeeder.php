<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitbrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ["name" => "Toyota"],
            ["name" => "Daihatsu"],
            ["name" => "Mercedes"],
            ["name" => "Carterpilar"],
        ];
        DB::table('unit_brands')->insert($data);
    }
}
