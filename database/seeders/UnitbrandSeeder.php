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
                "request_token" => (string) Str::uuid(), //1
                "name" => "Toyota"
            ],
            [
                "request_token" => (string) Str::uuid(), //2
                "name" => "Mitsubishi"
            ],
            [
                "request_token" => (string) Str::uuid(), //3
                "name" => "Mercedes Benz"
            ],
            [
                "request_token" => (string) Str::uuid(), //4
                "name" => "Isuzu"
            ],
            [
                "request_token" => (string) Str::uuid(), //5
                "name" => "Scania"
            ],
            [
                "request_token" => (string) Str::uuid(), //6
                "name" => "Fuwa"
            ],
            [
                "request_token" => (string) Str::uuid(), //7
                "name" => "LCT Craft"
            ],
            [
                "request_token" => (string) Str::uuid(), //8
                "name" => "CAT"
            ],
        ];
        DB::table('unit_brands')->insert($data);
    }
}
