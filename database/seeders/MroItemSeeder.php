<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MroItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "type" => "Good",
                "request_token" => (string) Str::uuid(),
                "name" => "Tread"
            ],
            [
                "type" => "Good",
                "request_token" => (string) Str::uuid(),
                "name" => "Sidewall"
            ],
            [
                "type" => "Good",
                "request_token" => (string) Str::uuid(),
                "name" => "Bead"
            ],
            [
                "type" => "Good",
                "request_token" => (string) Str::uuid(),
                "name" => "Carcass"
            ],
            [
                "type" => "Good",
                "request_token" => (string) Str::uuid(),
                "name" => "Breaker"
            ],
            [
                "type" => "Good",
                "request_token" => (string) Str::uuid(),
                "name" => "Inner Liner"
            ],
            [
                "type" => "Service",
                "request_token" => (string) Str::uuid(),
                "name" => "Jasa Service"
            ],
        ];
        DB::table('mro_items')->insert($data);
    }
}
