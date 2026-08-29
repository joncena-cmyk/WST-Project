<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'email' => 'student.auth@umak.edu.ph',
            'password' => 'password',
            'account_status' => User::STATUS_ACTIVE,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'student.wrong@umak.edu.ph',
            'password' => 'password',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_login_is_locked_after_three_invalid_password_attempts(): void
    {
        $user = User::factory()->create([
            'email' => 'student.lockout@umak.edu.ph',
            'password' => 'password',
        ]);

        foreach (range(1, 3) as $attempt) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $response->assertSessionHas('login_locked_seconds');
    }

    public function test_login_lock_expires_after_one_minute(): void
    {
        $user = User::factory()->create([
            'email' => 'student.lockout-expiry@umak.edu.ph',
            'password' => 'password',
        ]);

        foreach (range(1, 3) as $attempt) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $this->travel(61)->seconds();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_suspended_users_cannot_authenticate(): void
    {
        $user = User::factory()->create([
            'email' => 'suspended.user@umak.edu.ph',
            'password' => 'password',
            'account_status' => User::STATUS_SUSPENDED,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHas('account_error');
    }

    public function test_inactive_users_cannot_authenticate(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive.user@umak.edu.ph',
            'password' => 'password',
            'account_status' => User::STATUS_INACTIVE,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHas('account_error');
    }

    public function test_non_umak_email_rejected_at_login(): void
    {
        $response = $this->post('/login', [
            'email' => 'external@gmail.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'logout.test@umak.edu.ph',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
