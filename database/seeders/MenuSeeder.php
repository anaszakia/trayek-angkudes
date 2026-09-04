<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::where('slug', 'superadmin')->first();
        $driver = Role::where('slug', 'driver')->first();

        $dashboard = $this->menu([
            'name'  => 'Dashboard',
            'url'   => '/dashboard',
            'icon'  => 'ti ti-layout-dashboard',
            'order' => 1,
        ]);

        $driverDashboard = $this->menu([
            'name'  => 'Dashboard Driver',
            'url'   => '/driver/dashboard',
            'icon'  => 'ti ti-steering-wheel',
            'order' => 1,
        ]);

        $menuManagement = $this->menu([
            'name'  => 'Menu Management',
            'url'   => null,
            'icon'  => 'ti ti-menu-deep',
            'order' => 2,
        ]);

        $masterData = $this->menu([
            'name'  => 'Master Data',
            'url'   => null,
            'icon'  => 'ti ti-database',
            'order' => 3,
        ]);

        $children = [
            [
                'name'      => 'User',
                'url'       => '/users',
                'icon'      => 'ti ti-users',
                'parent_id' => $menuManagement->id,
                'order'     => 1,
            ],
            [
                'name'      => 'Roles',
                'url'       => '/roles',
                'icon'      => 'ti ti-shield',
                'parent_id' => $menuManagement->id,
                'order'     => 2,
            ],
            [
                'name'      => 'Permission',
                'url'       => '/permissions',
                'icon'      => 'ti ti-key',
                'parent_id' => $menuManagement->id,
                'order'     => 3,
            ],
            [
                'name'      => 'Menus',
                'url'       => '/menus',
                'icon'      => 'ti ti-menu-2',
                'parent_id' => $menuManagement->id,
                'order'     => 4,
            ],
            [
                'name'      => 'Driver',
                'url'       => '/drivers',
                'icon'      => 'ti ti-user-check',
                'parent_id' => $masterData->id,
                'order'     => 1,
            ],
            [
                'name'      => 'Vehicle',
                'url'       => '/vehicles',
                'icon'      => 'ti ti-bus',
                'parent_id' => $masterData->id,
                'order'     => 2,
            ],
            [
                'name'      => 'Assignment',
                'url'       => '/assignments',
                'icon'      => 'ti ti-clipboard-check',
                'parent_id' => $masterData->id,
                'order'     => 3,
            ],
            [
                'name'      => 'Trayek',
                'url'       => '/routes',
                'icon'      => 'ti ti-route',
                'parent_id' => $masterData->id,
                'order'     => 4,
            ],
            [
                'name'      => 'Titik Trayek',
                'url'       => '/route-points',
                'icon'      => 'ti ti-map-pin',
                'parent_id' => $masterData->id,
                'order'     => 5,
            ],
            [
                'name'      => 'Halte',
                'url'       => '/route-stops',
                'icon'      => 'ti ti-bus-stop',
                'parent_id' => $masterData->id,
                'order'     => 6,
            ],
            [
                'name'      => 'Tarif',
                'url'       => '/fares',
                'icon'      => 'ti ti-currency-dollar',
                'parent_id' => $masterData->id,
                'order'     => 7,
            ],
            [
                'name'      => 'Jadwal',
                'url'       => '/schedules',
                'icon'      => 'ti ti-calendar-time',
                'parent_id' => $masterData->id,
                'order'     => 8,
            ],
            [
                'name'      => 'Trip',
                'url'       => '/trips',
                'icon'      => 'ti ti-map-2',
                'parent_id' => $masterData->id,
                'order'     => 9,
            ],
            [
                'name'      => 'GPS',
                'url'       => '/gps',
                'icon'      => 'ti ti-location',
                'parent_id' => $masterData->id,
                'order'     => 10,
            ],
        ];

        $menus = collect([$dashboard, $menuManagement, $masterData]);

        foreach ($children as $child) {
            $menus->push($this->menu($child));
        }

        if ($admin) {
            foreach ($menus as $menu) {
                $menu->roles()->syncWithoutDetaching([$admin->id]);
            }
        }

        if ($driver) {
            $driverDashboard->roles()->syncWithoutDetaching([$driver->id]);
        }
    }

    private function menu(array $data): Menu
    {
        $lookup = $data['url'] === null
            ? ['name' => $data['name'], 'url' => null]
            : ['url' => $data['url']];

        return Menu::updateOrCreate(
            $lookup,
            [
                'name'      => $data['name'],
                'icon'      => $data['icon'],
                'parent_id' => $data['parent_id'] ?? null,
                'order'     => $data['order'],
                'is_active' => true,
            ]
        );
    }
}
