<?php

namespace Tests\Feature;

use App\Models\TransportRoute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_route(): void
    {
        $route = TransportRoute::create([
            'code' => 'TR-001',
            'name' => 'Trayek Desa A - Desa B',
            'route_type' => 'loop',
            'start_point' => 'Desa A',
            'end_point' => 'Desa B',
            'distance_km' => 12.5,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('routes', [
            'id' => $route->id,
            'code' => 'TR-001',
            'status' => 'active',
        ]);
    }
}
