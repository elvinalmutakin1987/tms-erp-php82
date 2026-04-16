<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Client_vendor;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            "location_id" => 1,
            "type" => "Client",
            "name" => "PT. Kaltim Prima Coal",
            "pic" => "-",
            "address" => "-",
            "email" => "-",
            "top" => 30
        ];
        DB::table('client_vendors')->insert($data);
    }
}
