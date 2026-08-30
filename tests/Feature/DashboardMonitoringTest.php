<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Permission;
use App\Models\Role;
use App\Models\TransportRoute;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_operational_summary_and_active_trip_status(): void
    {
        $role = Role::firstOrCreate(['slug' => 'superadmin'], ['name' => 'Super Admin']);
        $permission = Permission::firstOrCreate(['slug' => 'dashboard.view'], ['name' => 'Lihat Dashboard']);
        $role->permissions()->syncWithoutDetaching([$permission->id]);

        $user = User::factory()->create([
            'name' => 'Dashboard Admin',
            'email' => 'dashboard-admin@example.com',
        ]);
        $user->roles()->syncWithoutDetaching([$role->id]);
        $this->withSession(['user_id' => $user->id]);

        $route = TransportRoute::create([
            'code' => 'TR-900',
            'name' => 'Trayek Dashboard',
            'route_type' => 'one_way',
            'start_point' => 'Desa A',
            'end_point' => 'Desa B',
            'distance_km' => 12,
            'status' => 'active',
        ]);

        $driver = Driver::create([
            'user_id' => $user->id,
            'driver_code' => 'DRV-900',
            'nik' => '3320000000900',
            'phone' => '08120000900',
            'license_number' => 'SIM-900',
            'license_type' => 'C',
            'address' => 'Desa A',
            'status' => 'active',
        ]);

        $vehicle = Vehicle::create([
            'vehicle_code' => 'VHC-900',
            'plate_number' => 'AB-9000-ZZ',
            'vehicle_type' => 'angkot',
            'brand' => 'Toyota',
            'model' => 'Kijang',
            'capacity' => 12,
            'status' => 'active',
        ]);

        Trip::create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'trip_code' => 'TRIP-900',
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $response = $this->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Dashboard');
        $response->assertSee('Trip Aktif');
        $response->assertSee('1');
    }
}
