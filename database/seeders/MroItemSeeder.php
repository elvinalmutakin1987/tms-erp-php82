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
                "request_token" => (string) Str::uuid(),
                "name" => "Tread"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "name" => "Sidewall"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "name" => "Bead"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "name" => "Carcass"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "name" => "Breaker"
            ],
            [
                "request_token" => (string) Str::uuid(),
                "name" => "Inner Liner"
            ],
        ];
        DB::table('mro_items')->insert($data);
    }
}
