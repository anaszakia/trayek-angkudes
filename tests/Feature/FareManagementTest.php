<?php

namespace Tests\Feature;

use App\Models\Fare;
use App\Models\TransportRoute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FareManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_fare_for_a_route(): void
    {
        $route = TransportRoute::create([
            'code' => 'TR-010',
            'name' => 'Trayek Desa A - Desa B',
            'route_type' => 'loop',
            'start_point' => 'Desa A',
            'end_point' => 'Desa B',
            'distance_km' => 12.5,
            'status' => 'active',
        ]);

        $fare = Fare::create([
            'route_id' => $route->id,
            'fare_code' => 'FA-010',
            'name' => 'Umum',
            'passenger_type' => 'general',
            'amount' => 3500,
            'currency' => 'IDR',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('fares', [
            'id' => $fare->id,
            'route_id' => $route->id,
            'fare_code' => 'FA-010',
            'amount' => '3500.00',
        ]);
    }
}
