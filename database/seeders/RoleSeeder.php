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
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super_admin'],
            ['name' => 'Super Admin', 'slug' => 'superadmin'],
            ['name' => 'Driver', 'slug' => 'driver'],
            ['name' => 'User', 'slug' => 'user'],
        ];

        $roleMap = [];

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                ['name' => $roleData['name']]
            );

            $roleMap[$roleData['slug']] = $role;
        }

        $adminRole = $roleMap['super_admin'] ?? $roleMap['superadmin'];
        $driverRole = $roleMap['driver'];
        $userRole = $roleMap['user'];

        $adminUser = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
                'role_id' => $adminRole?->id,
            ]
        );

        $adminUser->update([
            'name' => 'Super Admin',
            'role_id' => $adminRole?->id,
        ]);

        $adminUser->roles()->syncWithoutDetaching([
            $adminRole?->id,
            $roleMap['superadmin']?->id,
        ]);

        $driverUser = User::firstOrCreate(
            ['email' => 'driver@trayek.test'],
            [
                'name' => 'Driver Default',
                'password' => Hash::make('12345678'),
                'role_id' => $driverRole?->id,
            ]
        );

        $driverUser->update([
            'name' => 'Driver Default',
            'role_id' => $driverRole?->id,
        ]);

        $driverUser->roles()->syncWithoutDetaching([$driverRole?->id]);

        $regularUser = User::firstOrCreate(
            ['email' => 'user@trayek.test'],
            [
                'name' => 'User Default',
                'password' => Hash::make('12345678'),
                'role_id' => $userRole?->id,
            ]
        );

        $regularUser->update([
            'name' => 'User Default',
            'role_id' => $userRole?->id,
        ]);

        $regularUser->roles()->syncWithoutDetaching([$userRole?->id]);
    }
}
