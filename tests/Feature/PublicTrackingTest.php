<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\GpsTracking;
use App\Models\TransportRoute;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_open_public_tracking_page(): void
    {
        $route = TransportRoute::create([
            'code' => 'TR-800',
            'name' => 'Trayek Publik',
            'route_type' => 'one_way',
            'start_point' => 'Desa A',
            'end_point' => 'Desa B',
            'distance_km' => 10,
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $driver = Driver::create([
            'user_id' => $user->id,
            'driver_code' => 'DRV-800',
            'nik' => '3320000000800',
            'phone' => '08120000800',
            'license_number' => 'SIM-800',
            'license_type' => 'C',
            'address' => 'Desa A',
            'status' => 'active',
        ]);

        $vehicle = Vehicle::create([
            'vehicle_code' => 'VHC-800',
            'plate_number' => 'AB-8000-ZZ',
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
            'trip_code' => 'TRIP-800',
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        GpsTracking::create([
            'trip_id' => $trip->id,
            'vehicle_id' => $vehicle->id,
            'latitude' => -7.8,
            'longitude' => 110.4,
            'speed_kmh' => 20,
            'recorded_at' => now(),
        ]);

        $response = $this->get('/tracking');

        $response->assertOk();
        $response->assertSee('Angkutan Desa');
        $response->assertSee('tracking/data');

        $dataResponse = $this->getJson('/tracking/data');
        $dataResponse->assertOk();
        $dataResponse->assertJsonPath('routes.0.code', 'TR-800');
        $dataResponse->assertJsonPath('vehicles.0.trip_code', 'TRIP-800');
    }
}
