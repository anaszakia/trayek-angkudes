<?php

namespace Tests\Feature;

use App\Models\RoutePoint;
use App\Models\RouteStop;
use App\Models\TransportRoute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutePointStopManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_route_point_and_stop(): void
    {
        $route = TransportRoute::create([
            'code' => 'TR-POINT-001', 'name' => 'Trayek Titik', 'route_type' => 'one_way',
            'start_point' => 'A', 'end_point' => 'B', 'status' => 'active',
        ]);

        $point = RoutePoint::create([
            'route_id' => $route->id, 'sequence' => 1, 'name' => 'Jalur A',
            'latitude' => -6.75, 'longitude' => 111.03, 'is_terminal' => false,
        ]);
        $stop = RouteStop::create([
            'route_id' => $route->id, 'sequence' => 1, 'name' => 'Halte A',
            'latitude' => -6.76, 'longitude' => 111.04, 'is_active' => true,
        ]);

        $this->assertDatabaseHas('route_points', ['id' => $point->id, 'route_id' => $route->id]);
        $this->assertDatabaseHas('route_stops', ['id' => $stop->id, 'route_id' => $route->id]);
    }
}
