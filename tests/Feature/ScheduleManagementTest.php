<?php

namespace Tests\Feature;

use App\Models\TransportRoute;
use App\Models\TransportSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_create_a_schedule_for_a_route(): void
    {
        $route = TransportRoute::create([
            'code' => 'TR-020',
            'name' => 'Trayek Desa C - Desa D',
            'route_type' => 'one_way',
            'start_point' => 'Desa C',
            'end_point' => 'Desa D',
            'distance_km' => 18.5,
            'status' => 'active',
        ]);

        $schedule = TransportSchedule::create([
            'route_id' => $route->id,
            'schedule_code' => 'SCH-020',
            'day_of_week' => 'Monday',
            'departure_time' => '06:00:00',
            'arrival_time' => '07:00:00',
            'frequency_minutes' => 30,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'route_id' => $route->id,
            'schedule_code' => 'SCH-020',
            'status' => 'active',
        ]);
    }
}
