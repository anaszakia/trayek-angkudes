<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\TransportRoute;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_detects_active_trip_conflict_for_same_driver_or_vehicle(): void
    {
        $route = TransportRoute::create([
            'code' => 'TR-200',
            'name' => 'Trayek Desa E - Desa F',
            'route_type' => 'one_way',
            'start_point' => 'Desa E',
            'end_point' => 'Desa F',
            'distance_km' => 15,
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $driver = Driver::create([
            'user_id' => $user->id,
            'driver_code' => 'DRV-200',
            'nik' => '3320000000001',
            'phone' => '081234567890',
            'license_number' => 'SIM-200',
            'license_type' => 'C',
            'address' => 'Desa E',
            'status' => 'active',
        ]);

        $vehicle = Vehicle::create([
            'vehicle_code' => 'VHC-200',
            'plate_number' => 'AB-2000-XY',
            'vehicle_type' => 'angkot',
            'brand' => 'Toyota',
            'model' => 'Kijang',
            'capacity' => 12,
            'status' => 'active',
        ]);

        $trip = Trip::create([
            'route_id' => $route->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'trip_code' => 'TRIP-200',
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $this->assertTrue(Trip::hasActiveTripForDriverOrVehicle($driver->id, $vehicle->id));
        $this->assertFalse(Trip::hasActiveTripForDriverOrVehicle($driver->id, $vehicle->id, $trip->id));
    }
}
