<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UnitbrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "request_token" => (string) Str::uuid(),
                "name" => "Toyota"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "name" => "Daihatsu"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "name" => "Mercedes"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "name" => "Carterpilar"
            ],
        ];
        DB::table('unit_brands')->insert($data);
    }
}
