<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoringMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_open_live_map_page_for_admin(): void
    {
        $role = Role::firstOrCreate(['slug' => 'superadmin'], ['name' => 'Super Admin']);
        $permission = Permission::firstOrCreate(['slug' => 'gps.view'], ['name' => 'Lihat GPS']);
        $role->permissions()->syncWithoutDetaching([$permission->id]);

        $user = User::factory()->create([
            'name' => 'Map Admin',
            'email' => 'map-admin@example.com',
        ]);
        $user->roles()->syncWithoutDetaching([$role->id]);

        $this->withSession(['user_id' => $user->id]);

        $response = $this->get('/gps/map');

        $response->assertOk();
        $response->assertSee('Live Map');
    }
}
