<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secreto123')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secreto123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secreto123')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'incorrecta',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_inactive_user_is_rejected_even_with_correct_password(): void
    {
        $user = User::factory()->inactivo()->create(['password' => bcrypt('secreto123')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secreto123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_guest_is_redirected_to_login_from_protected_pages(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
