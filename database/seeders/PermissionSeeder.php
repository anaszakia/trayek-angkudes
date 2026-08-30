<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'Lihat Dashboard', 'slug' => 'dashboard.view'],

            ['name' => 'Lihat User', 'slug' => 'users.view'],
            ['name' => 'Buat User', 'slug' => 'users.create'],
            ['name' => 'Edit User', 'slug' => 'users.edit'],
            ['name' => 'Hapus User', 'slug' => 'users.delete'],

            ['name' => 'Lihat Role', 'slug' => 'roles.view'],
            ['name' => 'Buat Role', 'slug' => 'roles.create'],
            ['name' => 'Edit Role', 'slug' => 'roles.edit'],
            ['name' => 'Hapus Role', 'slug' => 'roles.delete'],

            ['name' => 'Lihat Permission', 'slug' => 'permissions.view'],
            ['name' => 'Buat Permission', 'slug' => 'permissions.create'],
            ['name' => 'Edit Permission', 'slug' => 'permissions.edit'],
            ['name' => 'Hapus Permission', 'slug' => 'permissions.delete'],

            ['name' => 'Lihat Menu', 'slug' => 'menus.view'],
            ['name' => 'Buat Menu', 'slug' => 'menus.create'],
            ['name' => 'Edit Menu', 'slug' => 'menus.edit'],
            ['name' => 'Hapus Menu', 'slug' => 'menus.delete'],

            ['name' => 'Lihat Driver', 'slug' => 'drivers.view'],
            ['name' => 'Buat Driver', 'slug' => 'drivers.create'],
            ['name' => 'Edit Driver', 'slug' => 'drivers.edit'],
            ['name' => 'Hapus Driver', 'slug' => 'drivers.delete'],

            ['name' => 'Lihat Kendaraan', 'slug' => 'vehicles.view'],
            ['name' => 'Buat Kendaraan', 'slug' => 'vehicles.create'],
            ['name' => 'Edit Kendaraan', 'slug' => 'vehicles.edit'],
            ['name' => 'Hapus Kendaraan', 'slug' => 'vehicles.delete'],

            ['name' => 'Lihat Trayek', 'slug' => 'routes.view'],
            ['name' => 'Buat Trayek', 'slug' => 'routes.create'],
            ['name' => 'Edit Trayek', 'slug' => 'routes.edit'],
            ['name' => 'Hapus Trayek', 'slug' => 'routes.delete'],

            ['name' => 'Lihat Titik Trayek', 'slug' => 'route_points.view'],
            ['name' => 'Buat Titik Trayek', 'slug' => 'route_points.create'],
            ['name' => 'Edit Titik Trayek', 'slug' => 'route_points.edit'],
            ['name' => 'Hapus Titik Trayek', 'slug' => 'route_points.delete'],

            ['name' => 'Lihat Halte', 'slug' => 'route_stops.view'],
            ['name' => 'Buat Halte', 'slug' => 'route_stops.create'],
            ['name' => 'Edit Halte', 'slug' => 'route_stops.edit'],
            ['name' => 'Hapus Halte', 'slug' => 'route_stops.delete'],

            ['name' => 'Lihat Tarif', 'slug' => 'fares.view'],
            ['name' => 'Buat Tarif', 'slug' => 'fares.create'],
            ['name' => 'Edit Tarif', 'slug' => 'fares.edit'],
            ['name' => 'Hapus Tarif', 'slug' => 'fares.delete'],

            ['name' => 'Lihat Jadwal', 'slug' => 'schedules.view'],
            ['name' => 'Buat Jadwal', 'slug' => 'schedules.create'],
            ['name' => 'Edit Jadwal', 'slug' => 'schedules.edit'],
            ['name' => 'Hapus Jadwal', 'slug' => 'schedules.delete'],

            ['name' => 'Lihat Trip', 'slug' => 'trips.view'],
            ['name' => 'Mulai Trip', 'slug' => 'trips.start'],
            ['name' => 'Stop Trip', 'slug' => 'trips.stop'],
            ['name' => 'Histori Trip', 'slug' => 'trips.history'],

            ['name' => 'Update GPS', 'slug' => 'gps.update'],
            ['name' => 'Lihat GPS', 'slug' => 'gps.view'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        $superAdminRole = Role::whereIn('slug', ['superadmin', 'super_admin'])->first();
        $driverRole = Role::where('slug', 'driver')->first();
        $userRole = Role::where('slug', 'user')->first();

        if ($superAdminRole) {
            $superAdminRole->permissions()->sync(Permission::pluck('id')->all());
        }

        if ($driverRole) {
            $driverPermissions = Permission::whereIn('slug', [
                'dashboard.view',
                'drivers.view',
                'vehicles.view',
                'routes.view',
                'trips.view',
                'trips.start',
                'trips.stop',
                'trips.history',
                'gps.view',
                'gps.update',
            ])->pluck('id');

            $driverRole->permissions()->sync($driverPermissions);
        }

        if ($userRole) {
            $userPermissions = Permission::whereIn('slug', [
                'dashboard.view',
                'routes.view',
                'fares.view',
                'schedules.view',
                'vehicles.view',
            ])->pluck('id');

            $userRole->permissions()->sync($userPermissions);
        }
    }
}
