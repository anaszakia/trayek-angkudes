<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\DriverVehicleAssignment;
use App\Models\Permission;
use App\Models\Role;
use App\Models\TransportRoute;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_open_dashboard_with_active_assignment(): void
    {
        $user = User::factory()->create(['name' => 'Driver Dashboard']);
        $role = Role::firstOrCreate(['slug' => 'driver'], ['name' => 'Driver']);
        $permission = Permission::firstOrCreate(['slug' => 'trips.view'], ['name' => 'Lihat Trip']);
        $role->permissions()->sync([$permission->id]);
        $user->roles()->sync([$role->id]);
        $this->withSession(['user_id' => $user->id]);

        $driver = Driver::create(['user_id' => $user->id, 'driver_code' => 'DRV-DASH', 'status' => 'active']);
        $vehicle = Vehicle::create(['vehicle_code' => 'VHC-DASH', 'plate_number' => 'AB-DASH', 'vehicle_type' => 'angkot', 'status' => 'active']);
        DriverVehicleAssignment::create(['driver_id' => $driver->id, 'vehicle_id' => $vehicle->id, 'started_at' => now(), 'status' => 'active']);

        $this->get(route('driver.dashboard'))
            ->assertOk()
            ->assertSee('Driver Dashboard')
            ->assertSee('AB-DASH');
    }

    public function test_driver_cannot_start_trip_without_active_assignment(): void
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['slug' => 'driver'], ['name' => 'Driver']);
        $permission = Permission::firstOrCreate(['slug' => 'trips.start'], ['name' => 'Mulai Trip']);
        $role->permissions()->sync([$permission->id]);
        $user->roles()->sync([$role->id]);
        $this->withSession(['user_id' => $user->id]);
        $driver = Driver::create(['user_id' => $user->id, 'driver_code' => 'DRV-NO-ASSIGN', 'status' => 'active']);
        $route = TransportRoute::create(['code' => 'TR-NO-ASSIGN', 'name' => 'Tanpa Penugasan', 'route_type' => 'one_way', 'start_point' => 'A', 'end_point' => 'B', 'status' => 'active']);

        $this->post(route('driver.trips.start'), ['route_id' => $route->id])
            ->assertSessionHasErrors('route_id');
        $this->assertDatabaseMissing('trips', ['driver_id' => $driver->id]);
    }

    public function test_driver_can_start_and_stop_own_trip(): void
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['slug' => 'driver'], ['name' => 'Driver']);
        foreach (['trips.view', 'trips.start', 'trips.stop', 'gps.update'] as $slug) {
            $role->permissions()->syncWithoutDetaching([Permission::firstOrCreate(['slug' => $slug], ['name' => $slug])->id]);
        }
        $user->roles()->sync([$role->id]);
        $this->withSession(['user_id' => $user->id]);

        $driver = Driver::create(['user_id' => $user->id, 'driver_code' => 'DRV-WF', 'status' => 'active']);
        $vehicle = Vehicle::create(['vehicle_code' => 'VHC-WF', 'plate_number' => 'AB-WF', 'vehicle_type' => 'angkot', 'status' => 'active']);
        DriverVehicleAssignment::create(['driver_id' => $driver->id, 'vehicle_id' => $vehicle->id, 'started_at' => now(), 'status' => 'active']);
        $route = TransportRoute::create(['code' => 'TR-WF', 'name' => 'Workflow', 'route_type' => 'one_way', 'start_point' => 'A', 'end_point' => 'B', 'status' => 'active']);

        $response = $this->post(route('driver.trips.start'), ['route_id' => $route->id]);
        $trip = Trip::first();
        $response->assertRedirect(route('driver.dashboard'));
        $this->assertSame('in_progress', $trip->status);
        $this->assertSame($driver->id, $trip->driver_id);
        $this->assertSame($vehicle->id, $trip->vehicle_id);

        $this->post(route('driver.trips.stop', $trip))->assertRedirect(route('driver.dashboard'));
        $this->assertDatabaseHas('trips', ['id' => $trip->id, 'status' => 'completed']);
    }

    public function test_driver_cannot_send_location_for_another_driver_trip(): void
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['slug' => 'driver'], ['name' => 'Driver']);
        $permission = Permission::firstOrCreate(['slug' => 'gps.update'], ['name' => 'Update GPS']);
        $role->permissions()->sync([$permission->id]);
        $user->roles()->sync([$role->id]);
        $this->withSession(['user_id' => $user->id]);
        $driver = Driver::create(['user_id' => $user->id, 'driver_code' => 'DRV-OWN', 'status' => 'active']);
        $route = TransportRoute::create(['code' => 'TR-OWN', 'name' => 'Ownership', 'route_type' => 'one_way', 'start_point' => 'A', 'end_point' => 'B', 'status' => 'active']);
        $otherUser = User::factory()->create();
        $other = Driver::create(['user_id' => $otherUser->id, 'driver_code' => 'DRV-OTHER', 'status' => 'active']);
        $trip = Trip::create(['route_id' => $route->id, 'driver_id' => $other->id, 'trip_code' => 'TRIP-OTHER', 'status' => 'in_progress', 'started_at' => now()]);

        $this->postJson(route('driver.trips.location', $trip), ['latitude' => -6, 'longitude' => 110, 'recorded_at' => now()->toIso8601String()])->assertForbidden();
        $this->assertDatabaseCount('gps_trackings', 0);
    }
}
