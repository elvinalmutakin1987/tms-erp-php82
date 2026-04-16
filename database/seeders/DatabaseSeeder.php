<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            UserSeeder::class,
            UnitbrandSeeder::class,
            LocationSeeder::class,
            UnitmodelSeeder::class,
            UnitSeeder::class,
            MaintenanceItemSeeder::class,
            MroItemSeeder::class,
            ClientSeeder::class,
            VendorSeeder::class
        ]);
    }
}
