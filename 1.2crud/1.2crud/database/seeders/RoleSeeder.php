<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Administrador']
        );

        Role::firstOrCreate(
            ['slug' => 'empleado'],
            ['name' => 'Empleado']
        );

        User::firstOrCreate(
            ['email' => 'administrador@gmail.com'],
            [
                'name' => 'administrador',
                'password' => Hash::make('Admin1234!'),
                'role_id' => $adminRole->id,
            ]
        );
    }
}