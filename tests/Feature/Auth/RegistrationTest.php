<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Student',
            'email' => 'student.test@umak.edu.ph',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'terms' => true,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_cannot_register_with_non_umak_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'Invalid Domain User',
            'email' => 'student@gmail.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_users_cannot_register_without_accepting_terms(): void
    {
        $response = $this->post('/register', [
            'name' => 'No Terms User',
            'email' => 'noterms@umak.edu.ph',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'terms' => false,
        ]);

        $response->assertSessionHasErrors('terms');
        $this->assertGuest();
    }
}
