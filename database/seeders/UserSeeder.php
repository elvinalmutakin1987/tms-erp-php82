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
        Role::create(['name' => 'procurement_staff']);
        Role::create(['name' => 'procurement_manager']);
        Role::create(['name' => 'equipment_staff']);
        Role::create(['name' => 'operational_spv']);
        Role::create(['name' => 'maintenance_spv']);

        /**
         * User
         */
        // Permission::create(['name' => 'user.read']);
        // Permission::create(['name' => 'user.create']);
        // Permission::create(['name' => 'user.update']);
        // Permission::create(['name' => 'user.delete']);

        /**
         * Permission
         */
        // Permission::create(['name' => 'permission.read']);
        // Permission::create(['name' => 'permission.create']);
        // Permission::create(['name' => 'permission.update']);
        // Permission::create(['name' => 'permission.delete']);

        /**
         * Role
         */
        // Permission::create(['name' => 'role.read']);
        // Permission::create(['name' => 'role.create']);
        // Permission::create(['name' => 'role.update']);
        // Permission::create(['name' => 'role.delete']);

        $permissions = [
            'dashboard.operational',
            'dashboard.maintenance',
            'dashboard.procurement',
            'dashboard.survey',
            'dashboard.safety',
            'dashboard.finance',

            'p2h',
            'mechanical_inspection',
            'maintenance',
            'purchase_requisition',
            'proforma_invoice',

            'unit_expired',
            'purchase_requisition_general',

            'request_quotation',
            'purchase_order',

            'purchase_order_payment',

            'approval',

            'service',
            'contract',
            'unit',
            'unit_model',
            'unit_brand',
            'unit_rate',
            'location',
            'maintenance_item',
            'mro_item',
            'client_vendor',
        ];

        foreach ($permissions as $d) {
            Permission::firstOrCreate([
                'name' => $d,
                'guard_name' => 'web',
            ]);
        }

        $data = [
            [
                "username" => "superadmin",
                "request_token" => (string) Str::uuid(),
                "name" => "Super Admin",
                "email" => "admin@tunasmitrasejati.com",
                "password" => bcrypt("123456"),
                "pass_mobile" => "123456",
                "email_verified_at" => now(),
                "remember_token" =>  Str::random(10)
            ],
            [
                "username" => "andri",
                "request_token" => (string) Str::uuid(),
                "name" => "Procurement Staff",
                "email" => "procurement@tunasmitrasejati.com",
                "password" => bcrypt("123456"),
                "pass_mobile" => "123456",
                "email_verified_at" => now(),
                "remember_token" =>  Str::random(10)
            ],
            [
                "username" => "irsan",
                "request_token" => (string) Str::uuid(),
                "name" => "Procurement Manager",
                "email" => "irsan@tunasmitrasejati.com",
                "password" => bcrypt("123456"),
                "pass_mobile" => "123456",
                "email_verified_at" => now(),
                "remember_token" =>  Str::random(10)
            ],
            [
                "username" => "andy",
                "request_token" => (string) Str::uuid(),
                "name" => "Equipment Staff",
                "email" => "equipment.spv@tunasmitrasejati.com",
                "password" => bcrypt("123456"),
                "pass_mobile" => "123456",
                "email_verified_at" => now(),
                "remember_token" =>  Str::random(10)
            ],
            [
                "username" => "rio",
                "request_token" => (string) Str::uuid(),
                "name" => "Repair & Maintennance SPV",
                "email" => "equipment.admin@tunasmitrasejati.com",
                "password" => bcrypt("123456"),
                "pass_mobile" => "123456",
                "email_verified_at" => now(),
                "remember_token" =>  Str::random(10)
            ],
            [
                "username" => "hugo",
                "request_token" => (string) Str::uuid(),
                "name" => "Operasional SPV",
                "email" => "operasional.spv@tunasmitrasejati.com",
                "password" => bcrypt("123456"),
                "pass_mobile" => "123456",
                "email_verified_at" => now(),
                "remember_token" =>  Str::random(10)
            ]
        ];

        DB::table('users')->insert($data);

        $users = [
            [
                'user_id' => 1,
                'role_id' => 1,
                'permissions' => Permission::all(),
            ],
            [
                'user_id' => 2,
                'role_id' => 2,
                'permissions' => ['purchase_order', 'request_quotation'],
            ],
            [
                'user_id' => 3,
                'role_id' => 3,
                'permissions' => ['dashboard.procurement', 'approval'],
            ],
            [
                'user_id' => 4,
                'role_id' => 4,
                'permissions' => ['p2h', 'mechanical_inspection', 'maintenance', 'purchase_requisition', 'proforma_invoice'],
            ],
            [
                'user_id' => 5,
                'role_id' => 5,
                'permissions' => ['dashboard.operational', 'approval'],
            ],
            [
                'user_id' => 6,
                'role_id' => 6,
                'permissions' => ['dashboard.maintenance', 'approval'],
            ],
        ];

        foreach ($users as $item) {
            $user = User::find($item['user_id']);
            $role = Role::find($item['role_id']);

            $user->givePermissionTo($item['permissions']);
            $user->assignRole($role);
            $role->givePermissionTo($item['permissions']);
        }
    }
}
