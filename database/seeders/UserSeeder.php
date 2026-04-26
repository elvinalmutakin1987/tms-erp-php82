<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Role superuser
         */
        Role::create(['name' => 'superadmin']);

        /**
         * User
         */
        Permission::create(['name' => 'user.read']);
        Permission::create(['name' => 'user.create']);
        Permission::create(['name' => 'user.update']);
        Permission::create(['name' => 'user.delete']);

        /**
         * Permission
         */
        Permission::create(['name' => 'permission.read']);
        Permission::create(['name' => 'permission.create']);
        Permission::create(['name' => 'permission.update']);
        Permission::create(['name' => 'permission.delete']);

        /**
         * Role
         */
        Permission::create(['name' => 'role.read']);
        Permission::create(['name' => 'role.create']);
        Permission::create(['name' => 'role.update']);
        Permission::create(['name' => 'role.delete']);


        $data = [
            "username" => "superadmin",
            "request_token" => (string) Str::uuid(),
            "name" => "Super Admin",
            "email" => "it.staff@tunasmitrasejati.com",
            "password" => bcrypt("Tmssgt2026"),
            "pass_mobile" => "Tmssgt2026",
            "email_verified_at" => now(),
            "remember_token" =>  Str::random(10)
        ];

        DB::table('users')->insert($data);
        $user = User::find(1);
        $permission = Permission::all();
        $role = Role::find(1);
        $user->givePermissionTo($permission);
        $user->assignRole($role);
        $role->givePermissionTo($permission);
    }
}
