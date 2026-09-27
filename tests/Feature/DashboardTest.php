<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_verified_users_can_view_dashboard(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'account_status' => User::STATUS_ACTIVE,
        ]);

        Event::create([
            'title' => 'Tech Summit 2026',
            'description' => 'Annual CCIS tech summit.',
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'venue' => 'UMak Grand Auditorium',
            'status' => Event::STATUS_PUBLISHED,
            'points_awarded' => 10,
            'is_featured' => true,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Tech Summit 2026');
        $response->assertSee('Upcoming Events');
    }

    public function test_unverified_user_can_skip_email_verification(): void
    {
        $user = User::factory()->unverified()->create([
            'account_status' => User::STATUS_ACTIVE,
        ]);

        $this->assertNull($user->email_verified_at);

        $response = $this->actingAs($user)->post('/verify-email/skip');

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_president_can_view_officer_dashboard_with_all_role_stats(): void
    {
        $president = User::factory()->create([
            'role'              => User::ROLE_PRESIDENT,
            'account_status'    => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($president)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('President');
    }
}
