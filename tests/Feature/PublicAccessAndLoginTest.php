<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PublicAccessAndLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_opens_public_tracking_without_login(): void
    {
        $this->get('/')->assertRedirect(route('tracking.public'));
    }

    public function test_driver_can_login_and_is_sent_to_driver_dashboard(): void
    {
        $role = Role::create(['name' => 'Pengemudi', 'slug' => 'driver']);
        $user = User::factory()->create(['email' => 'driver-login@example.com', 'password' => Hash::make('password')]);
        $user->roles()->attach($role);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('driver.dashboard'));
    }

    public function test_regular_user_cannot_login(): void
    {
        $role = Role::create(['name' => 'Pengguna', 'slug' => 'user']);
        $user = User::factory()->create(['email' => 'regular-login@example.com', 'password' => Hash::make('password')]);
        $user->roles()->attach($role);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertNull(session('user_id'));
    }
}
