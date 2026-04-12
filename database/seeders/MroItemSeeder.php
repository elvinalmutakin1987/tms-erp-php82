<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MroItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ["name" => "Tread"],
            ["name" => "Sidewall"],
            ["name" => "Bead"],
            ["name" => "Carcass"],
            ["name" => "Breaker"],
            ["name" => "Inner Liner"],
        ];
        DB::table('mro_items')->insert($data);
    }
}
