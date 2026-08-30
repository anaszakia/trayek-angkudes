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

class GpsTrackingManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateUserWithGpsPermission(): User
    {
        $role = Role::firstOrCreate(
            ['slug' => 'superadmin'],
            ['name' => 'Super Admin']
        );

        foreach (['gps.view', 'gps.update'] as $slug) {
            $permission = Permission::firstOrCreate(['slug' => $slug], ['name' => ucfirst(str_replace('.', ' ', $slug))]);
            $role->permissions()->syncWithoutDetaching([$permission->id]);
        }

        $user = User::factory()->create([
            'email' => 'gpsadmin@example.com',
            'name' => 'GPS Admin',
        ]);

        $user->roles()->syncWithoutDetaching([$role->id]);
        $this->withSession(['user_id' => $user->id]);

        return $user;
    }

    public function test_it_rejects_gps_record_for_non_active_trip(): void
    {
        $this->authenticateUserWithGpsPermission();

        $route = TransportRoute::create([
            'code' => 'TR-300',
            'name' => 'Trayek Desa G - Desa H',
            'route_type' => 'one_way',
            'start_point' => 'Desa G',
            'end_point' => 'Desa H',
            'distance_km' => 20,
            'status' => 'active',
        ]);

        $user = User::query()->first();

        $driver = Driver::create([
            'user_id' => $user->id,
            'driver_code' => 'DRV-300',
            'nik' => '3320000000003',
            'phone' => '081200000003',
            'license_number' => 'SIM-300',
            'license_type' => 'C',
            'address' => 'Desa G',
            'status' => 'active',
        ]);

        $vehicle = Vehicle::create([
            'vehicle_code' => 'VHC-300',
            'plate_number' => 'AB-3000-XY',
            'vehicle_type' => 'angkot',
            'brand' => 'Honda',
            'model' => 'Scoopy',
            'capacity' => 8,
            'status' => 'active',
        ]);

        $trip = Trip::create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'trip_code' => 'TRIP-300',
            'status' => 'completed',
            'started_at' => now()->subHours(2),
            'ended_at' => now()->subHour(),
        ]);

        $response = $this->post('/gps', [
            'trip_id' => $trip->id,
            'vehicle_id' => $vehicle->id,
            'latitude' => -7.123456,
            'longitude' => 110.123456,
            'speed_kmh' => 30,
            'recorded_at' => now()->toDateTimeString(),
        ]);

        $response->assertSessionHasErrors(['trip_id']);
        $this->assertDatabaseMissing('gps_trackings', [
            'trip_id' => $trip->id,
        ]);
    }

    public function test_it_can_store_gps_for_active_trip(): void
    {
        $this->authenticateUserWithGpsPermission();

        $route = TransportRoute::create([
            'code' => 'TR-301',
            'name' => 'Trayek Desa I - Desa J',
            'route_type' => 'one_way',
            'start_point' => 'Desa I',
            'end_point' => 'Desa J',
            'distance_km' => 25,
            'status' => 'active',
        ]);

        $user = User::query()->first();

        $driver = Driver::create([
            'user_id' => $user->id,
            'driver_code' => 'DRV-301',
            'nik' => '3320000000004',
            'phone' => '081200000004',
            'license_number' => 'SIM-301',
            'license_type' => 'C',
            'address' => 'Desa I',
            'status' => 'active',
        ]);

        $vehicle = Vehicle::create([
            'vehicle_code' => 'VHC-301',
            'plate_number' => 'AB-3010-XY',
            'vehicle_type' => 'angkot',
            'brand' => 'Toyota',
            'model' => 'Avanza',
            'capacity' => 10,
            'status' => 'active',
        ]);

        $trip = Trip::create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'trip_code' => 'TRIP-301',
            'status' => 'in_progress',
            'started_at' => now()->subMinutes(5),
        ]);

        $response = $this->post('/gps', [
            'trip_id' => $trip->id,
            'vehicle_id' => $vehicle->id,
            'latitude' => -7.456789,
            'longitude' => 110.456789,
            'speed_kmh' => 35,
            'recorded_at' => now()->toDateTimeString(),
        ]);

        $response->assertRedirect(route('gps.index'));
        $this->assertDatabaseHas('gps_trackings', [
            'trip_id' => $trip->id,
            'vehicle_id' => $vehicle->id,
        ]);
    }
}
