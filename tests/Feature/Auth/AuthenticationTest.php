<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_mobile_number_and_pin(): void
    {
        $user = User::factory()->create([
            'mobile_number' => '0912345678',
            'password' => Hash::make('1234'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'mobile_number' => '0912345678',
            'pin' => '1234',
        ]);

        $this->assertAuthenticated();

        $response->assertRedirect(
            route('dashboard', absolute: false)
        );
    }

    public function test_users_can_not_authenticate_with_invalid_pin(): void
    {
        $user = User::factory()->create([
            'mobile_number' => '0912345678',
            'password' => Hash::make('1234'),
            'is_active' => true,
        ]);

        $this->post('/login', [
            'mobile_number' => $user->mobile_number,
            'pin' => '9999',
        ]);

        $this->assertGuest();
    }

    public function test_inactive_users_receive_account_inactive_message(): void
    {
        $user = User::factory()->create([
            'mobile_number' => '0912345678',
            'password' => Hash::make('1234'),
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'mobile_number' => '0912345678',
            'pin' => '1234',
        ]);

        $this->assertGuest();

        $response->assertSessionHasErrors([
            'mobile_number' => 'Account is inactive.',
        ]);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create([
            'mobile_number' => '0912345678',
            'password' => Hash::make('1234'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();

        $response->assertRedirect('/');
    }
}
